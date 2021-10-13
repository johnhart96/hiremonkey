<?php
require 'inc/usrPath.php';
// Check local DB file
session_start();
if( ! isset( $_SESSION['company'] ) ) {
    header( "Location:static/company_select.php" );
}
$dbpath = usrPath . "/" . $_SESSION['company'];
if( ! file_exists( $dbpath ) ) {
    session_destroy();
    header( "Location:static/company_select.php" );
} else {
    try {
        $db = new PDO( "sqlite:" . $dbpath );
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch( Exception $e ) {
        die( "Unable to open local database file: " . $e->getMessage() );
    }
}
// Check for a remote database connection
$checkForRemotes = $db->query( "SELECT * FROM `remote` ORDER BY `id` ASC LIMIT 1" );
$fetch = $checkForRemotes->fetch( PDO::FETCH_ASSOC );
if( $fetch ) {
    // Remotes found
    echo "<script>console.log('Starting remote database connection');</script>";
    $host = $fetch['host'];
    $username = $fetch['username'];
    $password = $fetch['password'];
    $dbname = $fetch['dbname'];
    unset( $db );
    try{
        $db = new PDO( "mysql:host=" . $host . ";dbname=" . $dbname , $username , $password );
    } catch( PDOException $Exception ) {
        header( "Location: static/error_remote.php" );
    }
} else {
    echo "<script>console.log('Remaining with local database');</script>";
}

require_once 'inc/version.php';

// Licence control
require_once 'inc/jhactivation.php';
if( date( "Y-m-d" , strtotime( licence( "nextactivation" ) ) ) < date( "Y-m-d" ) or empty( licence( "nextactivation" ) )  && ! trial() ) {
    echo "<script>console.log('Starting activation');</script>";
    $activation = jhl_activate( licence( "licencekey" ) , LICENCEVERSION , $_SESSION['uuid'] );
    $activationSuccessful = FALSE;
    if( isset( $activation->status ) ) {
        if( $activation->status == 200 ) {
            // Successful
            $activationSuccessful = TRUE;
            $nextActivation = filter_var( $activation->nextActivation , FILTER_SANITIZE_STRING );
            $purchaseDate = filter_var( $activation->purchaseDate , FILTER_SANITIZE_STRING );
            $licenceTo = filter_var( $activation->customer , FILTER_SANITIZE_STRING );
            $updateNextActivation = $db->prepare("
                UPDATE `licence` SET
                    `nextactivation` =:nextActivation,
                    `lastactivation` =:lastActivation,
                    `purchasedate` =:purchaseDate,
                    `licenceto` =:licenceto

                WHERE `id` > 0
            ");
            $updateNextActivation->execute( [ ':nextActivation' => $nextActivation , ':lastActivation' => date( "Y-m-d H:i" ) , ':purchaseDate' => $purchaseDate , ':licenceto' => $licenceTo ] );
            echo "<script>console.log('Successful activation');</script>";
        } else {
            jhl_licenceerror();
            $licenceError = true;
            echo "<script>console.log('Error with activation');</script>";
        }
    } else {
        jhl_licenceerror();
        $licenceError = true;
        echo "<script>console.log('Error with activation');</script>";
    }
}
?>

<link href="../node_modules/bootstrap/dist/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="css/sidebars.css" rel="stylesheet" type="text/css" />
<link href="css/jquery-ui.css" rel="stylesheet" />
<link href="css/job.css" rel="stylesheet" />

<script>
    window.$ = window.jQuery = require('jQuery');
</script>
<script src="./js/jquery-ui.js" type="text/javascript"></script>
<script src="./js/job.js" type="text/javascript"></script>
