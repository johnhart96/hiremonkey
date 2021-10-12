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
// Check local DB file
if( ! file_exists( usrPath . "/monkey.db" ) ) {
    // Create new file
    $blank = fopen( "inc/default_database.sql" , "r" );
    $blank_db = fread( $blank , filesize( "inc/default_database.sql" ) );
    fclose( $blank );
    $newDB = new SQLite3( usrPath . "/monkey.db" );
    $newDB->query( $blank_db );
    header( "Location:static/welcome.php" );
    
} else {
    try {
        $db = new PDO( "sqlite:" . usrPath . "/monkey.db" );
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
if( date( "Y-m-d" , strtotime( licence( "nextactivation" ) ) ) < date( "Y-m-d" ) && ! trial() ) {
    echo "<script>console.log('Starting activation');</script>";
    $activation = jhl_activate( licence( "licencekey" ) , LICENCEVERSION );
    $activationSuccessful = FALSE;
    if( isset( $activation->status ) ) {
        if( $activation->status == 200 ) {
            // Successful
            $activationSuccessful = TRUE;
            $nextActivation = filter_var( $activation->nextActivation , FILTER_SANITIZE_STRING );
            $updateNextActivation = $db->prepare( "UPDATE `licence` SET `nextActivation` =:nextActivation WHERE `id` > 0" );
            $updateNextActivation->execute( [ ':nextActivation' => $nextActivation ] );
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
