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
 * @copyright  2025 John Hart
 * @license    https://www.hiremonkey.app/licence.php
 */
require '../inc/usrPath.php';
require '../inc/version.php';

if( isset( $_POST['submit'] ) ) {
    // Load the blank database file
    $file = usrPath . "/" . date( "YmdHi" ) . ".db";
    $db = new PDO( "sqlite:" . $file );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Load default SQL
    $default = fopen( "../inc/default_database.sql" , "r" );
    $sql = fread( $default , filesize( "../inc/default_database.sql" ) );
    fclose( $default );
    $newDB = new SQLite3( $file );
    $newDB->query( $sql );

    $name = filter_var( $_POST['name'] , FILTER_UNSAFE_RAW );
    $email = filter_var( $_POST['email'] , FILTER_VALIDATE_EMAIL );
    $licencekey = NULL;

    $insertCompany = $db->prepare( "INSERT INTO `company` (`name`,`lastbackup`,`email`,`appversion`) VALUES(:comName,:lastbackup,:email,:appversion)" );
    $insertCompany->execute( [ ':comName' => $name , ':lastbackup' => date( "Y-m-d" ) , ':email' => $email , ':appversion' => FULLBUILD ] );

    if( ! empty( $licencekey ) ) {
        $addLicence = $db->prepare( "INSERT INTO `licence` (`licencekey`) VALUES(:licKey)" );
        $addLicence->execute( [ ':licKey' => $licencekey ] );
        require_once '../inc/version.php';
        require '../inc/jhactivation.php';
        $activation = jhl_activate( $licencekey , LICENCEVERSION );
        if( $activation->status !== 200 ) {
            // Activation failed
            $removeKey = $db->query( "DELETE FROM `licence` WHERE `id` > 0" );
        } else {
            // Activation fine!
            $addActivation = $db->prepare( "UPDATE `licence` SET `licenceto` =:licenceto , `purchasedate` =:purchasedate , `lastactivation` =:lastactivation , `nextactivation` =:nextactivation WHERE `id` > 0" );
            $addActivation->execute([
                ':licenceto' => filter_var( $activation->customer , FILTER_UNSAFE_RAW ),
                ':purchasedate' => filter_var( $activation->purchaseDate , FILTER_UNSAFE_RAW ),
                ':lastactivation' => filter_var( $activation->activationDate , FILTER_UNSAFE_RAW ),
                ':nextactivation' => filter_var( $activation->nextActivation , FILTER_UNSAFE_RAW )
            ]);
        }
    }
    session_start();
    $_SESSION['company'] = str_replace( usrPath . "/" , "" , $file );
    header( "Location:../index.php" );
}
?>
<html>
    <head>
        <title>Welcome to HireMonkey</title>
        <link href="../node_modules/bootstrap/dist/css/bootstrap.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <div class="container">&nbsp;</div>
        <div class="container">
            <div class="row">
                <div class="col">
                    <center>
                        <img src="../icons/logo.png" height="200"> <br />
                        <h1>Welcome to HireMonkey</h1>
                    </center>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="alert alert-info">Looks like your new here, please tell me a little about yourself to get started!</div>
                </div>
            </div>     
            <div class="row">&nbsp;</div>    
            <div class="row">
                <div class="col">
                    <form method="post">
                        <div class="card">
                            <div class="card-header"><strong>Setup wizard:</strong></div>
                            <div class="card-body">
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class='input-group-text'>Company Name:</span></div>
                                    <input class="form-control" name="name">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class='input-group-text'>Email:</span></div>
                                    <input class="form-control" name="email">
                                </div>
                            </div>
                            <div class="card-footer">
                                <center>
                                    By continueing, you are agreeing to the <a href='../LICENSE.txt' download>end-user agreement!</a> <br />
                                    <button type="submit" name="submit" class="btn btn-success">Yet's get started!</button>
                                </center>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>