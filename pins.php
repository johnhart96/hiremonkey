<div class="row">
    <div class="col">
        <h1>Pins</h1>
        <hr />
    </div>
</div>
<?php
if( isset( $_GET['delete'] ) ) {
    $del = $db->prepare( "DELETE FROM pins WHERE id =:id" );
    $del->execute( [ ':id' => $_GET['delete'] ] );
    go( "index.php?l=pins" );
}
?>
<div class="row">
    <div class="col">
        <table class="table table-bordered table-stripe">
            <thead>
                <tr>
                    <th>Pins</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $getPins = $db->query( "SELECT * FROM pins" );
                while( $pin = $getPins->fetch( PDO::FETCH_ASSOC ) ) {
                    echo "<tr>";
                    echo "<td>" . $pin['label'] . "</td>";
                    echo "<td width='1'><a href='index.php?l=pins&delete=" . $pin['id'] . "' class='btn btn-sm btn-danger'>X</a></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>