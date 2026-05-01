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
        <div class="card" style="background-color:#13ba00; height: 220px;">
            <div class="card-body">
                <h3 class="text-center">Quotes</h3>
                <?php
                $getQuotes = $db->query( "SELECT * FROM `jobs` WHERE `jobType` ='quote'" );
                $count = 0;
                while( $row = $getQuotes->fetch( PDO::FETCH_ASSOC ) ) {
                    $count ++;
                }
                echo "<p class='card-hero text-center'><a href='?l=job_quotes'>" . $count . "</a></p>";
                ?>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card" style="background-color: #f5b907; height: 220px;">
            <div class="card-body">
                <h3 class="text-center">Dispatched jobs</h3>
                <?php
                $getDispatchedJobs = $db->query( "SELECT * FROM `jobs_lines` WHERE `dispatch` =1 AND `return` =0" );
                $jobs = array();
                while( $line = $getDispatchedJobs->fetch( PDO::FETCH_ASSOC ) ) {
                    array_push( $jobs , $line['job'] );
                }
                $jobs = array_unique( $jobs );
                echo "<p class='card-hero text-center'><a href='?l=job_dispatched'>" . count( $jobs ) . "</a></p>";
                ?>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card" style="background-color: #ff0000; height: 220px;">
            <div class="card-body">
                <h3 class="text-center">Overdue returns</p>
                <?php
                $getOverdue = $db->prepare( "SELECT * FROM `jobs` WHERE `complete` =0 AND `enddate` < :today AND `jobType` ='order'" );
                $getOverdue->execute( [ ':today' => date( "Y-m-d" ) ] );
                $count = 0;
                while( $row = $getOverdue->fetch( PDO::FETCH_ASSOC ) ) {
                    $count ++;
                }
                echo "<p class='text-center card-hero'><a href='?l=job_overdue'>" . $count . "</a></p>";
                ?>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card" style="background-color: #0077ff; height: 220px;">
            <div class="card-body">
                <h3 class="text-center">Invoice pile</h3>
                <?php
                $getToBeInvoiced = $db->query( "SELECT * FROM `jobs` WHERE `invoiced` =0 AND `jobType` ='order' AND `complete` =1" );
                $count = 0;
                while( $row = $getToBeInvoiced->fetch( PDO::FETCH_ASSOC ) ) {
                    $count ++;
                }
                echo "<p class='card-hero text-center'><a href='?l=invoicing'>" . $count . "</a></p>";
                ?>
            </div>
        </div>
    </div>
</div>
<div class="row">&nbsp;</div>
<div class="row">
    <div class="col">
        <div class="card" style="background-color: #9d00ff; height: 220px;">
            <div class="card-body">
                <h3 class="text-center">Repairs</h3>
                <?php
                $getRepairs = $db->query( "SELECT * FROM `kit_repairs` WHERE `complete` =0" );
                $count = 0;
                while( $row = $getRepairs->fetch( PDO::FETCH_ASSOC ) ) {
                    $count ++;
                }
                echo "<p class='card-hero text-center'><a href='?l=repairs'>" . $count . "</a></p>";
                ?>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card" style="background-color: #00adad; height: 220px;">
            <div class="card-body">
                <h3 class="text-center">People</h3>
                <?php
                $getContacts = $db->query( "SELECT * FROM `customers`" );
                $count = 0;
                while( $row = $getContacts->fetch( PDO::FETCH_ASSOC ) ) {
                    $count ++;
                }
                echo "<p class='card-hero text-center'><a href='?l=customer_browse'>" . $count . "</a></p>";
                ?>
            </div>
        </div>
    </div>
</div>

<hr />

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header text-center"><strong>Going out today</strong></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <?php
                        $goingOutToday = $db->query("
                            SELECT id, name
                            FROM jobs
                            WHERE startdate = date('now');
                        ");
                        while( $job = $goingOutToday->fetch( PDO::FETCH_ASSOC ) ) {
                            echo "<tr>";
                            echo "<td>" . "<a style='color: #000' href='index.php?l=job_view&id=" . $job['id'] . "'>" . $job['name'] . "</a></td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header text-center"><strong>Coming back today</strong></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <?php
                        $goingOutToday = $db->query("
                            SELECT id, name
                            FROM jobs
                            WHERE enddate = date('now');
                        ");
                        while( $job = $goingOutToday->fetch( PDO::FETCH_ASSOC ) ) {
                            echo "<tr>";
                            echo "<td>" . "<a style='color: #000' href='index.php?l=job_view&id=" . $job['id'] . "'>" . $job['name'] . "</a></td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>