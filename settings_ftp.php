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

if( isset( $_POST['submitFTP'] ) ) {
    $ftp_host = filter_var( $_POST['ftp_host'] , FILTER_SANITIZE_STRING );
    $ftp_username = filter_var( $_POST['ftp_username'] , FILTER_SANITIZE_STRING );
    $ftp_password = filter_var( $_POST['ftp_password'] , FILTER_SANITIZE_STRING );
    $ftp_port = filter_var( $_POST['ftp_port'] , FILTER_SANITIZE_NUMBER_INT );
    $ftp_dir = filter_var( $_POST['ftp_dir'] , FILTER_SANITIZE_STRING );
    if( isset( $_POST['ftp_backup'] ) ) {
        $ftp_backup = 1;
    } else {
        $ftp_backup = 0;
    }

    $updateFTP = $db->prepare( "UPDATE `company` SET `ftp_host` =:ftp_host, `ftp_username` =:ftp_username, `ftp_password` =:ftp_password, `ftp_port` =:ftp_port, `ftp_dir` =:ftp_dir, `ftp_backup` =:ftp_backup WHERE `id` =1" );
    $updateFTP->execute( [ ':ftp_host' => $ftp_host , ':ftp_username' => $ftp_username , ':ftp_password' => $ftp_password , ':ftp_port' => $ftp_port , ':ftp_dir' => $ftp_dir , ':ftp_backup' => $ftp_backup ] );
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
            <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=settings_licence">Licence</a></li>
            <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=settings_sloc">Storage Locations</a></li>
            <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php?l=settings_ftp">FTP</a></li>
        </ul>
    </div>
</div>
<div class="row">&nbsp;</div>
<div class="row">
    <div class="col">
        <form method="post">
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Host:</div>
                <input class="form-control" name="ftp_host" value="<?php echo company( "ftp_host" ); ?>">
                <span class="input-group-text">:</span>
                <div class="input-group-append">
                    <input class="form-control" name="ftp_port" value="<?php echo company( "ftp_port"); ?>" placeholder="21">
                </div>
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Username:</div>
                <input class="form-control" name="ftp_username" value="<?php echo company( "ftp_username" ); ?>">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Password:</div>
                <input class="form-control" name="ftp_password" type="password" value="<?php echo company( "ftp_password" ); ?>">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Directory:</div>
                <input class="form-control" name="ftp_dir" value="<?php echo company( "ftp_dir" ); ?>" placeholder="/">
            </div>
            <?php
            if( company( "ftp_backup" ) == 1 ) {
                $checked = "checked";
            } else {
                $checked = "";
            }
            ?>
            &nbsp;<input type="checkbox" name="ftp_backup" <?php echo $checked; ?>>
            <label for="ftp_backup">Use FTP for Backup</label>

            <p>&nbsp;</p>
            <button type="submit" class="btn btn-success" name="submitFTP">Save</button>
        </form>

    </div>
</div>
