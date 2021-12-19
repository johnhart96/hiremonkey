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
        <h1>Invoice Pile</h1>
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
                <th>&nbsp;</th>
            </tr>
            <?php
            $getUnInvoiced = $db->query( "SELECT * FROM `jobs` WHERE `jobType` ='order' AND `invoiced` =0 AND `complete` =1" );
            while( $job = $getUnInvoiced->fetch( PDO::FETCH_ASSOC ) ) {
                echo "<tr>";
                echo "<td>" . $job['id'] . "</td>";
                echo "<td>" . $job['name'] . "</td>";
                echo "<td>" . customer( $job['customer'] ) . "</td>";
                echo "<td width='1'><a href='index.php?l=invoicing_view&id=" . $job['id'] . "' class='btn btn-primary'>View</a></td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</div>