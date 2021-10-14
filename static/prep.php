<?php
// INIT
require '../inc/version.php';
require '../inc/usrPath.php';
require '../inc/functions.php';
session_start();
$file = usrPath . "/" . $_SESSION['company'];
$db = new PDO( 'sqlite:' . $file );
if( ! isset( $_GET['id'] ) ) {
    die( "No ID was passed!" );
}
$id = filter_var( $_GET['id'] , FILTER_SANITIZE_NUMBER_INT );
$getJob = $db->prepare( "SELECT * FROM `jobs` WHERE `id` =:id" );
$getJob->execute( [ ':id' => $id ] );
$job = $getJob->fetch( PDO::FETCH_ASSOC );
?>
<html>
    <head>
        <title><?php echo $job['name']; ?> - Picking List</title>
        <link href="../../node_modules/bootstrap/dist/css/bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="../css/documents.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="box">
                        <table class="table">
                            <tr>
                                <td>
                                    <h1>Picking List</h1>
                                    <p>
                                        <?php echo company( 'website' ); ?><br />
                                        <?php echo company( 'email' ); ?><br />
                                        <?php echo company( 'telephone' ); ?>
                                    </p>
                                </td>
                                <td style="text-align: right">
                                    <h1><?php echo company( 'name' ); ?></h1>
                                    <p>
                                        <?php echo company( 'address_line1' ); ?>,<br />
                                        <?php echo company( 'address_line2' ); ?>,<br />
                                        <?php echo company( 'town' ); ?>,<br />
                                        <?php echo company( 'postcode' ); ?><br />
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="line"><hr /></div>
            <div class="row">
                <div class="col">
                    <div class="box" style="height: 300px; !important;">
                        <p>
                            <strong>Shipping Address:</strong><br />
                            <?php
                            $getAddress = $db->prepare( "SELECT * FROM `customers_addresses` WHERE `id` =:id LIMIT 1" );
                            $getAddress->execute( [ ':id' => $job['address'] ] );
                            $fetch = $getAddress->fetch( PDO::FETCH_ASSOC );
                            echo $fetch['line1'] . ",<br />";
                            echo $fetch['line2'] . ",<br />";
                            echo $fetch['town'] . ",<br />";
                            echo $fetch['postcode'] . "";
                            ?>
                        </p>
                    </div>
                </div> 
                <div class="col">
                    <div class="box" height="200">
                        <p>
                            <strong>Job Details:</strong><br />
                        </p>
                        <table class='table table-bordered'>
                            <tr>
                                <th width='30%'>Job#:</th>
                                <td><?php echo $job['id']; ?></td>
                            </tr>
                            <tr>
                                <th width='30%'>Job name:</th>
                                <td><?php echo $job['name']; ?></td>
                            </tr>
                            <tr>
                                <th width='30%'>Contact:</th>
                                <td>
                                    <?php
                                    $getContact = $db->prepare( "SELECT * FROM `customers_contacts` WHERE `id` =:id LIMIT 1" );
                                    $getContact->execute( [ ':id' => $job['contact'] ] );
                                    $contact = $getContact->fetch( PDO::FETCH_ASSOC );
                                    echo $contact['name'];
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th width='30%'>Date:</th>
                                <td><?php echo date( "d/m/Y" ) ?></td>
                            </tr>
                            <tr>
                                <th width='30%'>Start date:</th>
                                <td><?php echo date( "d/m/Y" , strtotime( $job['startdate'] ) ); ?></td>
                            </tr>
                            <tr>
                                <th width='30%'>End date:</th>
                                <td><?php echo date( "d/m/Y" , strtotime( $job['enddate'] ) ); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>              
            </div>
            <div class="line"><hr /></div>
            <div class="row">
                <div class="col">
                    <?php
                    function getLines( $parent = 0 , $cat = NULL ) {
                        global $db;
                        global $id;
                        global $days;
                        if( $parent == 0  ) {
                            $getItems = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `job` =:job AND `parent` =:parent AND `cat` =:cat AND `dispatch` =1" );
                            $getItems->execute( [ ':job' => $id , ':parent' => $parent , ':cat' => $cat ] );
                        } else {
                            $getItems = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `job` =:job AND `parent` =:parent AND `dispatch` =1" );
                            $getItems->execute( [ ':job' => $id , ':parent' => $parent ] );
                        }
                        while( $item = $getItems->fetch( PDO::FETCH_ASSOC ) ) {
                            echo "<tr>";
                            if( $parent == 0 ) {
                                echo "<td><strong>" . $item['itemName'] . "</strong></td>";
                            } else {
                                echo "<td>" . $item['itemName'] . "</td>";
                            }
                            echo "<td>" . $item['qty'] . "</td>";
                            // SLOC
                            echo "<td>";
                            $getSloc = $db->prepare( "SELECT `sloc` FROM `kit` WHERE `id` =:kitID " );
                            $getSloc->execute( [ ':kitID' => $item['kit']  ] );
                            $fetch = $getSloc->fetch( PDO::FETCH_ASSOC );
                            echo sloc( $fetch['sloc'] );
                            echo "</td>";
                            // Dispatch date
                            echo "<td>&nbsp;</td>";
                            echo "</tr>";
                            getLines( $item['id'] , $cat );
                        }
                    }
                    $getJobCats = $db->prepare( "SELECT * FROM `jobs_cat` WHERE `job` =:job" );
                    $getJobCats->execute( [ ':job' => $id ] );
                    while( $cat = $getJobCats->fetch( PDO::FETCH_ASSOC ) ) {
                        $_SESSION['catTotal'] = 0.0;
                        echo "<p><strong>" . $cat['cat'] . ":</strong></p>";
                        echo "<table class='table table-bordered table-stripe'>";
                        echo "
                            <thead>
                                <tr>
                                    <th width='70%'>Item</th>
                                    <th width='10%'>Qty</th>
                                    <th width='10%'>Location</th>
                                    <th width='10%'></th>
                                </tr>
                            </thead>
                        ";
                        echo "<tbody>";
                        getLines( 0 , $cat['id'] );
                        echo "</tbody>";
                        echo "</table>";
                    }
                    ?>
                </div>
            </div>
            <div class="line"><hr /></div>
            <div class="row">
                <div class="col">
                    <div class="box">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th width="50%">Prepped By:</th>
                                <td><br /><br /></td>
                            </tr>
                            <tr>
                                <th width="50%">Date:</th>
                                <td><br /><br /></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col">&nbsp;</div>
            </div>
        </div>
    </body>
</html>