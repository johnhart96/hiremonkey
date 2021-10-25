<?php
$id = filter_var( $_GET['id'] , FILTER_SANITIZE_STRING );
// Submit
if( isset( $_POST['submitInvoice'] ) ) {
    $invoiced = filter_var( $_POST['invoiced'] , FILTER_SANITIZE_NUMBER_INT );
    $invoice_number = filter_var( $_POST['invoice_number'] , FILTER_SANITIZE_STRING );
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
                <div class="card-header"><strong>Invoice</strong></div>
                <div class="card-body">
                    <?php //require 'static/invoice.php'; ?>
                    <iframe style="width: 100%; height: 1024px; border: none;" src="static/invoice.php?id=<?php echo $id; ?>"></iframe>
                </div>
            </div>
        </div>
    </div>
</form>