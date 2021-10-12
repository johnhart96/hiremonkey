<?php
if( isset( $_POST['submit'] ) ) {
    $name = filter_var( $_POST['name'] , FILTER_SANITIZE_STRING );
    $sloc = filter_var( $_POST['sloc'] , FILTER_SANITIZE_NUMBER_INT );
    $purchasevalue = filter_var( $_POST['purchasevalue'] , FILTER_VALIDATE_FLOAT );
    $height = filter_var( $_POST['height'] , FILTER_VALIDATE_FLOAT );
    $width = filter_var( $_POST['width'] , FILTER_VALIDATE_FLOAT );
    $length = filter_var( $_POST['length'] , FILTER_VALIDATE_FLOAT );
    $weight = filter_var( $_POST['weight'] , FILTER_VALIDATE_FLOAT );
    $notes = filter_var( $_POST['notes'] , FILTER_SANITIZE_STRING );
    $price = filter_var( $_POST['price'] , FILTER_VALIDATE_FLOAT );
    if( isset( $_POST['accessory'] ) ) {
        $toplevel = 0;
    } else {
        $toplevel = 1;
    }
    if( isset( $_POST['active'] ) ) {
        $active = 1;
    } else {
        $active = 0;
    }

    $insert = $db->prepare("
        INSERT INTO `kit` (`name`,`sloc`,`purchasevalue`,`height`,`width`,`length`,`weight`,`notes`,`price`,`active`,`toplevel`)
        VALUES(:name,:sloc,:purchasevalue,:height,:weight,:length,:weight,:notes,:price,:active,:toplevel)
    ");
    $insert->execute([
        ':name' => $name,
        ':sloc' => $sloc,
        ':purchasevalue' => $purchasevalue,
        ':height' => $height,
        ':weight' => $weight,
        ':length' => $length,
        ':weight' => $weight,
        ':notes' => $notes,
        ':price' => $price,
        ':active' => $active,
        ':toplevel' => $toplevel
    ]);
    $getLastEntry = $db->query( "SELECT * FROM `kit` ORDER BY `id` DESC LIMIT 1" );
    $fetch = $getLastEntry->fetch( PDO::FETCH_ASSOC );
    go( "index.php?l=kit_view&id=" . $fetch['id'] );
}
?>
<div class="row">
    <div class="col">
        <h1>New Equipment:</h1>
        <hr />
        <?php
        if( trial() ) {
            if( entry_count( "kit" ) >= 10 ) {
                echo "<div class='alert alert-danger'><strong>Error:</strong> You can only have 10 equipment in trial mode!</div>";
                die();
            } else {
                echo "<div class='alert alert-warning'><strong>Warning:</strong> You can only add " . 10 - entry_count( "kit" ) . " more equipment while in trial mode!</div>";
            }
        }
        ?>
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php?l=kit_browse">Equipment</a></li>
                <li class="breadcrumb-item active" aria-current="page">New</li>
            </ol>
        </nav>
    </div>
</div>
<form method="post">
    <div class="row">
        <div class="col">
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Name:</span></div>
                <input type="text" name="name" class="form-control">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Purchase value: <?php echo company( "currencysymbol" ); ?></span></div>
                <input type="text" name="purchasevalue" class="form-control" value="0.00" required>
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Storage Location:</span></div>
                <select name="sloc" class="form-control">
                    <?php
                    $getSloc = $db->query( "SELECT * FROM `sloc`" );
                    while( $sloc = $getSloc->fetch( PDO::FETCH_ASSOC ) ) {
                        echo "<option value='" . $sloc['id'] . "'>" . $sloc['name'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Height:</span></div>
                <input type="text" name="height" class="form-control">
                <div class="input-group-append"><span class="input-group-text">MM</span></div>
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Width:</span></div>
                <input type="text" name="width" class="form-control">
                <div class="input-group-append"><span class="input-group-text">MM</span></div>
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Length:</span></div>
                <input type="text" name="length" class="form-control">
                <div class="input-group-append"><span class="input-group-text">MM</span></div>
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Weight:</span></div>
                <input type="text" name="weight" class="form-control">
                <div class="input-group-append"><span class="input-group-text">KG</span></div>
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Hire Price: <?php echo company( "currencysymbol" ); ?> </span></div>
                <input type="text" name="price" class="form-control" value="0.00" required>
                <div class="input-group-append"><span class="input-group-text">/day</span></div>
            </div>
            <div class="form-floating">
                <textarea class="form-control" name="notes" placeholder="Notes:" id="notes" style="height: 100px"></textarea>
                <label for="notes">Notes:</label>
            </div>
            <div class="btn-group" role="group" aria-label="">
                <input type="checkbox" class="btn-check" name="accessory" id="accessory" autocomplete="on">
                <label class="btn btn-outline-primary" for="accessory">Accessory only</label>
                <input checked type="checkbox" class="btn-check" name="active" id="active" autocomplete="on">
                <label class="btn btn-outline-primary" for="active">Active</label>
            </div>
            <p>&nbsp;</p>
            <button type="submit" name="submit" class="btn btn-success">Save</button>
        </div>
    </div>
</form>