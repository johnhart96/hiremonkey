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
    <?php
    require_once 'inc/header.php';
    ?>
    <title>HireMonkey - Freelance Edition (v<?php echo FULLBUILD; ?>)</title>
  </head>
  <body>
    <?php
    echo "<main>";
    require 'inc/sidebar.php';
    ?>
    <div class="container-fluid">
      <div class="pins btn-group">
        <?php
        if( isset( $_POST['label'] ) ) {
          $url = url();
          $label = filter_var( $_POST['label'] , FILTER_UNSAFE_RAW );
          $add = $db->prepare( "INSERT INTO pins (link,label) VALUES(:u,:l)" );
          $add->execute( [ ':u' => $url , ':l' => $label ] );
        }
        $getPins = $db->query( "SELECT * FROM pins" );
        while( $pin = $getPins->fetch( PDO::FETCH_ASSOC ) ) {
          echo "<a href='" . $pin['link'] . "' class='btn btn-secondary'>" . $pin['label'] . "</a>";
        }
        modalButton( "newPin" , "+" , "Add a pin" );
        $form = "
          <div class='form-group'>
            <label for='label'>Label:</label>
            <input type='text' name='label' class='form-control'>
          </div>
        ";
        modal( "newPin" , "Add a new pin" , $form , "Save Cancel" );
        ?>
      </div>
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