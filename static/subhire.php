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

// Get Suppliers
$getLines = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `supplier` !=0 AND `job` =:jobID" );
$getLines->execute( [ ':jobID' => $id ] );
$suppliers = array();
while( $line = $getLines->fetch( PDO::FETCH_ASSOC ) ) {
    array_push( $suppliers , $line['supplier'] );
}
$suppliers = array_unique( $suppliers );
?>
<html>
    <head>
        <style>
            @media print {
                .container { page-break-after: always; }
            }
        </style>
        <title><?php echo $job['name']; ?> - Purchase Order</title>
        <link href="../../node_modules/bootstrap/dist/css/bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="../css/documents.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php
        $pocount = 1;
        foreach( $suppliers as $supplier ) {
           require 'subhire_individual.php'; 
           $pocount ++;
        }
        ?>
    </body>
</html>