<?php
if( isset( $_POST['submit'] ) ) {
    $host = filter_var( $_POST['host'] , FILTER_SANITIZE_STRING );
    $username = filter_var( $_POST['username'] , FILTER_SANITIZE_STRING );
    $password = filter_var(  $_POST['password'] , FILTER_SANITIZE_STRING );
    $dbname = filter_var( $_POST['dbname'] , FILTER_SANITIZE_STRING );

    $insert = $db->query( "INSERT INTO `remote` (`host`,`username`,`password`,`dbname`) VALUES(:host,:username,:passwd,:dbname)" );
    $insert->execute( [ ':host' => $host , ':username' => $username , ':passwd' => $password , ':dbname' => $dbname ] );
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
            <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php?l=settings_db">Database</a></li>
            <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=settings_licence">Licence</a></li>
            <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=settings_sloc">Storage Locations</a></li>
        </ul>
    </div>
</div>
<div class="row">&nbsp;</div>
<div class="row">
    <div class="col">
        <div class="alert alert-warning"><strong>WARNING:</strong> Adding a remote database will disconnect your local database. Make sure your details are correct before saving!</div>
    </div>
</div>
<form method="post">
    <?php
    $getExternalDB = $db->query( "SELECT * FROM `remote` ORDER BY `id` ASC LIMIT 1" );
    $fetch = $getExternalDB->fetch( PDO::FETCH_ASSOC );
    if( ! isset( $fetch['host'] ) ) {
        $fetch = array(
            "host" => NULL,
            "username" => NULL,
            "password" => NULL,
            "dbname" => NULL
        );
    }
    ?>
    <div class="row">
        <div class="col">
            <h2>MySQL or MariaDB:</h2>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">DB Host:</span></div>
                <input name="host" type="text" class="form-control" value="<?php echo $fetch['host']; ?>">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">DB Username:</span></div>
                <input name="username" type="text" class="form-control" value="<?php echo $fetch['username']; ?>">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">DB Password:</span></div>
                <input name="password" type="password" class="form-control" value="<?php echo $fetch['password']; ?>">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">DB Name:</span></div>
                <input name="dbname" type="text" class="form-control" value="<?php echo $fetch['dbname']; ?>">
            </div>
            <p>&nbsp;</p>
            <button type="submit" name="submit" class="btn btn-success">Save</button>
        </div>
    </div>
</form>