<?php 
/**
 *
 * This source code is subject to copyright.
 * Viewing, distributing, editing or extracting this source code will result in licence violation and/or legal action
 *
 * 
 * @package    HireMonkey
 * @author     John Hart
 * @copyright  2021 John Hart
 * @license    https://www.hiremonkey.app/licence.php
 */
session_start();
require_once 'inc/functions.php';
?>
<html>
  <head>
    <title>HireMonkey</title>
    
    <?php
    require_once 'inc/header.php';
    ?>
  </head>
  <body>
    <?php
    echo "<main>";
    require 'inc/sidebar.php';
    ?>
    <div class="container-fluid">
      <?php
      if( ! isset( $_GET['l'] ) ) {
        $location = "dashboard.php";
      } else {
        $location = filter_var( $_GET['l'] , FILTER_UNSAFE_RAW ) . ".php";
      }
      if( file_exists( $location ) ) {
        require $location;
      } else {
        die( "<div class='alert alert-danger'><strong>Error:</strong> Cannot locate `" . $location . "`</div>" );
      }
      ?>
    </div>
    <?php echo "</main>"; ?>
    <script src="../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
    <script src="js/sidebars.js" type="text/js"></script>
  </body>
</html>