<?php
require 'inc/usrPath.php';
// Check local DB file
if( ! isset( $_SESSION['company'] ) ) {
    go( "static/company_select.php" );
}
if( ! empty( $_SESSION['company'] ) ) {
    $dbpath = usrPath . "/" . $_SESSION['company'];
} else {
    $dbpath = NULL;
}

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
require_once 'inc/version.php';
?>

<link href="../node_modules/bootstrap/dist/css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="../node_modules/datatables/media/css/jquery.dataTables.css" rel="stylesheet" type="text/css" />
<link href="css/sidebars.css" rel="stylesheet" type="text/css" />
<link href="css/jquery-ui.css" rel="stylesheet" />
<link href="css/job.css" rel="stylesheet" />

<script>
    window.$ = window.jQuery = require('jQuery');
</script>
<script src="./js/jquery-ui.js" type="text/javascript"></script>
<script src="../node_modules/datatables/media/js/jquery.dataTables.js" type="text/javascript"></script>
<script src="./js/job.js" type="text/javascript"></script>
<script src="./js/repairs.js" type="text/javascript"></script>
<script src="./js/tables.js" type="text/javascript"></script>
<script src="ckeditor/ckeditor.js"></script>
<script src="js/ckeditor.init.js"></script>