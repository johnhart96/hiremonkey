<div class="row">
    <div class="col">
        <h1>Dashboard</h1>
        <hr />
        <?php
        if( BUILD_STATUS == "alpha" ) {
            echo "<div class='alert alert-warning'><strong>WARNING:</strong> You are using an Alpha build!</div>";
        }
        if( BUILD_STATUS == "beta" ) {
            echo "<div class='alert alert-warning'><strong>WARNING:</strong> You are using an Beta build!</div>";
        }
        ?>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header"><strong>Quotes:</strong></div>
            <div class="card-body">
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
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header"><strong>Dispatched Jobs:</strong></div>
            <div class="card-body">
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
    </div>
</div>
<div class="row">&nbsp;</p>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header"><strong>Overdue returns:</strong></div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th>Job#</th>
                        <th>Name</th>
                        <th>Customer</th>
                        <th>Due back</th>
                        <th width='1'></th>
                    </tr>
                    <?php
                    $getOverdue = $db->prepare( "SELECT * FROM `jobs` WHERE `complete` =0 AND `enddate` < :today" );
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
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header"><strong>Jobs to be invoiced</strong></div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th>Job#</th>
                        <th>Name</th>
                        <th>Customer</th>
                        <th width='1'></th>
                    </tr>
                    <?php
                    $getToBeInvoiced = $db->query( "SELECT * FROM `jobs` WHERE `invoiced` =0 AND `jobType` ='order'" );
                    while( $job = $getToBeInvoiced->fetch( PDO::FETCH_ASSOC ) ) {
                        echo "<tr>";
                        echo "<td>" . $job['id'] . "</td>";
                        echo "<td>" . $job['name'] . "</td>";
                        echo "<td>" . customer( $job['customer'] ) . "</td>";
                        echo "<td><a href='index.php?l=job_view&id=" . $job['id'] . "' class='btn btn-primary'>View</a></td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>
