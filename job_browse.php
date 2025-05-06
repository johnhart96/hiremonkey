<?php
/**
 *
 * This source code is subject to copyright.
 * Viewing, distributing, editing or extracting this source code will result in licence violation and/or legal action
 *
 * 
 * @package    HireMonkey
 * @author     John Hart
 * @copyright  2021 John Hart
 * @license    https://www.hiremonkey.app/licence.php
 */
?>
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
                <input autofocus name="search" class="form-control" placeholder="Little shop of horrors" value="<?php if( isset( $_POST['search'] ) ) { echo filter_var( $_POST['search'] , FILTER_UNSAFE_RAW ); } ?>" >
                <select name="filter" class="form-control">
                    <?php
                    function checked( $ch ) {
                        if( isset( $_POST['filter'] ) ) {
                            $filter = filter_var( $_POST['filter'] , FILTER_UNSAFE_RAW );
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
            $filter = filter_var( $_POST['filter'] , FILTER_UNSAFE_RAW );
            $search = filter_var( $_POST['search'] , FILTER_UNSAFE_RAW );
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
        <table class="table table-dark table-bordered table-striped" id="jobs">
            <thead>
                <tr>
                    <th>Job#</th>
                    <th>Name</th>
                    <th>Customer</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Type</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                while( $job = $getJobs->fetch( PDO::FETCH_ASSOC ) ) {
                    echo "<tr>";
                    echo "<td>" . $job['id'] . "</td>";
                    echo "<td>" . $job['name'] . "</td>";
                    echo "<td><a href='index.php?l=customer_view&id=" . $job['customer'] . "'>" . customer( $job['customer'] ) . "</a></td>";
                    echo "<td>" . $job['startdate'] . "</td>";
                    echo "<td>" . $job['enddate'] . "</td>";
                    echo "<td>" . ucfirst( $job['jobType'] ) . "</td>";
                    echo "<td width='1'><a href='index.php?l=job_view&id=" . $job['id'] . "' class='btn btn-primary'>View</a></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>