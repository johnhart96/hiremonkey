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
        <title><?php echo $job['name']; ?> - Delivery Note</title>
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
                                    <h1>Delivery Note</h1>
                                    <p>
                                        <?php echo company( 'website' ); ?><br />
                                        <?php echo company( 'email' ); ?><br />
                                        <?php echo company( 'telephone' ); ?>
                                    </p>
                                </td>
                                <td style="text-align: right">
                                    <?php
                                    if( ! empty( company( 'logo' ) ) && ! trial() ) {
                                        echo "<img src='" . company( 'logo' ) . "'>";
                                    }
                                    ?>
                                    <h1><?php echo company( 'name' ); ?></h1>
                                    <p>
                                        <?php
                                        if( ! empty( company( 'address_line1' ) ) ) {
                                            echo company( 'address_line1' ) . "<br />";
                                        }
                                        if( ! empty( company( 'address_line2' ) ) ) {
                                            echo company( 'address_line2' ) . "<br />";
                                        }
                                        if( ! empty( company( 'town' ) ) ) {
                                            echo company( 'town' ) . "<br />";
                                        }
                                        if( ! empty( company( 'postcode' ) ) ) {
                                            echo company( 'postcode' ) . "<br />";
                                        }
                                        ?>
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
                    <div class="box" style="height: 250px; !important;">
                        <p>
                            <strong>Customer:</strong> <?php echo customer( $job['customer'] ); ?> <br />
                            <strong>Shipping Address:</strong><br />
                            <?php
                            $getAddress = $db->prepare( "SELECT * FROM `customers_addresses` WHERE `id` =:id LIMIT 1" );
                            $getAddress->execute( [ ':id' => $job['address'] ] );
                            $fetch = $getAddress->fetch( PDO::FETCH_ASSOC );
                            if( ! empty( $fetch['line1'] ) ) {
                                echo $fetch['line1'] . "<br />";
                            }
                            if( ! empty( $fetch['line2'] ) ) {
                                echo $fetch['line2'] . "<br />";
                            }
                            if( ! empty( $fetch['town'] ) ) {
                                echo $fetch['town'] . "<br />";
                            }
                            if( ! empty( $fetch['postcode'] ) ) {
                                echo $fetch['postcode'] . "<br />";
                            }
                            ?>
                        </p>
                    </div>
                </div> 
                <div class="col">
                    <div class="box" style="height: 250px !important;">
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
                    $startDate = strtotime( $job['startdate'] );
                    $endDate = strtotime( $job['enddate'] );
                    $diff = $endDate - $startDate;
                    $years = floor( $diff / (365*60*60*24) );
                    $months = floor( ( $diff - $years * 365*60*60*24 ) / ( 30*60*60*24 ) ); 
                    $days = floor( ( $diff - $years * 365*60*60*24 - $months*30*60*60*24 ) / ( 60*60*24 ) );
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
                            // Weight
                            echo "<td>";
                            $getWeight = $db->prepare( "SELECT `weight` FROM `kit` WHERE `id` =:kitID LIMIT 1" );
                            $getWeight->execute( [ ':kitID' => $item['kit'] ] );
                            $fetch = $getWeight->fetch( PDO::FETCH_ASSOC );
                            if( ! empty( $fetch['weight'] ) ) {
                                echo (double)$fetch['weight'];
                            } else {
                                echo 0;
                            }
                            echo " KG</td>";
                            // Dispatch date
                            echo "<td>" . date( "d/m/Y" , strtotime( $item['dispatch_date'] ) ) . "</td>";
                            echo "</tr>";
                            getLines( $item['id'] , $cat );
                        }
                    }
                    $getJobCats = $db->prepare( "SELECT * FROM `jobs_cat` WHERE `job` =:job" );
                    $getJobCats->execute( [ ':job' => $id ] );
                    while( $cat = $getJobCats->fetch( PDO::FETCH_ASSOC ) ) {
                        $_SESSION['catTotal'] = 0.0;
                        echo "<p><strong>" . $cat['cat'] . ":</strong></p>";
                        echo "<table class='table table-bordered table-striped'>";
                        echo "
                            <thead>
                                <tr>
                                    <th width='70%'>Item</th>
                                    <th width='10%'>Qty</th>
                                    <th width='10%'>Weight</th>
                                    <th width='10%'>Dispatch date</th>
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
                        <p><em>I confirm that I have received the equipment listed above in good working order and I agree to the terms and conditions.</em></p>
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th width="50%">Signed:</th>
                                <td><br /><br /></td>
                            </tr>
                            <tr>
                                <th width="50%">Print:</th>
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
            <div class="row">&nbsp;</div>
            <div class="row">
                <div class="col">
                    <center>
                        <?php
                        if( trial() ) {
                            echo "Generated using a trial of HireMonkey";
                        }
                        ?>
                    </center>
                <div>
            </div>
        </div>
    </body>
</html>