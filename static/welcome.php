<?php
// OS detection
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    define( "PLATFORM" , "windows" );
} else if( strtoupper( PHP_OS ) == "DARWIN" ) {
    define( "PLATFORM" , "macos" );
} else {
    die( "Unknown OS!" );
}
// Locate a database file
if( PLATFORM == "macos" ) {
    $home = shell_exec( "echo ~/monkey" );
    define( "usrPath" , preg_replace('/\s+/', '', $home ) );
} else if( PLATFORM == "windows" ) {
    define( "usrPath" , "" );
    die( "NOT READY FOR WINDOWS YET" ); // do this in the future;
}
// Check for usr path
if( ! file_exists( usrPath ) ) {
    if( PLATFORM == "macos" ) {
        shell_exec( "mkdir " . usrPath );
    } else {
        mkdir( usrPath );
    }
}
// Load the blank database file
$db = new PDO( "sqlite:" . usrPath . "/monkey.db" );
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if( isset( $_POST['submit'] ) ) {
    $name = filter_var( $_POST['name'] , FILTER_SANITIZE_STRING );
    $email = filter_var( $_POST['email'] , FILTER_VALIDATE_EMAIL );
    $licencekey = filter_var( $_POST['licencekey'] , FILTER_SANITIZE_STRING );

    $insertCompany = $db->prepare( "INSERT INTO `company` (`name`,`lastbackup`,`email`) VALUES(:comName,:lastbackup,:email)" );
    $insertCompany->execute( [ ':comName' => $name , ':lastbackup' => date( "Y-m-d" ) , ':email' => $email ] );

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
                ':licenceto' => filter_var( $activation->customer , FILTER_SANITIZE_STRING ),
                ':purchasedate' => filter_var( $activation->purchaseDate , FILTER_SANITIZE_STRING ),
                ':lastactivation' => filter_var( $activation->activationDate , FILTER_SANITIZE_STRING ),
                ':nextactivation' => filter_var( $activation->nextActivation , FILTER_SANITIZE_STRING )
            ]);
        }
    }
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
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class='input-group-text'>Licence Key:</span></div>
                                    <input class="form-control" name="licencekey">
                                </div>
                            </div>
                            <div class="card-footer">
                                <center><button type="submit" name="submit" class="btn btn-success">Yet's get started!</button></center>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>