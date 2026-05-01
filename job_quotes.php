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
            </tr>
            <?php
            $getQuotes = $db->query( "SELECT * FROM `jobs` WHERE `jobType` ='quote'" );
            while( $quote = $getQuotes->fetch( PDO::FETCH_ASSOC ) ) {
                echo "<tr>";
                echo "<td><a style='color: #000;' href='index.php?l=job_view&id=" . $quote['id'] . "'>" . $quote['id'] . "</a></td>";
                echo "<td><a style='color: #000;'href='index.php?l=job_view&id=" . $quote['id'] . "'>" . $quote['name'] . "</a></td>";
                echo "<td>" . customer( $quote['customer'] ) . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</div>