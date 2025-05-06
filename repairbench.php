<?php
$id = filter_var( $_GET['id'] , FILTER_SANITIZE_NUMBER_INT );
if( isset( $_POST['submitHeader'] ) ) {
    $description = filter_var( $_POST['description'] , FILTER_UNSAFE_RAW );
    $repairtype = filter_var( $_POST['repairtype'] , FILTER_UNSAFE_RAW );
    $startdate = filter_var( $_POST['startdate'] , FILTER_UNSAFE_RAW );
    $enddate = filter_var( $_POST['enddate'] , FILTER_UNSAFE_RAW );
    
    $update = $db->prepare( "UPDATE `kit_repairs` SET `description` =:descr, `repairtype` =:repairtype, `startdate` =:startdate, `enddate` =:enddate WHERE `id` =:repairID" );
    try {
        $update->execute( [ ':descr' => $description , ':repairtype' => $repairtype , ':startdate' => $startdate , ':enddate' => $enddate , ':repairID' => $id ] );
    } catch( PDOException $e ) {
        die( "Could not update the header!" );
    }
    $saved = TRUE;
}
if( isset( $_POST['submitNotes'] ) ) {
    $cost = (double)$_POST['cost'];
    $notes = filter_var( $_POST['notes'] , FILTER_SANITIZE_SPECIAL_CHARS );
    $update = $db->prepare("
            UPDATE `kit_repairs` SET
                `notes` =:notes,
                `cost` =:repairCost
            WHERE `id` =:repairID
    ");
    $update->execute( [ ':notes' => $notes , ':repairCost' => $cost , ':repairID' => $id ] );
    $saved = TRUE;
}
if( isset( $_GET['close'] ) ) {
    $complete = $db->prepare( "UPDATE `kit_repairs` SET `complete` =1 , `enddate` =:enddate WHERE `id` =:id" );
    $complete->execute( [ ':enddate' => date( "Y-m-d" ) , ':id' => $id ] );
    $saved = TRUE;
}
if( isset( $_GET['open'] ) ) {
    $open = $db->prepare( "UPDATE `kit_repairs` SET `complete` =0 WHERE `id` =:id" );
    $open->execute( [ ':id' => $id ] );
}
$getRepair = $db->prepare( "SELECT * FROM `kit_repairs` WHERE `id` =:repairID" );
$getRepair->execute( [ ':repairID' => $id ] );
$repair = $getRepair->fetch( PDO::FETCH_ASSOC );
?>
<div class="row">
    <div class="col">
        <h1>Repair Bench</h1>
        <hr />
        <?php if( isset( $saved ) ) { ?>
            <div class="alert alert-success">Changes saved!</div>
        <?php } ?>
    </div>  
</div>
<div class="row">
    <div class="col">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php?l=repairs">Repairs</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $repair['description']; ?></li>
            </ol>
        </nav>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header" id="details-header">
                <strong>Header</strong>
            </div>
            <div class="card-body" id="details-body" style="display: none;">
                <form method="post">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Description:</span></div>
                        <input name="description" class="form-control" value="<?php echo $repair['description']; ?>">
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Type:</span></div>
                        <select name="repairtype" class="form-control">
                            <?php
                            switch( $repair['repairtype'] ) {
                                case "repair":
                                    echo "<option value='repair' selected>Repair</option>";
                                    echo "<option value='service'>Service</option>";
                                    echo "<option value='lost'>Lost</option>";
                                    echo "<option value='test'>Test</option>";
                                    break;
                                case "service":
                                    echo "<option value='repair'>Repair</option>";
                                    echo "<option value='service' selected>Service</option>";
                                    echo "<option value='lost'>Lost</option>";
                                    echo "<option value='test'>Test</option>";
                                    break;
                                case "lost":
                                    echo "<option value='repair'>Repair</option>";
                                    echo "<option value='service'>Service</option>";
                                    echo "<option value='lost' selected>Lost</option>";
                                    echo "<option value='test'>Test</option>";
                                    break;
                                case "test":
                                    echo "<option value='repair'>Repair</option>";
                                    echo "<option value='service'>Service</option>";
                                    echo "<option value='lost'>Lost</option>";
                                    echo "<option value='test' selected>Test</option>";
                                    break;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Start date:</span></div>
                        <input class="form-control" name="startdate" value="<?php echo date( "Y-m-d"  , strtotime( $repair['startdate'] ) ); ?>" placeholder="YYYY-MM-DD">
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">End date:</span></div>
                        <input class="form-control" name="enddate" value="<?php if( ! empty( $repair['enddate'] ) ) { echo date( "Y-m-d"  , strtotime( $repair['enddate'] ) ); } ?>" placeholder="YYYY-MM-DD">
                    </div>
                    <p>&nbsp;</p>
                    <p><button type="submit" name="submitHeader" class="btn btn-success">Save</button></p>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">&nbsp;</div>

<div class="row">
    <div class="col-3">
        <div class="card">
            <div class="card-header"><strong>Equipment Details:</strong></div>
            <div class="card-body">
                <?php
                $getKitDetails = $db->prepare( "SELECT * FROM `kit` WHERE `id` =:kitID LIMIT 1" );
                $getKitDetails->execute( [ ':kitID' => (int)$repair['kit'] ] );
                $kit = $getKitDetails->fetch( PDO::FETCH_ASSOC );
                ?>
                <table style="text-align: left;">
                    <tr>
                        <th>Type:</th>
                        <td><?php echo $kit['name']; ?></td>
                    </tr>
                    <tr>
                        <th>Purchase value:</th>
                        <td><?php echo company( "currencysymbol" ) . price( $kit['purchasevalue'] ); ?></td>
                    </tr>
                    <tr>
                        <th>Previous repairs:</th>
                        <td>
                            <?php
                            $searchRepairs = $db->prepare( "SELECT * FROM `kit_repairs` WHERE `kit` =:kitID AND `id`  !=:repairID" );
                            $searchRepairs->execute( [ ':kitID' => (int)$repair['kit'] , ':repairID' => (int)$repair['id'] ] );
                            $count = 0;
                            while( $row = $searchRepairs->fetch( PDO::FETCH_ASSOC ) ) {
                                $count ++;
                            }
                            echo $count;
                            ?>
                        </td>
                    <tr>
                </table>
            </div>
            <div class="card-footer">
                <?php if( $repair['complete'] == 1 ) { ?>
                    <a href="index.php?l=repairbench&id=<?php echo $id; ?>&open" class="btn btn-primary">Re-open</a>
                <?php } else { ?>
                    <a href="index.php?l=repairbench&id=<?php echo $id; ?>&close" class="btn btn-primary">Complete</a>    
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="col">
        <form method="post">
            <div class="card">
                <div class="card-header"><strong>Notes:</strong></div>
                <div class="card-body">
                    <textarea name="notes" id="editor"><?php echo $repair['notes']; ?></textarea>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Cost:</span>
                        </div>
                        <input name="cost" type="text" placeholder="0.00" value="<?php echo price( $repair['cost'] ); ?>">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" name="submitNotes" class="btn btn-success">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
if( $repair['complete'] == 1 ) {
    echo "<script>CKEDITOR.config.readOnly = true;</script>";
}
?>
<script>
  	initSample();
</script>