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
<div class="row">
    <div class="col">
        <h1>Equipment:</h1>
        <hr />
    </div>
</div>
<div class="row">
    <div class="col">
        <form method="post" id="search">
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Search:</span></div>
                <input autofocus name="search" class="form-control" placeholder="Strand Patt. 23" value="<?php if( isset( $_POST['search'] ) ) { echo filter_var( $_POST['search'] , FILTER_UNSAFE_RAW ); } ?>" >
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
            <div class="btn-group" role="group" aria-label="">
                <?php
                if( isset( $_POST['filter'] ) ) {
                    $filter = filter_var( $_POST['filter'] , FILTER_SANITIZE_NUMBER_INT );
                    if( $filter == 1 ) {
                        $active = "checked";
                        $inactive = "";
                    } else {
                        $active = "";
                        $inactive = "checked";
                    }
                } else {
                    $active = "checked";
                    $inactive = "";
                }
                ?>
                <input type="radio" class="btn-check" name="filter" id="active" value="1" autocomplete="off" <?php echo $active; ?>>
                <label class="btn btn-outline-primary" for="active">Active</label>
                <input type="radio" class="btn-check" name="filter" id="inactive" value="0" autocomplete="off"<?php echo $inactive; ?>>
                <label class="btn btn-outline-primary" for="inactive">Inactive</label>
                <?php
                if( isset( $_POST['toplevel'] ) ) {
                    $toplevel = filter_var( $_POST['toplevel'] , FILTER_SANITIZE_NUMBER_INT );
                    if( $toplevel == 1 ) {
                        $active = "checked";
                        $inactive = "";
                    } else {
                        $active = "";
                        $inactive = "checked";
                    }
                } else {
                    $active = "checked";
                    $inactive = "";
                }
                ?>
                <input type="radio" class="btn-check" name="toplevel" id="toplevel" value="1" autocomplete="off" <?php echo $active; ?>>
                <label class="btn btn-outline-primary" for="toplevel">Products</label>
                <input type="radio" class="btn-check" name="toplevel" id="accessories" value="0" autocomplete="off"<?php echo $inactive; ?>>
                <label class="btn btn-outline-primary" for="accessories">Accessories</label>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col">
        <table class="table table-bordered table-striped table-dark" id="kit">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Storage Location</th>
                    <th>Hire Price</th>
                    <th>Stock</th>
                    <th></th>
                </tr>
            </thead>
            <?php
            if( isset( $_POST['search'] ) ) {
                $search = filter_var( $_POST['search'] , FILTER_UNSAFE_RAW );
                $filter = filter_var( $_POST['filter'] , FILTER_SANITIZE_NUMBER_INT );
                $toplevel = filter_var( $_POST['toplevel'] , FILTER_SANITIZE_NUMBER_INT );

                $getKit = $db->prepare( "SELECT * FROM `kit` WHERE instr(`name`,:searchTerm) AND `active` =:filter AND `toplevel` =:toplevel --case-insensitive  " );
                $getKit->execute( [ ':searchTerm' => $search , ':filter' => $filter , ':toplevel' => $toplevel ] );
            } else {
                $getKit = $db->query( "SELECT * FROM `kit` WHERE `active` =1 AND `toplevel` =1" );
            }
            while( $kit = $getKit->fetch( PDO::FETCH_ASSOC ) ) {
                echo "<tr>";
                echo "<td>" . $kit['name'] . "</td>";
                echo "<td>" . cat( $kit['cat'] ) . "</td>";
                echo "<td>" . sloc( $kit['sloc'] ) . "</td>";
                echo "<td>" . company( "currencysymbol") .  price( $kit['price'] ) . "</td>";
                echo "<td>" . totalStockCount( $kit['id'] ) . "</td>";
                echo "<td width='1'><a href='index.php?l=kit_view&id=" . $kit['id'] . "' class='btn btn-primary'>Open</a>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</div>