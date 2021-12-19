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
        <h1 id="jobtitle">Job Sheet:</h1>
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
        <div class="items">
            <div class="items-body" id="order-body">
                <ul class="nav nav-tabs">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php?l=job_view&id=<?php echo $id; ?>">Overview</a></li>
                    <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=job_costs&id=<?php echo $id; ?>">Costs</a></li>
                    <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=job_service&id=<?php echo $id; ?>">Services</a></li>
                    <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=job_subhire&id=<?php echo $id; ?>">Subhire</a></li>
                    <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=job_shipping&id=<?php echo $id; ?>">Shipping</a></li>
                </ul>
                <?php if( $job['complete'] == 0 ) { ?>
                    <form method="post">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <input style="width: 50px; text-align:center" type="text" class="form-control" value="1" name="qty" placeholder="Qty">
                            </div>
                            <input type="text" name="newitem" id="newitem" class="form-control" placeholder="New item...">
                            <div class="input-group-append">
                                <button type="submit" name="submitNewItem" class="btn btn-success">+</button>
                            </div>
                        </div>
                    </form>
                <?php } ?>
                <?php
                if( ! empty( $error ) ) {
                    echo "<p>&nbsp;</p>";
                    echo "<div class='alert alert-danger'>" . $error . "</div>";
                }
                require 'inc/job_items.php';
                ?>
            </div>
        </div>
    </div>
</div>
<!-- Line Edit -->
<?php
if( isset( $_GET['editline'] ) ) {
    echo "<p>&nbsp;</p>";
    echo "<div class='editline'>";
    echo "<div class='editline-header'><strong>Line Editor <a href='index.php?l=job_view&id=$id'>(X)</a></strong></div>";
    echo "<div class='editline-body'>";
    require 'inc/job_lineedit.php';
    echo "</div>";
    echo "</div>";
}
?>