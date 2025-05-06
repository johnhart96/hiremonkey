<?php
/**
 * company_select.php
 *
 * This source code is subject to copyright.
 * Viewing, distributing, editing or extracting this source code will result in licence violation and/or legal action
 *
 * 
 * @package    HireMonkey
 * @author     John Hart
 * @copyright  2021 John Hart
 * @license    https://www.hiremonkey.app/licence.php
 */
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
if( isset( $_POST['company'] ) ) {
    $company = filter_var( $_POST['company'] , FILTER_UNSAFE_RAW );
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
        
        require '../inc/restore_map.php';
        
        unlink( usrPath . "/" . filter_var( $_POST['company'] , FILTER_UNSAFE_RAW ) );
        $_SESSION['company'] = str_replace( usrPath . "/" , "" , $newFile );
    } else {
        $_SESSION['company'] = filter_var( $_POST['company'] , FILTER_UNSAFE_RAW );
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
            body {
                background-color: #5f5f5f;
            }
            h1 {
                color: #FFFFFF;
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
                    <form method="post" id="companySelect" action="company_select.php">
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
        <script>
            document.getElementById("company").addEventListener("dblclick", function() {
                document.getElementById('companySelect').submit();
            });
        </script>
    </body>
</html>