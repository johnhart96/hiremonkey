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
            // Get real path for our folder
            $rootPath = realpath( usrPath );

            // Initialize archive object
            $zip = new ZipArchive();
            $zip->open( usrPath . '/backup_' . date( "YmdHi" ) . ".zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);

            // Create recursive directory iterator
            /** @var SplFileInfo[] $files */
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($rootPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file)
            {
                // Skip directories (they would be added automatically)
                if (!$file->isDir())
                {
                    // Get real and relative path for current file
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootPath) + 1);
                    $bang = explode( "." , $filePath );
                    if( $bang[1] !== "zip" ) {
                        // Add current file to archive
                        $zip->addFile($filePath, $relativePath);
                    }
                }
            }

            // Zip archive will be created only after closing object
            $zip->close();
            $setLastBackup = $db->prepare( "UPDATE `company` SET `lastbackup` =:lastBackup WHERE `id` > 0" );
            $setLastBackup->execute( [ ':lastBackup' => date( "Y-m-d H:i" ) ] );
            echo "<div class='alert alert-success'>Backup created, check in " . usrPath . "</div>";
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
                        if( $file !== "." && $file !== ".." && $bang[1] == "zip" ) {
                            echo "<li>" . $file . "</li>";
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>