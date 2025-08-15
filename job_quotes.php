<div class="row">
    <div class="col">
        <h1>Quotes:</h1>
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
                <th width='1'></th>
            </tr>
            <?php
            $getQuotes = $db->query( "SELECT * FROM `jobs` WHERE `jobType` ='quote'" );
            while( $quote = $getQuotes->fetch( PDO::FETCH_ASSOC ) ) {
                echo "<tr>";
                echo "<td>" . $quote['id'] . "</td>";
                echo "<td>" . $quote['name'] . "</td>";
                echo "<td>" . customer( $quote['customer'] ) . "</td>";
                echo "<td>";
                echo "<a href='index.php?l=job_view&id=" . $quote['id'] . "' class='btn btn-primary'>View</a>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</div>