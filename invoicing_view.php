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
$id = filter_var( $_GET['id'] , FILTER_UNSAFE_RAW );
// Submit
if( isset( $_POST['submitInvoice'] ) ) {
    $invoiced = filter_var( $_POST['invoiced'] , FILTER_SANITIZE_NUMBER_INT );
    $invoice_number = filter_var( $_POST['invoice_number'] , FILTER_UNSAFE_RAW );
    $update = $db->prepare( "UPDATE `jobs` SET `invoiced` =:invoiced , `invoice_number` =:invoiceNo WHERE `id` =:job" );
    $update->execute( [ ':invoiced' => $invoiced , ':invoiceNo' => $invoice_number , ':job' => $id ] );
}

// Current
$getJob = $db->prepare( "SELECT * FROM `jobs` WHERE `id` =:id LIMIT 1" );
$getJob->execute( [ ':id' => $id ] );
$job = $getJob->fetch( PDO::FETCH_ASSOC );
?>
<form method="post">
    <div class="row">
        <div class="col">
            <h1>Process Invoice</h1>
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?l=job_browse">Jobs</a></li>
                    <li class="breadcrumb-item"><a href="index.php?l=job_view&id=<?php echo $id; ?>"><?php echo $job['name']; ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Invoice</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header"><strong>Header</strong></div>
                <div class="card-body">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Status:</span></div>
                        <select name="invoiced" class="form-control">
                            <?php
                            switch( $job['invoiced'] ) {
                                case 0:
                                    echo "<option value='0' selected>Not invoiced</option>";
                                    echo "<option value='1'>Invoiced</option>";
                                    break;
                                case 1:
                                    echo "<option value='0'>Not invoiced</option>";
                                    echo "<option value='1' selected>Invoiced</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Invoice#:</span></div>
                        <input class="form-control" name="invoice_number" value="<?php echo $job['invoice_number']; ?>">
                    </div>
                    <p>&nbsp;</p>
                    <button type="submit" name="submitInvoice" class="btn btn-success">Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">&nbsp;</div>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header"><strong>Body</strong></div>
                <div class="card-body">
                    <div class="alert alert-info">
                        Invoice body serves to copy into your accounting package. Any changes to the text body will not be saved!
                    </div>
                    <?php
                    // Duration calc
                    $startDate = strtotime( $job['startdate'] );
                    $endDate = strtotime( $job['enddate'] );
                    $diff = $endDate - $startDate;
                    $years = floor( $diff / (365*60*60*24) );
                    $months = floor( ( $diff - $years * 365*60*60*24 ) / ( 30*60*60*24 ) ); 
                    $days = floor( ( $diff - $years * 365*60*60*24 - $months*30*60*60*24 ) / ( 60*60*24 ) );
                    $days ++;

                    $totalBill = 0.0;
                    // Body
                    echo "<textarea style='height: 400px; width: 100%;'>";
                    echo "Hire as per job " . $job['id'] . " (" . $job['name'] . ") from " . date( "d/m/Y" , strtotime( $job['startdate'] ) ) . " till " . date( "d/m/Y" , strtotime( $job['enddate'] ) ) . ":\n";
                    $getJobLines = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `job` =:jobID ORDER BY `id` ASC" );
                    $getJobLines->execute( [ ':jobID' => $id ] );
                    while( $line = $getJobLines->fetch( PDO::FETCH_ASSOC ) ) {
                        if( $line['linetype'] == "hire" or $line['linetype'] == "subhire" ) {
                            $total = (double)$line['price'] * (double)$line['discount'] * (int)$line['qty'] * $days;
                        } else {
                            $total = (double)$line['price'] * (double)$line['discount'] * (int)$line['qty'];
                        }
                        echo "    " . $line['qty'] . "x " . $line['itemName'] . " = " . company( 'currencysymbol') . price( $total ) . "\n";
                        $totalBill = $totalBill + $total;
                    }
                    echo "</textarea>";
                    ?>
                </div>
                <div class="card-footer">
                    <strong>Total to bill: </strong><?php echo company( 'currencysymbol') . price( $totalBill ); ?>
                </div>
            </div>
        </div>
    </div>
</form>