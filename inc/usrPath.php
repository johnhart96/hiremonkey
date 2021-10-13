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
    $home = shell_exec( "echo %USERPROFILE%\monkey" );
    $home = str_replace( DIRECTORY_SEPARATOR , "/" , $home );
    define( "usrPath" , dirname($home) . "/monkey" );
}
// Check for usr path
if( ! file_exists( usrPath ) ) {
    if( PLATFORM == "macos" ) {
        shell_exec( "mkdir " . usrPath );
    } else {
        shell_exec( 'mkdir "' . usrPath . "'" );
    }
}
?>