<?php
// Version control
if( file_exists( "package.json" ) ) {
    $packageFile = fopen( "package.json" , "r" );
    $fileContent = fread( $packageFile , filesize( "package.json" ) );
    $package = json_decode( $fileContent );
    fclose( $packageFile );
    define( "VERSION" , $package->version );
} else if( file_exists( "../package.json" ) ) {
    $packageFile = fopen( "../package.json" , "r" );
    $fileContent = fread( $packageFile , filesize( "../package.json" ) );
    $package = json_decode( $fileContent );
    fclose( $packageFile );
    define( "VERSION" , $package->version );
}
define( "BUILD_STATUS" , "alpha" ); 
define( "LICENCEVERSION" , 1 ); 
?>