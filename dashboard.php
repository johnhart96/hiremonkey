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
if( isset( $_POST['dismissWelcome'] ) ) {
    $dismiss = $db->query( "UPDATE `company` SET `welcome` =1 WHERE `id` > 0" );
}
?>
<div class="row">
    <div class="col">
        <h1>Dashboard</h1>
        <hr />
        <?php
        if( BUILD_STATUS == "alpha" ) {
            echo "<div class='alert alert-warning'><strong>WARNING:</strong> You are using an Alpha build!</div>";
        }
        if( BUILD_STATUS == "beta" ) {
            echo "<div class='alert alert-info'>You are using an Beta build!</div>";
        }
        ?>
    </div>
</div>
<?php if( company( "welcome" ) == 0 ) { ?>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header"><strong>Getting started:</strong></div>
                <div class="card-body">
                    <div class="alert alert-info">Here is a list of a few tasks to help you get started with HireMonkey</div>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Activate licence</th>
                            <td width='30%' align='center'>
                                <?php
                                if( trial() ) {
                                    echo "<a href='index.php?l=settings_licence' class='btn btn-info'>Action</a>";
                                } else {
                                    echo "Done";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Add company details</th>
                            <td width='30%' align='center'>
                                <?php
                                if( empty( company( 'address_line1' ) ) ) {
                                    echo "<a href='index.php?l=settings' class='btn btn-info'>Action</a>";
                                } else {
                                    echo "Done";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Add storage locations</th>
                            <td width='30%' align='center'>
                                <?php
                                if( entry_count( "sloc" ) == 0 ) {
                                    echo "<a href='index.php?l=settings_sloc' class='btn btn-info'>Action</a>";
                                } else {
                                    echo "Done";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Add categories</th>
                            <td width='30%' align='center'>
                                <?php
                                if( entry_count( "cats" ) == 0 ) {
                                    echo "<a href='index.php?l=cats' class='btn btn-info'>Action</a>";
                                } else {
                                    echo "Done";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Add equipment</th>
                            <td width='30%' align='center'>
                                <?php
                                if( entry_count( "kit" ) == 0 ) {
                                    echo "<a href='index.php?l=kit_new' class='btn btn-info'>Action</a>";
                                } else {
                                    echo "Done";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Add customers</th>
                            <td width='30%' align='center'>
                                <?php
                                if( entry_count( "customers" ) == 0 ) {
                                    echo "<a href='index.php?l=customer_new' class='btn btn-info'>Action</a>";
                                } else {
                                    echo "Done";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Create your first job</th>
                            <td width='30%' align='center'>
                                <?php
                                if( entry_count( "jobs" ) == 0 ) {
                                    echo "<a href='index.php?l=job_new' class='btn btn-info'>Action</a>";
                                } else {
                                    echo "Done";
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer">
                    <?php
                    modalButton( "dismisswelcome" , "Dismiss" );
                    $dialog = "
                        Are you sure you want to dismiss the welcome screen?
                        <input type='hidden' name='dismissWelcome'>
                    ";
                    modal( "dismisswelcome" , "Dismiss?" , $dialog , "Yes No" );
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">&nbsp;</div>
<?php } ?>
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
<div class="row">&nbsp;</div>
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
                    $getToBeInvoiced = $db->query( "SELECT * FROM `jobs` WHERE `invoiced` =0 AND `jobType` ='order' AND `complete` =1" );
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
<div class="row">&nbsp;</div>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header"><strong>Repairs:</strong></div>
            <div class="card-body">
                <table class="table table-bordered table-stripped">
                    <thead>
                        <tr>
                            <th>Repair#</th>
                            <th>Description</th>
                            <th>Start date</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $getRepairs = $db->query( "SELECT * FROM `kit_repairs` WHERE `complete` =0" );
                        while( $repair = $getRepairs->fetch( PDO::FETCH_ASSOC ) ) {
                            echo "<tr>";
                            echo "<td>" . $repair['id'] . "</td>";
                            echo "<td>" . $repair['description'] . "</td>";
                            echo "<td>" . date( "Y-m-d" , strtotime( $repair['startdate'] ) ) . "</td>";
                            echo "<td><a href='index.php?l=repairbench&id=" . $repair['id'] . "' class='btn btn-primary'>Open</a></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>