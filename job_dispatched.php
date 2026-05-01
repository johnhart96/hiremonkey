<div class="row">
    <div class="col">
        <h1>Dispatched Jobs:</h1>
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
            $getDispatchedJobs = $db->query( "SELECT * FROM `jobs_lines` WHERE `dispatch` =1 AND `return` =0" );
            $jobs = array();
            while( $line = $getDispatchedJobs->fetch( PDO::FETCH_ASSOC ) ) {
                array_push( $jobs , $line['job'] );
            }
            $jobs = array_unique( $jobs );
            $getJob = $db->prepare( "SELECT * FROM `jobs` WHERE `id` =:jobID" );
            foreach( $jobs as $job ) {
                echo "<tr>";
                $getJob->execute( [ ':jobID' => $job ] );
                $fetch = $getJob->fetch( PDO::FETCH_ASSOC );
                echo "<td>" . $job . "</td>";
                echo "<td>" . $fetch['name'] . "</td>";
                echo "<td>" . customer( $fetch['customer'] ) . "</td>";
                echo "<td>" . date( "d/m/Y" , strtotime( $fetch['enddate'] ) ) . "</td>";
                echo "<td><a href='index.php?l=job_view&id=" . $job . "' class='btn btn-primary'>View</a></td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</div>