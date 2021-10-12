<?php
// Activate
if( isset( $_POST['activate'] ) ) {
    $key = filter_var( $_POST['key'] , FILTER_SANITIZE_STRING );
    // Delete existing keys
    $delete = $db->query( "DELETE FROM `licence`" );
    // Add new key
    $add = $db->prepare( "INSERT INTO `licence` (`licencekey`) VALUES(:licencekey)" );
    $add->execute( [ ':licencekey' => $key ] );

    // Activation
    $activate = jhl_activate( licence( "licencekey" ) , LICENCEVERSION );
    if( isset( $activate->status ) ) {
        // Got a valid response
        if( $activate->status !== 200 ) {
            $activation_error = $activate->message;
            $delete = $db->query( "DELETE FROM `licence`" );
        } else {
            // Activation was successful
            $updateKey = $db->prepare( "UPDATE `licence` SET `purchasedate` =:purchasedate , `lastactivation` =:lastactivation , `nextactivation` =:nextactivation , `licenceto` =:licenceto WHERE `id` !=0" );
            $updateKey->execute([
                ':purchasedate' => filter_var( $activate->purchaseDate , FILTER_SANITIZE_STRING ),
                ':lastactivation' => filter_var( $activate->activationDate , FILTER_SANITIZE_STRING ),
                ':nextactivation' => filter_var( $activate->nextActivation , FILTER_SANITIZE_STRING ),
                ':licenceto' => filter_var( $activate->customer , FILTER_VALIDATE_EMAIL )
            ]);
            $activated = $activate->message;
        }
    }
}
// Deactivate
if( isset( $_POST['deactivate'] ) ) {
    // Delete existing keys
    $delete = $db->query( "DELETE FROM `licence`" );
}
?>
<div class="row">
    <div class="col">
        <h1>Settings:</h1>
        <hr />
        <?php
        if( isset( $activation_error ) ) {
            echo "<div class='alert alert-danger'><strong>Activation Error:</strong> Vendor responded with `" . $activation_error . "`</div>";
        } else if( isset( $activated ) ) {
            echo "<div class='alert alert-success'><strong>Activation Completed:</strong> Vendor responded with `" . $activated . "`</div>";
        }
        ?>
    </div>
</div>
<div class="row">
    <div class="col">
        <ul class="nav nav-tabs">
            <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=settings">Company</a></li>
            <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=settings_db">Database</a></li>
            <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php?l=settings_licence">Licence</a></li>
            <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=settings_sloc">Storage Locations</a></li>
        </ul>
    </div>
</div>
<div class="row">&nbsp;</div>
<div class="row">
    <div class="col">
        <?php if( trial() ) { ?>
            <div class="alert alert-warning"><strong>TRIAL MODE:</strong> You are running as a trial. All entries will be limited to 10. To unlock full features. Please activate a licence.</div>
        <?php } ?>
    </div>
</div>
<div class="row">
    <div class="col">
        <form method="post">
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Key:</span></div>
                <input type="text" name="key" class="form-control" value="<?php echo licence( "licencekey" ); ?>">
                <div class="input-group-append">
                    <button type="submit" name="activate" class="btn btn-success">Activate</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header"><strong>Licence Info:</strong></div>
            <div class="card-body">
                <table class="table table-bordered table-stripe">
                    <tr>
                        <th>Licenced to:</th>
                        <td>
                            <?php echo licence( "licenceto" ); ?>
                        </td> 
                    </tr>
                    <tr>
                        <th>Purchase date:</th>
                        <td>
                            <?php
                            if( licence( "purchasedate" ) == "trial" ) {
                                echo "<em>N/A</em>";
                            } else {
                                echo date( "d/m/Y" , strtotime( licence( "purchasedate" ) ) );
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Last activation:</th>
                        <td>
                            <?php
                            if( licence( "lastactivation" ) == "trial" ) {
                                echo "<em>N/A</em>";
                            } else {
                                echo date( "d/m/Y H:i" , strtotime( licence( "lastactivation" ) ) );
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Next activation:</th>
                        <td>
                            <?php
                            if( licence( "nextactivation" ) == "trial" ) {
                                echo "<em>N/A</em>";
                            } else {
                                echo date( "d/m/Y H:i" , strtotime( licence( "nextactivation" ) ) );
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <p>&nbsp;</p>
        <?php
        $dialog = "
            Are you sure you want to remove your licence and revert to trial mode?
            <input type='hidden' name='deactivate' value='1'>
        ";
        modal( "deactivate" , "Remove Licence" , $dialog , "Yes No" );
        modalButton_red( "deactivate" , "Remove licence" );
        ?>
    </div>
</div>