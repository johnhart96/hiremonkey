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
    $name = filter_var( $_POST['name'] , FILTER_SANITIZE_STRING );
    $company_number = filter_var( $_POST['company_number'] , FILTER_SANITIZE_STRING );
    $vat_number = filter_var( $_POST['vat_number'] , FILTER_SANITIZE_STRING );
    $invoice_terms = filter_var( $_POST['invoice_terms'] , FILTER_SANITIZE_NUMBER_INT );
    $website = filter_var( $_POST['website'] , FILTER_VALIDATE_URL );

    $okToAdd = TRUE;
    if( trial() && entry_count( "customers" ) >= 10 ) {
        $okToAdd = FALSE;
    }
    if( $okToAdd ) {
        try {
            $insert = $db->prepare( "INSERT INTO `customers` (`name`,`company_number`,`vat_number`,`invoice_terms`,`website`) VALUES(:custname,:company_number,:vat_number,:invoice_terms,:website)" );
            $insert->execute( [ ':custname' => $name , ':company_number' => $company_number , ':vat_number' => $vat_number , ':invoice_terms' => $invoice_terms , ':website' => $website ] );
        } catch( PDOException $Exception ) {
            die( $Exception->getMessage() );
        }
        $getLastEntry = $db->query( "SELECT * FROM `customers` ORDER BY `id` DESC LIMIT 1" );
        $fetch = $getLastEntry->fetch( PDO::FETCH_ASSOC );
        $lastID = $fetch['id'];
        go( "index.php?l=customer_view&id=" . $lastID );
    }
}
?>
<div class="row">
    <div class="col">
        <h1>New Contact:</h1>
        <hr />
        <?php
        if( trial() ) {
            if( entry_count( "customers" ) >= 10 ) {
                echo "<div class='alert alert-danger'><strong>Error:</strong> You can only have 10 customers in trial mode! <a href='index.php'>Return?</a></div>";
                die();
            } else {
                echo "<div class='alert alert-warning'><strong>Warning:</strong> You can only add " . 10 - entry_count( "customers" ) . " more customers while in trial mode!</div>";
            }
        }
        ?>
    </div>
</div>
<form method="post">
    <div class="row">
        <div class="col">
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Name:</span></div>
                <input type="text" name="name" class="form-control">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Company number:</span></div>
                <input type="text" name="company_number" class="form-control">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">VAT number:</span></div>
                <input type="text" name="vat_number" class="form-control">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Invoice settlement:</span></div>
                <input type="text" name="invoice_terms" class="form-control" value="0">
                <div class="input-group-append"><span class="input-group-text">Days</span></div>
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Website:</span></div>
                <input type="text" name="website" class="form-control">
            </div>
            <p>&nbsp;</p>
            <button type="submit" name="submit" class="btn btn-success">Create</button>
        </div>  
    </div>
</form>
