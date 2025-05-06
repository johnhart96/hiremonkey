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
if( isset( $_POST['preview'] ) ) {
    $format = filter_var( $_POST['format'] , FILTER_UNSAFE_RAW );
    $include = "inc/import_customer_" . $format . ".php";

    // Upload
    $target_dir = usrPath . "/";
    $target_file = $target_dir . basename( $_FILES['file']['name'] );
    $uploadOK = TRUE;

    if( file_exists( $target_file ) ) {
        unlink( $target_file );
    }

    if( $_FILES['file']['size'] > 5000 ) {
        echo "<div class='alert alert-danger'><strong>Error:</strong> File size it too large!</div>";
        $uploadOK = FALSE;
    }

    if( $uploadOK ) {
        if( ! move_uploaded_file( $_FILES['file']['tmp_name'] , $target_file ) ) {
            echo "<div class='alert alert-danger'><strong>Error:</strong> Unable to open the file!</div>";
        }
    }

    // Custom import
    require $include;
    unlink( $target_file );
}
?>
<div class="row">
    <div class="col">
        <h1>Contact Import:</h1>
        <hr />
        <?php
        if( isset( $done ) ) {
            echo "<div class='alert alert-success'>Import done!</div>";
        }
        ?>
    </div>
</div>
<form method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col">
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Format:</span></div>
                <select name="format" class="form-control">
                    <option value='sage'>Sage 50 Accounts Essentials</option>
                </select>
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">File:</span></div>
                <input type="file" class="form-control" name="file">
            </div>
            <p>&nbsp;</p>
            <button type="submit" name="preview" class="btn btn-primary">Preview</button> 
        </div>
    </div>
</form>
