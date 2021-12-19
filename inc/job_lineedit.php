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
?>
<form method="post">
    <?php
    if( isset( $_GET['editline'] ) ) {
        $selectedLine = filter_var( $_GET['editline'] , FILTER_SANITIZE_NUMBER_INT );
        $getSelectedLine = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `job` =:jobID AND `id` =:lineID LIMIT 1" );
        $getSelectedLine->execute( [ ':jobID' => $id , ':lineID' => $selectedLine ] );
        $line = $getSelectedLine->fetch( PDO::FETCH_ASSOC );

        // Line type
        echo "<div class='input-group'>";
        echo "<div class='input-group-prepend'><span class='input-group-text'>Type:</span></div>";
        echo "<select name='lineType' class='form-control'>";
        $types = array( 'hire', 'text', 'subhire' );
        foreach( $types as $try ) {
            if( $try == $line['linetype'] ) {
                echo "<option value='$try' selected>" . ucfirst( $try ) . "</option>";
            } else {
                echo "<option value='$try'>" . ucfirst( $try ) . "</option>";
            }
        }
        echo "</select>";
        echo "</div>";
                    
        // Cat
        echo "<div class='input-group'>";
        echo "<div class='input-group-prepend'><span class='input-group-text'>Category:</span></div>";
        echo "<select required name='cat' class='form-control'>";
        $getJobCats = $db->prepare( "SELECT * FROM `jobs_cat` WHERE `job` =:jobID" );
        $getJobCats->execute( [ ':jobID' => $id ] );
        while( $row = $getJobCats->fetch( PDO::FETCH_ASSOC ) ) {
            echo "<option value='0'>None</option>";
            if( (int)$row['id'] == (int)$line['cat'] ) {
                echo "<option selected value='" . $row['id'] . "'>" . $row['cat'] . "</option>";
            } else {
                echo "<option value='" . $row['id'] . "'>" . $row['cat'] . "</option>";
            }
        }
        echo "</select>";
        echo "</div>";

        // Price
        $checkPriceLock = $db->prepare( "SELECT `price_lock` FROM `jobs` WHERE `id` =:jobID" );
        $checkPriceLock->execute( [ ':jobID' => $id ] );
        $f = $checkPriceLock->fetch( PDO::FETCH_ASSOC );
        if( (int)$f['price_lock'] == 1 ) {
            $disabled = "disabled";
        } else {
            $disabled = "";
        }
        echo "<div class='input-group'>";
        echo "<div class='input-group-prepend'><span class='input-group-text'>Price: " . company( "currencysymbol") . "</span></div>";
        echo "<input type='text' name='price' $disabled class='form-control' value='" . price( $line['price'] ) . "'>";
        echo "</div>";

        // Qty
        echo "<div class='input-group'>";
        echo "<div class='input-group-prepend'><span class='input-group-text'>Qty:</span></div>";
        if( $line['mandatory'] == 1 ) {
            $disabled = "disabled";
        } else {
            $disabled = "";
        }
        echo "<input " . $disabled . " type='text' name='qty' class='form-control' value='" . $line['qty'] . "'>";
        echo "</div>";

        // Notes
        echo "<div class='form-floating'>";
        echo "<textarea class='form-control' name='notes' id='notes' placeholder='Notes:'>" . $line['notes'] . "</textarea>";
        echo "<label for='notes'>Notes:</label>";
        echo "</div>";

        // button
        echo "<button name='submitLineEdit' value='" . $line['id'] . "' class='btn btn-success'>Change</button>";
    } else {
        echo "<center><em>No line selected</em></center>";
    }
    ?>
</form>