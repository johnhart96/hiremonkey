<div class="row">
    <div class="col">
        <h1>Invoice Pile</h1>
        <hr />
    </div>
</div>
<div class="row">
    <div class="col">
        <table class="table table-bordered table-striped">
            <tr>
                <th>Job#</th>
                <th>Name</th>
                <th>Customer</th>
            </tr>
            <?php
            $getUnInvoiced = $db->query( "SELECT * FROM `jobs` WHERE `jobType` ='order' AND `invoiced` =0 AND `complete` =1" );
            while( $job = $getUnInvoiced->fetch( PDO::FETCH_ASSOC ) ) {
                echo "<tr>";
                echo "<td><a href='index.php?l=invoicing_view&id=" . $job['id'] . "'>" . $job['id'] . "</a></td>";
                echo "<td><a href='index.php?l=invoicing_view&id=" . $job['id'] . "'>" . $job['name'] . "</a></td>";
                echo "<td>" . customer( $job['customer'] ) . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</div>