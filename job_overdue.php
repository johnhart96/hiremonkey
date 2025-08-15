<div class="row">
    <div class="col">
        <h1>Overdue Returns</h1>
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
                <th>Due back</th>
                <th width='1'></th>
            </tr>
            <?php
            $getOverdue = $db->prepare( "SELECT * FROM `jobs` WHERE `complete` =0 AND `enddate` < :today AND `jobType` ='order'" );
            $getOverdue->execute( [ ':today' => date( "Y-m-d" ) ] );
            while( $job = $getOverdue->fetch( PDO::FETCH_ASSOC ) ) {
                echo "<tr>";
                echo "<td>" . $job['id'] . "</td>";
                echo "<td>" . $job['name'] . "</td>";
                echo "<td>" . customer( $job['customer'] ) . "</td>";
                echo "<td>" . date( "d/m/Y" , strtotime( $job['enddate'] ) ) . "</td>";
                echo "<td><a href='index.php?l=job_view&id=" . $job['id'] . "' class='btn btn-danger'>View</a></td>";
                echo "</tr>";  
            }
            ?>
        </table>
    </div>
</div>