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
$id = filter_var( $_GET['id'] , FILTER_SANITIZE_NUMBER_INT );

// requires
require 'inc/job_submits.php';
require 'inc/job_select.php';

?>
<div class="row">
    <div class="col">
        <h1>Job Sheet:</h1>
        <hr />
        <?php
        if( isset( $saved ) ) {
            echo "<div class='alert alert-success'>Saved!</div>";
        }
        ?>
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php?l=job_browse">Jobs</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $job['name']; ?></li>
            </ol>
        </nav>
    </div>
</div>
<?php
require 'inc/job_header.php';
echo "<div class='row'>&nbsp;</div>";
?>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header" id="order-head"><strong>Order:</strong></div>
            <div class="card-body" id="order-body">
            <ul class="nav nav-tabs">
                <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=job_view&id=<?php echo $id; ?>">Overview</a></li>
                <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=job_costs&id=<?php echo $id; ?>">Costs</a></li>
                <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=job_service&id=<?php echo $id; ?>">Services</a></li>
                <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=job_subhire&id=<?php echo $id; ?>">Subhire</a></li>
                <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php?l=job_shipping&id=<?php echo $id; ?>">Shipping</a></li>
            </ul>
                <?php
                if( ! empty( $error ) ) {
                    echo "<p>&nbsp;</p>";
                    echo "<div class='alert alert-danger'>" . $error . "</div>";
                }
                if( $job['jobType'] == "quote" ) {
                    echo "<div class='alert alert-warning'>Only confirmed jobs can be shipped. You must change the type of this job in order to ship it!</div>";
                } else {
                    require 'inc/job_shipping.php';
                }
                ?>
            </div>
        </div>
    </div>
</div>