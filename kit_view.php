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
$id = filter_var( $_GET['id'] , FILTER_SANITIZE_NUMBER_INT );

// save Details
if( isset( $_POST['saveDetails'] ) ) {
    $name = filter_var( $_POST['name'] , FILTER_SANITIZE_STRING );
    $purchasevalue = filter_var( $_POST['purchasevalue'] , FILTER_SANITIZE_STRING );
    $price = filter_var( $_POST['price'] , FILTER_SANITIZE_STRING );
    $width = filter_var( $_POST['width'] , FILTER_VALIDATE_FLOAT );
    $height = filter_var( $_POST['height'] , FILTER_VALIDATE_FLOAT );
    $length = filter_var( $_POST['length'] , FILTER_VALIDATE_FLOAT );
    $weight = filter_var( $_POST['weight'] , FILTER_VALIDATE_FLOAT );
    $notes = filter_var( $_POST['notes'] , FILTER_SANITIZE_STRING );
    $active = filter_var( $_POST['active'] , FILTER_SANITIZE_NUMBER_INT );
    $toplevel = filter_var( $_POST['toplevel'] , FILTER_SANITIZE_NUMBER_INT );
    $sloc = filter_var( $_POST['sloc'] , FILTER_SANITIZE_NUMBER_INT );
    $cat = filter_var( $_POST['cat'] , FILTER_SANITIZE_NUMBER_INT );
    
    $update = $db->prepare("
        UPDATE `kit` SET
            `name`=:kitName,
            `purchasevalue` =:purchasevalue,
            `price` =:price,
            `weight` =:kitWeight,
            `height` =:height,
            `length` =:kitLength,
            `notes` =:notes,
            `width` =:width,
            `active` =:active,
            `toplevel` =:toplevel,
            `sloc` =:sloc,
            `cat` =:cat
        WHERE `id` =:id
    ");
    $update->execute([
        ':kitName' => $name,
        ':purchasevalue' => $purchasevalue,
        ':price' => $price,
        ':kitWeight' => $weight,
        ':height' => $height,
        ':kitLength' => $length,
        ':width' => $width,
        ':notes' => $notes,
        ':active' => $active,
        ':toplevel' => $toplevel,
        ':sloc' => $sloc,
        ':cat' => $cat,
        ':id' => $id
    ]);
    $saved = true;
}
// Delete Accessory
if( isset( $_POST['deleteAccessory'] ) ) {
    $deleteID = filter_var( $_POST['deleteAccessory'] , FILTER_SANITIZE_NUMBER_INT );
    $delete = $db->prepare( "DELETE FROM `kit_accessories` WHERE `id` =:id" );
    $delete->execute( [ ':id' => $deleteID ] );
    $saved = true;
}

// New accessory
if( isset( $_POST['submitNewAccessory'] ) ) {
    $accessory = filter_var( $_POST['accessory'] , FILTER_SANITIZE_NUMBER_INT );
    $type = filter_var( $_POST['type'] , FILTER_SANITIZE_STRING );
    $price = filter_var( $_POST['price'] , FILTER_VALIDATE_FLOAT );
    $mandatory = filter_var( $_POST['mandatory'] , FILTER_SANITIZE_NUMBER_INT );
    $qty = filter_var( $_POST['qty'] , FILTER_SANITIZE_NUMBER_INT );
    $insert = $db->prepare( "INSERT INTO `kit_accessories` (`accessory`,`type`,`price`,`kit`,`mandatory`,`qty`) VALUES(:accessory,:accType,:price,:kit,:mandatory,:qty)" );
    $insert->execute( [ ':accessory' => $accessory , ':accType' => $type , ':price' => $price , ':kit' => $id , ':mandatory' => $mandatory , ':qty' => $qty ] );
    $saved = true;
}

// New stock
if( isset( $_POST['submitNewStock'] ) ) {
    $serialized = filter_var( $_POST['type'] , FILTER_SANITIZE_NUMBER_INT );
    $purchasedate = filter_var( $_POST['purchasedate'] , FILTER_SANITIZE_STRING );
    $insert = $db->prepare( "INSERT INTO `kit_stock` (`kit`,`stock_count`,`serialnumber`,`purchasedate`,`serialized`) VALUES(:kit,:stock_count,:serialnumber,:purchasedate,:serialized)" );
    if( $serialized == 1 ) {
        // Serialized
        $serial = filter_var( $_POST['serialnumber'] , FILTER_SANITIZE_STRING );
        $insert->execute([
            ':kit' => $id,
            ':stock_count' => 1,
            ':serialnumber' => $serial,
            ':purchasedate' => $purchasedate,
            ':serialized' =>1
        ]);
    } else {
        // Bulk
        $count = filter_var( $_POST['count'] , FILTER_SANITIZE_NUMBER_INT );
        $insert->execute( [ ':kit' => $id , ':stock_count' => $count , ':serialnumber' => NULL , ':purchasedate' => $purchasedate , ':serialized' =>0 ] );
    }
    $saved = true;
}

// Edit stock
if( isset( $_POST['editStock'] ) ) {
    $purchasedate = filter_var( $_POST['purchasedate'] , FILTER_SANITIZE_STRING );
    $count = filter_var( $_POST['count'] , FILTER_SANITIZE_NUMBER_INT );
    $serial = filter_var( $_POST['serialnumber'] , FILTER_SANITIZE_STRING );
    $update = $db->prepare("
        UPDATE `kit_stock` SET
            `stock_count` =:stock_count,
            `serialnumber` =:serialnumber,
            `purchasedate` =:purchasedate,
            `serialnumber` =:serialnumber
        WHERE `id` =:stockID
    ");
    $stockID = filter_var( $_POST['editStock'] , FILTER_SANITIZE_NUMBER_INT );
    $update->execute( [ ':stock_count' => $count , ':serialnumber' => $serial , ':purchasedate' => $purchasedate , ':stockID' => $stockID ] );
    $saved = true;
}

// Delete stock
if( isset( $_POST['deleteStock'] ) ) {
    $toDelete = filter_var( $_POST['deleteStock'] , FILTER_SANITIZE_NUMBER_INT );
    $delete = $db->prepare( "DELETE FROM `kit_stock` WHERE `id` =:stockID" );
    $delete->execute( [ ':stockID' => $toDelete ] );
    $saved = true;
}

// Delete kit
if( isset( $_POST['deleteKit'] ) ) {
    $deleteAccessories = $db->prepare( "DELETE FROM `kit_accessories` WHERE `kit` =:id" );
    $deleteStock = $db->prepare( "DELETE FROM `kit_stock` WHERE `kit` =:id" );
    $delete = $db->prepare( "DELETE FROM `kit` WHERE `id` =:id" );

    $deleteAccessories->execute( [ ':id' => $id ] );
    $deleteStock->execute( [ ':id' => $id ] );
    $delete->execute( [ ':id' => $id ] );
    go( "index.php?l=kit_browse" );
}

// Get current
$getCurrent = $db->prepare( "SELECT * FROM `kit` WHERE `id` =:id LIMIT 1" );
$getCurrent->execute( [ ':id' => $id ] );
$kit = $getCurrent->fetch( PDO::FETCH_ASSOC );

// Duplicate
if( isset( $_POST['duplicate'] ) ) {
    $dup = $db->prepare("
        INSERT INTO `kit` (`name`,`purchasevalue`,`sloc`,`price`,`height`,`width`,`weight`,`notes`,`active`,`toplevel`,`cat`)
        VALUES(:kit,:purchasevalue,:sloc,:price,:height,:width,:weight,:notes,:active,:toplevel,:cat)
    ");
    $dup->execute(
        [
            ':kit' => $kit['name'] . "_" . date( "YmdHi" ),
            ':purchasevalue' => $kit['purchasevalue'],
            ':sloc' => $kit['sloc'],
            ':price' => $kit['price'],
            ':height' => $kit['height'],
            ':weight' => $kit['weight'],
            ':active' => $kit['active'],
            ':toplevel' => $kit['toplevel'],
            ':notes' => $kit['notes'],
            ':cat' => $kit['cat']
        ]
    );
    $getLastID = $db->query( "SELECT `id` FROM `kit` ORDER BY `id` DESC LIMIT 1" );
    $lastID = $getLastID->fetch( PDO::FETCH_ASSOC );
    $newID = (int)$lastID['id'];
    // Add accessories
    $getAccessories = $db->prepare( "SELECT * FROM `kit_accessories` WHERE `kit` =:kitID " );
    $getAccessories->execute( [ ':kitID' => $id ] );
    $insertAcccessory = $db->prepare( "INSERT INTO `kit_accessories` (`accessory`,`type`,`price`,`kit`,`qty`,`mandatory`) VALUES(:accessory,:typeID,:price,:kit,:qty,:mandatory)" );

    while( $accessory = $getAccessories->fetch( PDO::FETCH_ASSOC ) ) {
        $insertAcccessory->execute(
            [
                ':accessory' => $accessory['accessory'],
                ':typeID' => $accessory['type'],
                ':price' => $accessory['price'],
                ':kit' => $newID,
                ':qty' => $accessory['qty'],
                ':mandatory' => $accessory['mandatory']
            ]
        );
    }
    go( "index.php?l=kit_view&id=$newID" );
}
?>

<div class="row">
    <div class="col">
        <h1>Equipment Record:</h1>
        <hr />
    </div>
</div>
<div class="row">
    <div class="col">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php?l=kit_browse">Equipment</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $kit['name'] ?></li>
            </ol>
        </nav>
        <?php
        if( isset( $saved ) ) {
            echo "<div class='alert alert-success'>Saved!</div>";
        }
        ?>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="btn-group">
            <?php
            // Duplicate
            modalButton( "duplicate" , "Duplicate" );
            $dialog = "
                Are you sure you want to duplicate this equipment record?
                <input type='hidden' name='duplicate'>
            ";
            modal( "duplicate" , "Duplicate record" , $dialog , "Yes No" );

            // Delete
            modalButton_red( "deletekit" , "Delete" );
            $dialog = "
                <div class='alert alert-warning'>
                    <strong>WARNING!</strong> Deleting this equipment record will delete all stock records. Are you sure you want to do this? <br />
                    This will no have any effect on existing jobs!
                </div>
                <input type='hidden' name='deleteKit'>
            ";
            modal( "deletekit" , "Delete?" , $dialog , "Yes No" );
            ?>
        </div>
    </div>
</div>
<div class="row">&nbsp;</div>
<form method="post">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header"><strong>Details:</strong></div>
                <div class="card-body">
                    <?php
                    if( (int)$kit['active'] == 0 ) {
                        echo "<div class='alert alert-warning'>Equipment is inactive!</div>";
                    }
                    ?>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Name:</span></div>
                        <input type="text" name="name" class="form-control" value="<?php echo $kit['name']; ?>">
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Purchase Value: <?php echo company( "currencysymbol" ); ?> </span></div>
                        <input type="text" name="purchasevalue" class="form-control" value="<?php echo price( $kit['purchasevalue'] ); ?>">
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Hire Price: <?php echo company( "currencysymbol" ); ?> </span></div>
                        <input type="text" name="price" class="form-control" value="<?php echo price( $kit['price'] ); ?>">
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Width:</span></div>
                        <input type="text" name="width" class="form-control" value="<?php echo $kit['width']; ?>">
                        <div class="input-group-append"><span class="input-group-text">MM</span></div>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Height:</span></div>
                        <input type="text" name="height" class="form-control" value="<?php echo $kit['height']; ?>">
                        <div class="input-group-append"><span class="input-group-text">MM</span></div>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Length:</span></div>
                        <input type="text" name="length" class="form-control" value="<?php echo $kit['length']; ?>">
                        <div class="input-group-append"><span class="input-group-text">MM</span></div>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Weight:</span></div>
                        <input type="text" name="weight" class="form-control" value="<?php echo $kit['weight']; ?>">
                        <div class="input-group-append"><span class="input-group-text">KG</span></div>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Status:</span></div>
                        <select name="active" class="form-control">
                            <?php
                            switch( $kit['active'] ) {
                                case 1:
                                    echo "<option value='1' selected>Active</option>";
                                    echo "<option value='0'>Inactive</option>";
                                    break;
                                case 0:
                                    echo "<option value='1'>Active</option>";
                                    echo "<option value='0' selected>Inactive</option>";
                                    break;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Product:</span></div>
                        <select name="toplevel" class="form-control">
                            <?php
                            switch( $kit['toplevel'] ) {
                                case 1:
                                    echo "<option value='1' selected>Product</option>";
                                    echo "<option value='0'>Accessory</option>";
                                    break;
                                case 0:
                                    echo "<option value='1'>Product</option>";
                                    echo "<option value='0' selected>Accessory</option>";
                                    break;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Storage Location:</span></div>
                        <select name="sloc" class="form-control">
                            <?php
                            $currentSloc = (int)$kit['sloc'];
                            $getAllSloc = $db->query( "SELECT * FROM `sloc`" );
                            while( $sloc = $getAllSloc->fetch( PDO::FETCH_ASSOC ) ) {
                                if( (int)$sloc['id'] == $currentSloc ) {
                                    echo "<option value='" . $sloc['id'] . "' selected>" . $sloc['name'] . "</option>";
                                } else {
                                    echo "<option value='" . $sloc['id'] . "'>" . $sloc['name'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Category:</span></div>
                        <select name="cat" class="form-control">
                            <?php
                            $currentCat = (int)$kit['cat'];
                            $getAllCat = $db->query( "SELECT * FROM `categories`" );
                            while( $cat = $getAllCat->fetch( PDO::FETCH_ASSOC ) ) {
                                if( (int)$cat['id'] == $currentCat ) {
                                    echo "<option value='" . $cat['id'] . "' selected>" . $cat['name'] . "</option>";
                                } else {
                                    echo "<option value='" . $cat['id'] . "'>" . $cat['name'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-floating">
                        <textarea name="notes" id="notes" placeholder="Notes:" class="form-control"><?php echo $kit['notes']; ?></textarea>
                        <label for="notes">Notes:</label>
                    </div>
                    <p>&nbsp;</p>
                    <button type="submit" name="saveDetails" class="btn btn-success">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header"><strong>Stock (<?php echo totalStockCount( $id ); ?>) :</strong></div>
            <div class="card-body">
                <table class="table table-bordered table-stripe">
                    <tr>
                        <th>Count/Serial number</th>
                        <th>Stock type</th>
                        <th>Purchase date</th>
                        <th colspan="2"></td>
                    </tr>
                    <?php
                    $getStock = $db->prepare( "SELECT * FROM `kit_stock` WHERE `kit` =:id" );
                    $getStock->execute( [ ':id' => $id ] );
                    while( $row = $getStock->fetch( PDO::FETCH_ASSOC ) ) {
                        echo "<tr>";
                        if( (int)$row['serialized'] == 1 ) {
                            echo "<td>" . $row['serialnumber'] . "</td>";
                            echo "<td>Serialized</td>";
                        } else {
                            echo "<td>" . $row['stock_count'] . "</td>";
                            echo "<td>Bulk</td>";
                        }
                        echo "<td>" . $row['purchasedate'] . "</td>";
                        // Edit
                        echo "<td width='1'>";
                        $modal = "editstock_"  . $row['id'];
                        $dialog = "
                            <div class='alert alert-info'>
                                <strong>Note:</strong> If this entry is a serialized, the count is ignored.<br />
                                    If bulk stock, the serial number is ignored.
                            </div>
                            <div class='input-group'>
                                <div class='input-group-prepend'><span class='input-group-text'>Serial number:</span></div>
                                <input type='text' name='serialnumber' class='form-control' value='" . $row['serialnumber'] . "'>
                            </div>
                            <div class='input-group'>
                                <div class='input-group-prepend'><span class='input-group-text'>Purchase date:</span></div>
                                <input type='text' name='purchasedate' class='form-control' value='" . $row['purchasedate']. "' placeholder='YYYY-MM-DD'>
                            </div>
                            <div class='input-group'>
                                <div class='input-group-prepend'><span class='input-group-text'>Count:</span></div>
                                <input type='text' name='count' class='form-control' value='" . $row['stock_count']  . "'>
                            </div>
                            <input type='hidden' name='editStock' value='"  .$row['id'] . "'> 
                        ";
                        modal( $modal , "Edit stock entry" , $dialog , "Save Cancel" );
                        modalButton( $modal , "Edit" );
                        echo "</td>";
                        // Delete
                        echo "<td width='1'>";
                        $modal = "deletestock_" . $row['id'];
                        modalButton_red( $modal , "Delete" );
                        $dialog = "
                            Are you sure you want to delete this stock entry?
                            <input type='hidden' name='deleteStock' value='" . $row['id'] . "'>
                        ";
                        modal( $modal , "Delete?" , $dialog , "Yes No" );
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                    <tr>
                        <td colspan="5" align="center">
                            <?php
                            modalButton_green( "newstock" , "New" );
                            $dialog = "
                                <div class='alert alert-info'>
                                    <strong>Note:</strong> If this entry is a serialized, the count is ignored.<br />
                                    If bulk stock, the serial number is ignored.
                                </div>
                                <div class='input-group'>
                                    <div class='input-group-prepend'><span class='input-group-text'>Type:</span></div>
                                    <select name='type' class='form-control'>
                                        <option selected value='0'>Bulk</option>
                                        <option value='1'>Serialized</option>
                                    </select>
                                </div>
                                <div class='input-group'>
                                    <div class='input-group-prepend'><span class='input-group-text'>Serial number:</span></div>
                                    <input type='text' name='serialnumber' class='form-control'>
                                </div>
                                <div class='input-group'>
                                    <div class='input-group-prepend'><span class='input-group-text'>Purchase date:</span></div>
                                    <input type='text' name='purchasedate' class='form-control' value='" . date( "Y-m-d" ) . "' placeholder='YYYY-MM-DD'>
                                </div>
                                <div class='input-group'>
                                    <div class='input-group-prepend'><span class='input-group-text'>Count:</span></div>
                                    <input type='text' name='count' class='form-control' value='1'>
                                </div>
                                <input type='hidden' name='submitNewStock'> 
                            ";
                            modal( "newstock" , "New stock entry" , $dialog , "Save Cancel" );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header"><strong>Accessories:</strong></div>
            <div class="card-body">
                <table class="table table-bordered table-stripe">
                    <tr>
                        <th>Accessory</th>
                        <th>Type</th>
                        <th>Mandatory</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th colspan="2"></th>
                    </tr>
                    <?php
                    function getAccessories( $kit , $prefix = "" ) {
                        global $db;
                        $getAccessories = $db->prepare( "SELECT * FROM `kit_accessories` WHERE `kit` =:id" );
                        $getAccessories->execute( [ ':id' => $kit ] );
                        while( $accessory = $getAccessories->fetch( PDO::FETCH_ASSOC ) ) {
                            $accessoryID = $accessory['accessory'];
                            $getDetails = $db->prepare( "SELECT * FROM `kit` WHERE `id` =:id" );
                            $getDetails->execute( [ ':id' => $accessoryID ] );
                            $detail = $getDetails->fetch( PDO::FETCH_ASSOC );
                            echo "<tr>";
                            echo "<td>" . $prefix . "<a href='index.php?l=kit_view&id=" . $accessoryID . "'>" . $detail['name'] . "</a></td>";
                            // Type
                            echo "<td>";
                            switch( $accessory['type'] ) {
                                case "safety":
                                    echo "Safety item";
                                    break;
                                case "component":
                                    echo "Component";
                                    break;
                                case "accessory":
                                    echo "Accessory";
                                    break;
                                case "spare":
                                    echo "Spare";
                                    break;
                            }
                            echo "</td>";
                            // mandatory
                            echo "<td>";
                            switch( $accessory['mandatory'] ) {
                                case 1:
                                    echo "Yes";
                                    break;
                                case 0:
                                    echo "No";
                                    break;
                            }
                            echo "</td>";
                            // Price
                            echo "<td>" . company( "currencysymbol" ) .  price( $accessory['price'] ) . "</td>";
                            // Qty
                            echo "<td>" . $accessory['qty'] . "</td>";
                            // Delete
                            echo "<td width='1'>";
                            $modal = "deleteaccessory_" . $accessory['id'];
                            $dialog = "
                                Are you sure you want to delete this accessory?
                                <input type='hidden' name='deleteAccessory' value='" . $accessory['id'] . "'>
                            ";
                            if( $prefix == "" ) {
                                modal( $modal , "Delete?" , $dialog , "Yes No" );
                                modalButton_red( $modal , "Delete" );
                            }
                            echo "</td>";
                            echo "</tr>";
                            $prefix = "- " . $prefix;
                            if( $accessory['type'] !== 'spare' ) {
                                getAccessories( $accessoryID , $prefix );
                            }
                            $prefix = "";
                        }
                    }
                    getAccessories( $id );
                    ?>
                    <tr>
                        <td colspan="6" align="center">
                            <?php
                            modalButton_green( "new" , "New" );
                            $dialog = "
                                <div class='input-group'>
                                    <div class='input-group-prepend'><span class='input-group-text'>Accessory:</span></div>
                                    <select name='accessory' class='form-control'>
                            ";
                            $getAllAccessories = $db->prepare( "SELECT * FROM `kit` WHERE `toplevel` =0 AND `id` !=:id" );
                            $getAllAccessories->execute( [ ':id' => $id ] );
                            $dialog .= "<optgroup label='Accessories'>";
                            while( $row = $getAllAccessories->fetch( PDO::FETCH_ASSOC ) ) {
                                $dialog .= "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                            }
                            $dialog .= "</optgroup>";
                            $dialog .= "<optgroup label='Equipment'>";
                            $getAllKit = $db->query( "SELECT * FROM `kit` WHERE `toplevel` =1" );
                            while( $row = $getAllKit->fetch( PDO::FETCH_ASSOC ) ) {
                                $dialog .= "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                            }
                            $dialog .= "</optgroup>";
                            $dialog .= "
                                    </select>
                                </div>
                                <div class='input-group'>
                                    <div class='input-group-prepend'><span class='input-group-text'>Qty:</span></div>
                                    <input type='text' name='qty' class='form-control' value='1'>
                                </div>
                                <div class='input-group'>
                                    <div class='input-group-prepend'><span class='input-group-text'>Type:</span></div>
                                    <select name='type' class='form-control'>
                                        <option value='safety'>Safety item</option>
                                        <option value='component'>Component</option>
                                        <option selected value='accessory'>Accessory</option>
                                        <option value='spare'>Spare</option>
                                    </select>
                                </div>
                                <div class='input-group'>
                                    <div class='input-group-prepend'><span class='input-group-text'>Price: " . company( "currencysymbol" ) . "</span></div>
                                    <input type='text' name='price' class='form-control' value='0.00'>
                                </div>
                                <div class='input-group'>
                                    <div class='input-group-prepend'><span class='input-group-text'>Mandatory:</span></div>
                                    <select name='mandatory' class='form-control'>
                                        <option value='0'>No</option>
                                        <option value='1'>Yes</option>
                                    </select>
                                </div>
                                <input type='hidden' name='submitNewAccessory'>
                            ";
                            modal( "new" , "New accessory" , $dialog , "Save Cancel" );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">&nbsp;</div>
<div class="row">
    <div class="col">
        <?php
        
        ?>
    </div>
</div>
