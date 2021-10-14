<?php
require '../inc/usrPath.php';

// Add version details
require_once '../inc/version.php';

// Start session
session_start();
if ( PLATFORM == "windows" ) {
    $uuid = shell_exec( "wmic path win32_computersystemproduct get uuid" );
} else if( PLATFORM == "macos" ) {
    $command = shell_exec( "ioreg -l | grep IOPlatformSerialNumber" );
    $uuid = str_replace( '"IOPlatformSerialNumber" = "' , "" , $command );
    $uuid = str_replace( "| " , "" , $uuid );
    $uuid = str_replace( '"' , "" , $uuid );
    $uuid = str_replace( ' ' , "" , $uuid );
} 
$_SESSION['uuid'] = md5( $uuid ) ;

// Submit open
if( isset( $_POST['submitOpen'] ) ) {
    $company = filter_var( $_POST['company'] , FILTER_SANITIZE_STRING );
    // Try the file
    $test = new PDO( 'sqlite:' . usrPath . "/" . $company );
    $getVersion = $test->query( "SELECT `appversion`,`name` FROM `company` ORDER BY `id` DESC LIMIT 1" );
    $fetch = $getVersion->fetch( PDO::FETCH_ASSOC );
    $companyName = $fetch['name'];
    $companyVersion = $fetch['appversion'];

    /*****                      Does an upgrade need to be done?                            *****/
    if( $companyVersion !== FULLBUILD ) {
        // Upgrade required
        $upgrade = array();
        // Loop
        array_push( $tables , 'licence' );
        array_push( $tables , 'remote' );
        foreach( $tables as $table ) {
            $upgrade[$table] = array();
            $get = $test->query( "SELECT * FROM `$table`" );
            while( $row = $get->fetch( PDO::FETCH_ASSOC ) ) {
                $upgrade[$table][] = $row;
            }
        }
        // Create a fresh file with blank data
        $newFile = usrPath . "/" . str_replace( " " , "-" , $companyName ) . "_" . str_replace( "." , "_" , FULLBUILD ) . ".db";
        $blank = fopen( "../inc/default_database.sql" , "r" );
        $blank_db = fread( $blank , filesize( "../inc/default_database.sql" ) );
        fclose( $blank );
        $newDB = new SQLite3( $newFile );
        $newDB->query( $blank_db );
        
        // Insert data to new file
        $newDB = new PDO( 'sqlite:' . $newFile );

        $categories = $newDB->prepare( "INSERT INTO `categories` (`id`,`name`) VALUES(:id,:cat)" );
        foreach( $upgrade['categories'] as $cat ) {
            $categories->execute( [ ':id' => $cat['id'] , ':cat' => $cat['name'] ] );
        }

        $company = $newDB->prepare("
            INSERT INTO `company` (`id`,`name`,`address_line1`,`address_line2`,`town`,`postcode`,`telephone`,`website`,`email`,`currencysymbol`,`lastbackup`,`appversion`,`welcome`)
            VALUES(:id,:company,:address_line1,:address_line2,:town,:postcode,:telephone,:website,:email,:currencysymbol,:lastbackup,:appversion,:welcome)
        ");
        require '../inc/version.php';
        foreach( $upgrade['company'] as $com ) {
            if( isset( $com['welcome'] ) ) {
                $welcome = $com['welcome'];
            } else {
                $welcome = 1;
            }
            $company->execute([
                ':id' => $com['id'],
                ':company' => $com['name'],
                ':address_line1' => $com['address_line1'],
                ':address_line2' => $com['address_line2'],
                ':town' => $com['town'],
                ':postcode' => $com['postcode'],
                ':telephone' => $com['telephone'],
                ':website' => $com['website'],
                ':email' => $com['email'],
                ':currencysymbol' => $com['currencysymbol'],
                ':lastbackup' => $com['lastbackup'],
                ':appversion' => FULLBUILD,
                ':welcome' => $welcome
            ]);
        } 

        $customers = $newDB->prepare("
            INSERT INTO `customers` (`id`,`name`,`creationdate`,`notes`,`company_number`,`vat_number`,`invoice_terms`,`hold`,`website`)
            VALUES(:id,:customer,:creationdate,:notes,:company_number,:vat_number,:invoice_terms,:hold,:website)
        ");
        foreach( $upgrade['customers'] as $cus ) {
            $customers->execute([
                ':id' => $cus['id'],
                ':customer' => $cus['name'],
                ':creationdate' => $cus['creationdate'],
                ':notes' => $cus['notes'],
                ':company_number' => $cus['company_number'],
                ':vat_number' => $cus['vat_number'],
                ':invoice_terms' => $cus['invoice_terms'],
                ':hold' => $cus['hold'],
                ':website' => $cus['website']
            ]);
        }

        $customers_addresses = $newDB->prepare("
            INSERT INTO `customers_addresses` (`id`,`customer`,`line1`,`line2`,`town`,`postcode`)
            VALUES(:id,:customers_addresses,:line1,:line2,:town,:postcode)
        ");
        foreach( $upgrade['customers_addresses'] as $address ) {
            $customers_addresses->execute([
                ':id' => $address['id'],
                ':customer' => $address['customer'],
                ':line1' => $address['line1'],
                ':line2' => $address['line2'],
                ':town' => $address['town'],
                ':postcode' => $address['postcode']
            ]);
        }

        $customers_contacts = $newDB->prepare("
            INSERT INTO `customers_contacts` (`id`,`customer`,`name`,`email`,`telephone`)
            VALUES(:id,:customers,:customers_contact,:email,:telephone)
        ");
        foreach( $upgrade['customers_contacts'] as $contact ) {
            $customers_contacts->execute([
                ':id' => $contact['id'],
                ':customer' => $contact['customer'],
                ':name' => $contact['customer_contact'],
                ':email' => $contact['email'],
                ':telephone' => $contact['telephone']
            ]);
        }

        $jobs = $newDB->prepare("
            INSERT INTO `jobs` (`id`,`name`,`customer`,`address`,`contact`,`startdate`,`enddate`,`jobType`,`quoteAgreed`,`lost`,`complete`,`invoiced`)
            VALUES(:id,:job,:customer,:address,:contact,:startdate,:enddate,:jobType,:quoteAgreed,:lost,:complete,:invoiced)
        ");
        foreach( $upgrade['jobs'] as $j ) {
            $jobs->execute([
                ':id' => $j['id'],
                ':job' => $j['name'],
                ':customer' => $j['customer'],
                ':address' => $j['address'],
                ':contact' => $j['contact'],
                ':startdate' => $j['startdate'],
                ':enddate' => $j['enddate'],
                ':jobType' => $j['jobType'],
                ':quoteAgreed' => $j['quoteAgreed'],
                ':lost' => $j['lost'],
                ':complete' => $j['complete'],
                ':invoiced' => $j['invoiced']
            ]);
        }

        $jobs_cat = $newDB->prepare("
            INSERT INTO `jobs_cat` (`id`,`job`,`cat`) VALUES(:id,:job,:cat)
        ");
        foreach( $upgrade['jobs_cat'] as $cat ) {
            $jobs_cat->execute([
                ':id' => $cat['id'],
                ':job' => $cat['job'],
                ':cat' => $cat['cat']
            ]);
        }

        $jobs_lines = $newDB->prepare("
            INSERT INTO `jobs_lines` (`id`,`job`,`linetype`,`stockEntry`,`stockEffect`,`price`,`cat`,`qty`,`itemName`,`parent`,`kit`,`cost`,`notes`,`dispatch`,`dispatch_date`,`return`,`return_date`)
            VALUES(:id,:job,:linetype,:stockEntry,:stockEffect,:price,:cat,:qty,:itemName,:parent,:kit,:cost,:notes,:dispatch,:dispatch_date,:retrun,:return_date)
        ");
        foreach( $upgrade['jobs_lines'] as $line ) {
            $job_lines->execute([
                ':id' => $line['id'],
                ':job' => $line['job'],
                ':linetype' => $line['linetype'],
                ':stockEntry' => $line['stockEntry'],
                ':stockEffect' => $line['stockEffect'],
                ':price' => $line['price'],
                ':cat' => $line['cat'],
                ':qty' => $line['qty'],
                ':itemName' => $line['itemName'],
                ':parent' => $line['parent'],
                ':kit' => $line['kit'],
                ':cost' => $line['cost'],
                ':notes' => $line['notes'],
                ':dispatch' => $line['dispatch'],
                ':dispatch_date' => $line['dispatch_date'],
                ':return' => $line['return'],
                ':return_date' => $line['return_date']
            ]);
        }

        $kit = $newDB->prepare("
            INSERT INTO `kit` (`id`,`name`,`purchasevalue`,`sloc`,`price`,`height`,`width`,`length`,`weight`,`notes`,`active`,`toplevel`,`cat`)
            VALUES(:id,:kit,:purchasevalue,:sloc,:price,:height,:width,:length,:weight,:notes,:active,:toplevel,:cat)
        ");
        foreach( $upgrade['kit'] as $k ) {
            $kit->execute([
                ':id' => $k['id'],
                ':kit' => $k['name'],
                ':purchasevalue' => $k['purchasevalue'],
                ':sloc' => $k['sloc'],
                ':price' => $k['price'],
                ':height' => $k['height'],
                ':width' => $k['width'],
                ':length' => $k['length'],
                ':weight' => $k['weight'],
                ':notes' => $k['notes'],
                ':active' => $k['active'],
                ':toplevel' => $k['toplevel'],
                ':cat' => $k['cat']
            ]);
        }

        $kit_accessories = $newDB->prepare("
            INSERT INTO `kit_accessories` (`id`,`accessory`,`type`,`price`,`kit`,`qty`) VALUES(:id,:accessory,:type,:price,:kit,:qty)
        ");
        foreach( $upgrade['kit_accessories'] as $accessory ) {
            $kit_accessories->execute([
                ':id' => $accessory['id'],
                ':accessory' => $accessory['accessory'],
                ':type' => $accessory['type'],
                ':price' => $accessory['price'],
                ':kit' => $accessory['kit'],
                ':qty' => $accessory['qty']
            ]);
        }

        $kit_stock = $newDB->prepare("
            INSERT INTO `kit_stock` (`id`,`kit`,`stock_count`,`serialized`,`serialnumber`,`purchasedate`)
            VALUES(:id,:kit,:stock_count,:serialized,:serialnumber,:purchasedate)
        ");
        foreach( $upgrade['kit_stock'] as $stock ) {
            $kit_stock->execute([
                ':id' => $stock['id'],
                ':kit' => $stock['kit'],
                ':stock_count' => $stock['stock_count'],
                ':serialized' => $stock['serialized'],
                ':serialnumber' => $stock['serialnumber'],
                ':purchasedate' => $stock['purchasedate']
            ]);
        }

        $licence = $newDB->prepare("
            INSERT INTO `licence` (`id`,`licencekey`) VALUES(:id,:licencekey)
        ");
        foreach( $upgrade['licence'] as $lic ) {
            $licence->execute([
                ':id' => $lic['id'],
                ':licencekey' => $lic['licencekey']
            ]);
        }

        $remote = $newDB->prepare("
            INSERT INTO `remote` (`id`,`host`,`username`,`password`,`dbname`)
            VALUES(:id,:host,:username,:password,:dbname)
        ");
        foreach( $upgrade['remote'] as $r ) {
            $remote->execute([
                ':id' => $r['id'],
                ':host' => $r['host'],
                ':username' => $r['username'],
                ':password' => $r['password'],
                ':dbname' => $r['dbname']
            ]);
        }

        $sloc = $newDB->prepare("
            INSERT INTO `sloc` (`id`,`name`,`address_line1`,`address_line2`,`town`,`postcode`)
            VALUES(:id,:sloc,:address_line1,:address_line2,:town,:postcode)
        ");
        foreach( $upgrade['sloc'] as $s ) {
            $sloc->execute([
                ':id' => $s['id'],
                ':sloc' => $s['name'],
                ':address_line1' => $s['address_line1'],
                ':address_line2' => $s['address_line2'],
                ':town' => $s['town'],
                ':postcode' => $s['postcode']
            ]);
        }
        unlink( usrPath . "/" . filter_var( $_POST['company'] , FILTER_SANITIZE_STRING ) );
        $_SESSION['company'] = str_replace( usrPath . "/" , "" , $newFile );
    } else {
        $_SESSION['company'] = filter_var( $_POST['company'] , FILTER_SANITIZE_STRING );
    }
    header( "Location:../index.php" );
}
?>
<html>
    <head>
        <title>Welcome to HireMonkey</title>
        <link href="../node_modules/bootstrap/dist/css/bootstrap.css" rel="stylesheet" type="text/css" />
        <style>
            .card-body {
                height: 200px;
            }
        </style>
    </head>
    <body>
        <div class="container">&nbsp;</div>
        <div class="container">
            <div class="row">
                <div class="col">
                    <center>
                        <img src="../icons/logo.png" height="200"> <br />
                        <h1>HireMonkey</h1>
                    </center>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="alert alert-info">Please select what company you want to open, restore a backup or create a new one.</div>
                </div>
            </div>     
            <div class="row">&nbsp;</div>    
            <div class="row">
                <div class="col">
                    <form method="post">
                        <div class="card">
                            <div class="card-header"><strong>Open:</strong></div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="company" class="form-label">Company:</label>
                                    <select id="company" name="company" class="form-select" size="3" aria-label="Please select a company">
                                        <?php
                                        $files = scandir( usrPath );
                                        foreach( $files as $file ) {
                                            if( $file !== "." && $file !== ".." ) {
                                                $bang = explode( "." , $file );
                                                if( $bang[1] == "db" ) {
                                                    echo "<option value='$file'>";
                                                    // Load the file to check it
                                                    $test = new PDO( 'sqlite:' . usrPath . "/" . $file );
                                                    $getCompanyName = $test->query( "SELECT `name`,`appversion` FROM `company` ORDER BY `id` DESC LIMIT 1" );
                                                    $fetch = $getCompanyName->fetch( PDO::FETCH_ASSOC );
                                                    echo $fetch['name'] . " (v" . $fetch['appversion'] . ")";
                                                    unset( $test );
                                                    echo "</option>";
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" name="submitOpen" class="btn btn-success">Open</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col">
                    <form method="post" enctype="multipart/form-data" action="restore.php">
                        <div class="card">
                            <div class="card-header"><strong>Restore:</strong></div>
                            <div class="card-body">
                                <div class="input-group">
                                    <input type="file" name="restore" class="form-control">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" name="submitRestore" class="btn btn-success">Restore</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header"><strong>New:</strong></div>
                        <div class="card-body">
                            <p>Create a fresh, blank company.</p>
                        </div>
                        <div class="card-footer">
                            <a href="welcome.php" class="btn btn-success">New Company</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>