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
        <h1>Backup</h1>
        <hr />
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="alert alert-info">
            It is recomended to take reqular backups of your database. This can be done by clicking the backup button below. <br />
            Backups are stored in your user directory. It is recomended these are moved to a different storage media, otherwise its a pointless backup. <br />
            <br />
            To restore a backup, extract the backup zip file in your user directory, overriding any existing files. <br />
            <br />
            You're user directory is: <?php echo usrPath; ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <a href="index.php?l=backup&backup" class="btn btn-primary">Backup</a>
        <?php
        if( isset( $_GET['backup'] ) ) {
            // Starting off
            $backup = array();

            // Loop
            foreach( $tables as $table ) {
                $backup[$table] = array();
                $get = $db->query( "SELECT * FROM `$table`" );
                while( $row = $get->fetch( PDO::FETCH_ASSOC ) ) {
                    $backup[$table][] = $row;
                }
            }
            // Save the file
            $filename = usrPath . "/" . str_replace( " " , "-" , company( 'name' ) ) . "_" . date( "YmdHi" ) . ".json";
            $save = fopen( $filename , "w" );
            $json = json_encode( $backup );
            fwrite( $save , $json );
            fclose( $save );
            $updateLastBackup = $db->prepare( "UPDATE `company` SET `lastbackup` =:today WHERE `id` > 0 " );
            $updateLastBackup->execute( [ ':today' => date( "Y-m-d H:i" ) ] );

            $ftp = true;
            if( company( "ftp_backup" ) == 1 ) {
                $dest_file = company( "ftp_dir" ) .  str_replace( " " , "-" , company( 'name' ) ) . "_" . date( "YmdHi" ) . ".json";
                $ftp = ftp_connect( company( "ftp_host" ) , company( "ftp_port" ) , 100 );
                ftp_login( $ftp , company( "ftp_username" ) , company( "ftp_password" ) );
                $ret = ftp_nb_put( $ftp, $dest_file, $filename, FTP_BINARY, FTP_AUTORESUME);
                $ftp_upload = true;
                ftp_close( $ftp );
            }
            $saved = true;

        }
        ?>
    </div>
</div>

<div class="row">&nbsp;</div>
<div class="row">
    <div class="col">
        <?php
        if( isset( $saved ) ) {
            echo "<div class='alert alert-success'>Backup created locally!</div>";
        }
        if( isset( $ftp_upload ) ) {
            echo "<div class='alert alert-success'>Backup created on FTP!</div>";
        }
        ?>
        <div class="card">
            <div class="card-header"><strong>Existing Backups:</strong></div>
            <div class="card-body">
                <ul>
                    <?php
                    $files = scandir( usrPath );
                    foreach( $files as $file ) {
                        $bang = explode( "." , $file );
                        if( $file !== "." && $file !== ".." && $bang[1] == "json" ) {
                            echo "<li>" . $file . "</li>";
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>