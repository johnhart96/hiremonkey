<?php
require '../inc/usrPath.php';
require '../inc/version.php';
$target_dir = usrPath . "/";
$target_file = $target_dir . "restore_" . basename( $_FILES['restore']['name'] );
$uploadOK = TRUE;


if( $uploadOK ) {
    if( ! move_uploaded_file( $_FILES['restore']['tmp_name'] , $target_file ) ) {
        echo "<div class='alert alert-danger'><strong>Error:</strong> Unable to open the file!</div>";
        die();
    }
}
$openfile = fopen( $target_file , "r" );
$json = fread( $openfile , filesize( $target_file ) );
fclose( $openfile );
$upgrade = json_decode( $json , true );
// Create a fresh file with blank data
$newFile = usrPath . "/" . date( "YmdHi" ) . "_" . str_replace( "." , "_" , FULLBUILD ) . ".db";
$blank = fopen( "../inc/default_database.sql" , "r" );
$blank_db = fread( $blank , filesize( "../inc/default_database.sql" ) );
fclose( $blank );
$newDB = new SQLite3( $newFile );
$newDB->query( $blank_db );

require '../inc/restore_map.php';

unlink( $target_file );
header( "Location:company_select.php" );
?>