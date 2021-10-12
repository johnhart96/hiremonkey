<div class="row">
    <div class="col">
        <h1>Browse Jobs:</h1>
        <hr />
    </div>
</div>
<div class="row">
    <div class="col">
        <form method="post" id="search">
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Search:</span></div>
                <input autofocus name="search" class="form-control" placeholder="Little shop of horrors" value="<?php if( isset( $_POST['search'] ) ) { echo filter_var( $_POST['search'] , FILTER_SANITIZE_STRING ); } ?>" >
                <select name="filter" class="form-control">
                    <?php
                    function checked( $ch ) {
                        if( isset( $_POST['filter'] ) ) {
                            $filter = filter_var( $_POST['filter'] , FILTER_SANITIZE_STRING );
                            if( $ch == $filter ) {
                                return "selected";
                            }
                        } else {
                            if( $ch == "active" ) {
                                return "selected";
                            }
                        }
                    }
                    echo "<option value='active' " . checked( "active" ) . ">Active</option>"; 
                    echo "<option value='lost' " . checked( "lost" ) . ">Lost</option>"; 
                    echo "<option value='complete' " . checked( "complete" ) . ">Complete</option>"; 
                    ?>
                </select>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>

        </form>
    </div>
</div>
<div class="row">
    <div class="col">
        <?php
        if( isset( $_POST['search'] ) ) {
            $filter = filter_var( $_POST['filter'] , FILTER_SANITIZE_STRING );
            $search = filter_var( $_POST['search'] , FILTER_SANITIZE_STRING );
            if( $filter == "active" ) {
                $getJobs = $db->prepare( "SELECT * FROM `jobs` WHERE `complete` =0 AND `lost` =0 AND instr(`name`,:searchTerm)" );
            } else if( $filter == "lost" ) {
                $getJobs = $db->prepare( "SELECT * FROM `jobs` WHERE `lost` =1 AND instr(`name`,:searchTerm)" );
            } else if( $filter == "complete" ) {
                $getJobs = $db->prepare( "SELECT * FROM `jobs` WHERE `complete` =1 AND instr(`name`,:searchTerm)" );
            }
            $getJobs->execute( [ ':searchTerm' => $search ] );
        } else {
            $getJobs = $db->query( "SELECT * FROM `jobs` WHERE `complete` =0 AND `lost` =0" );
        }
        ?>
        <table class="table table-bordered table-stripe">
            <tr>
                <th>Job#</th>
                <th>Name</th>
                <th>Customer</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Type</th>
                <th></th>
            </tr>
            <?php
            while( $job = $getJobs->fetch( PDO::FETCH_ASSOC ) ) {
                echo "<tr>";
                echo "<td>" . $job['id'] . "</td>";
                echo "<td>" . $job['name'] . "</td>";
                echo "<td>" . customer( $job['customer'] ) . "</td>";
                echo "<td>" . $job['startdate'] . "</td>";
                echo "<td>" . $job['enddate'] . "</td>";
                echo "<td>" . ucfirst( $job['jobType'] ) . "</td>";
                echo "<td width='1'><a href='index.php?l=job_view&id=" . $job['id'] . "' class='btn btn-primary'>View</a></td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</div>