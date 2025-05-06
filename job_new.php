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
if( isset( $_POST['submit'] ) ) {
    $name = filter_var( $_POST['name'] , FILTER_UNSAFE_RAW );
    $startDate = filter_var( $_POST['startdate'] , FILTER_UNSAFE_RAW );
    $endDate = filter_var( $_POST['enddate'] , FILTER_UNSAFE_RAW );
    $customer = filter_var( $_POST['customer'] , FILTER_SANITIZE_NUMBER_INT );

    $insert = $db->prepare( "INSERT INTO `jobs` (`name`,`startDate`,`endDate`,`customer`) VALUES(:jobName,:startDate,:endDate,:customerID)" );
    $insert->execute( [ ':jobName' => $name , ':startDate' => $startDate , ':endDate' => $endDate , ':customerID' => $customer ] );

    $getLast = $db->query( "SELECT * FROM `jobs` ORDER BY `id` DESC LIMIT 1" );
    $last = $getLast->fetch( PDO::FETCH_ASSOC );

    $id = $last['id'];
    go( "index.php?l=job_view&id=" . $id );
}
?>
<div class="row">
    <div class="col">
        <h1>New Job:</h1>
        <hr />
        <?php
        if( trial() ) {
            if( entry_count( "jobs" ) >= 10 ) {
                echo "<div class='alert alert-danger'><strong>Error:</strong> You can only have 10 jobs in trial mode! <a href='index.php'>Return?</a></div>";
            } else {
                $left = 10 - entry_count( "jobs" );
                echo "<div class='alert alert-warning'><strong>Warning:</strong> You can only add " . $left . " more jobs while in trial mode!</div>";
            }
        }
        ?>
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php?l=jobs_browse">Jobs</a></li>
                <li class="breadcrumb-item active" aria-current="page">New</li>
            </ol>
        </nav>
    </div>
</div>
<form method="post">
    <div class="row">
        <div class="col">
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Name:</span></div>
                <input type="text" required autofocus class="form-control" name="name">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Customer:</span></div>
                <select name="customer" required class="form-control">
                    <option selected disabled></option>
                    <?php
                    $getCustomers = $db->query( "SELECT * FROM `customers` WHERE `hold` =0" );
                    while( $customer = $getCustomers->fetch( PDO::FETCH_ASSOC ) ) {
                        echo "<option value='" . $customer['id'] . "'>" . $customer['name'] . "</option>";
                    }
                    ?>
                </select>   
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Start date:</span></div>
                <input type="text" class="form-control" name="startdate" placeholder="YYYY-MM-YYYY" value="<?php echo date( "Y-m-d"); ?>">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">End date:</span></div>
                <input type="text" class="form-control" name="enddate" placeholder="YYYY-MM-YYYY" value="<?php echo date( "Y-m-d"); ?>">
            </div>
            <p>&nbsp;</p>
            <button type="text" name="submit" class="btn btn-success">Create</button>
        </div>
    </div>
</form>