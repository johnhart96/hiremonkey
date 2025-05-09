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
define( "BUILD_STATUS" , "beta" ); 
define( "LICENCEVERSION" , 2 ); 
define( "FULLBUILD" , "2.0.0.1" );

// Important table list
$tables = array(
    'categories',
    'company',
    'customers',
    'customers_addresses',
    'customers_contacts',
    'jobs',
    'jobs_cat',
    'jobs_lines',
    'kit',
    'kit_accessories',
    'kit_stock',
    'sloc',
    'licence',
    'kit_repairs'
);
?>