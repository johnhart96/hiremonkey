<?php
/******* Config Area *******/
define( "ACTIVATION_SERVER" , "https://activation.jh96.co.uk" );
define( "PRODUCT" , "5f96faf64ed4ca35e9f0e4ec71db0c58" );





// Do not touch past here
function jhl_activate( $key , $version , $uuid = NULL ) {
    error_reporting( false );
    $url = ACTIVATION_SERVER . "?app=" . PRODUCT . "&serial=" . $key . "&version=" . $version;
    if( $uuid !== NULL ) {
        $url .= "&uuid=" . $uuid;
    }
    $activate = file_get_contents( $url );
    $json = json_decode( $activate );
    if( $json->status > 0 ) {
        error_reporting( E_ALL );
        return $json;
    } else {
        error_reporting( E_ALL );
        echo "<script>alert('Cannot communicate with activation server!');</script>";
        return FALSE;
    }
}
function jhl_licenceerror() {
    if( isset( $_POST ) ) {
        if( ! isset( $_POST['activate'] ) AND ! isset( $_POST['deactivate'] ) ) {
            unset( $_POST );
        }
    }
    return TRUE;
}
?>