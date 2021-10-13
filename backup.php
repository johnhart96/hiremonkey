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

            // Tables
            $tables = array(
                'categories',
                'company',
                'customers',
                'customers_addresses',
                'customers_contacts',
                'jobs',
                'jobs_cat',
                'jobs_lines',
                'kit',
                'kit_accessories',
                'kit_stock',
                'sloc'
            );

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
        }

        ?>
    </div>
</div>

<div class="row">&nbsp;</div>
<div class="row">
    <div class="col">
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