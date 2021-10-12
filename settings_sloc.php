<?php
// Submit edit sloc
if( isset( $_POST['submitEditSloc'] ) ) {
    $name = filter_var( $_POST['name'] , FILTER_SANITIZE_STRING );
    $address_line1 = filter_var( $_POST['address_line1'] , FILTER_SANITIZE_STRING );
    $address_line2 = filter_var( $_POST['address_line2'] , FILTER_SANITIZE_STRING );
    $town = filter_var( $_POST['town'] , FILTER_SANITIZE_STRING );
    $postcode = filter_var( $_POST['postcode'] , FILTER_SANITIZE_STRING );

    $slocID = filter_var( $_POST['submitEditSloc'] , FILTER_SANITIZE_NUMBER_INT );

    $update = $db->prepare( "UPDATE `sloc` SET `name` =:slocName, `address_line1` =:address_line1, `address_line2` =:address_line2, `town` =:town, `postcode` =:postcode WHERE `id` =:slocID" );
    $update->execute( [ ':slocName' => $name , ':address_line1' => $address_line1 , ':address_line2' => $address_line2 , ':town' => $town , ':postcode' => $postcode , ':slocID' => $slocID ] );
    $saved = TRUE;
}
// Submit new sloc
if( isset( $_POST['new'] ) ) {
    $name = filter_var( $_POST['name'] , FILTER_SANITIZE_STRING );
    $address_line1 = filter_var( $_POST['address_line1'] , FILTER_SANITIZE_STRING );
    $address_line2 = filter_var( $_POST['address_line2'] , FILTER_SANITIZE_STRING );
    $town = filter_var( $_POST['town'] , FILTER_SANITIZE_STRING );
    $postcode = filter_var( $_POST['postcode'] , FILTER_SANITIZE_STRING );

    $new = $db->prepare( "INSERT INTO `sloc` (`name`,`address_line1`,`address_line2`,`town`,`postcode`) VALUES(:slocName,:address_line1,:address_line2,:town,:postcode)" );
    $new->execute( [ ':slocName' => $name , ':address_line1' => $address_line1 , ':address_line2' => $address_line2 , ':town' => $town , ':postcode' => $postcode ] );
    $saved = true;
}
// Submit delete sloc
if( isset( $_POST['submitDeleteSloc'] ) ) {
    $del = filter_var( $_POST['submitDeleteSloc'] , FILTER_SANITIZE_NUMBER_INT );
    $delete = $db->prepare( "DELETE FROM `sloc` WHERE `id` =:id" );
    $delete->execute( [ ':id' => $del ] );
    $saved = true;
}
?>
<div class="row">
    <div class="col">
        <h1>Settings:</h1>
        <hr />
        <?php
        if( isset( $saved ) ) {
            echo "<div class='alert alert-success'>Changes saved!</div>";
        }
        ?>
    </div>
</div>
<div class="row">
    <div class="col">
        <ul class="nav nav-tabs">
            <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=settings">Company</a></li>
            <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=settings_db">Database</a></li>
            <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=settings_licence">Licence</a></li>
            <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php?l=settings_sloc">Storage Locations</a></li>
        </ul>
    </div>
</div>
<div class="row">&nbsp;</div>

<div class="row">
    <div class="col">
        <table class="table table-bordered table-stripe">
            <tr>
                <th>Name</th>
                <th>Address Line 1</th>
                <th>Address Line 2</th>
                <th>Town</th>
                <th>Postcode</th>
                <th colspan="2"></th>
            </tr>
            <?php
            $getSloc = $db->query( "SELECT * FROM `sloc`" );
            while( $sloc = $getSloc->fetch( PDO::FETCH_ASSOC ) ) {
                echo "<tr>";
                echo "<td>" . $sloc['name'] . "</td>";
                echo "<td>" . $sloc['address_line1'] . "</td>";
                echo "<td>" . $sloc['address_line2'] . "</td>";
                echo "<td>" . $sloc['town'] . "</td>";
                echo "<td>" . $sloc['postcode'] . "</td>";
                // Edit
                echo "<td width='1'>";
                $modal = "edit_" . $sloc['id'];
                $dialog = "
                    <div class='input-group'>
                        <div class='input-group-prepend'><span class='input-group-text'>Name:</span></div>
                        <input type='text' name='name' class='form-control' value='" . $sloc['name'] . "'>
                    </div>
                    <div class='input-group'>
                        <div class='input-group-prepend'><span class='input-group-text'>Address Line 1:</span></div>
                        <input type='text' name='address_line1' class='form-control' value='" . $sloc['address_line1'] . "'>
                    </div>
                    <div class='input-group'>
                        <div class='input-group-prepend'><span class='input-group-text'>Address Line 2:</span></div>
                        <input type='text' name='address_line2' class='form-control' value='" . $sloc['address_line2'] . "'>
                    </div>
                    <div class='input-group'>
                        <div class='input-group-prepend'><span class='input-group-text'>Town:</span></div>
                        <input type='text' name='town' class='form-control' value='" . $sloc['town'] . "'>
                    </div>
                    <div class='input-group'>
                        <div class='input-group-prepend'><span class='input-group-text'>Postcode:</span></div>
                        <input type='text' name='postcode' class='form-control' value='" . $sloc['postcode'] . "'>
                    </div>
                    <input type='hidden' name='submitEditSloc' value='" . $sloc['id'] . "'>
                ";
                modal( $modal , "Edit storage location" , $dialog , "Save Cancel" );
                modalButton( $modal , "Edit" );
                echo "</td>";
                // Delete
                echo "<td width='1'>";
                $modal = "delete_" . $sloc['id'];
                modalButton_red( $modal , "Delete" );
                $dialog = "
                    Are you sure you want to delete this storage location?
                    <input type='hidden' name='submitDeleteSloc' value='" . $sloc['id'] . "'>
                ";
                modal( $modal , "Delete?" , $dialog , "Yes No" );
                echo "</td>";
                echo "</tr>";
            }
            ?>
            <tr>
                <td colspan="7" align="center">
                    <?php
                    modalButton_green( "new" , "New" );
                    $dialog = "
                        <div class='input-group'>
                            <div class='input-group-prepend'><span class='input-group-text'>Name:</span></div>
                            <input type='text' name='name' class='form-control' value=''>
                        </div>
                        <div class='input-group'>
                            <div class='input-group-prepend'><span class='input-group-text'>Address Line 1:</span></div>
                            <input type='text' name='address_line1' class='form-control' value=''>
                        </div>
                        <div class='input-group'>
                            <div class='input-group-prepend'><span class='input-group-text'>Address Line 2:</span></div>
                            <input type='text' name='address_line2' class='form-control' value=''>
                        </div>
                        <div class='input-group'>
                            <div class='input-group-prepend'><span class='input-group-text'>Town:</span></div>
                            <input type='text' name='town' class='form-control' value=''>
                        </div>
                        <div class='input-group'>
                            <div class='input-group-prepend'><span class='input-group-text'>Postcode:</span></div>
                            <input type='text' name='postcode' class='form-control' value=''>
                        </div>
                        <input type='hidden' name='new'>
                    ";
                    modal( "new" , "New storage location" , $dialog , "Save Cancel" );
                    ?>
                </td>
            </tr>
        </table>
    </div> 
</div>
