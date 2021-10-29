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
// Customer selection
if( ! isset( $_GET['id'] ) ) {
    die( "<div class='alert alert-danger'><strong>Error:</strong> No customer selected!</div>" );
} else {
    $id = filter_var( $_GET['id'] , FILTER_SANITIZE_NUMBER_INT );
}
// Details submit
if( isset( $_POST['submit'] ) ) {
    $name = filter_var( $_POST['name'] , FILTER_SANITIZE_STRING );
    $company_number = filter_var( $_POST['company_number'] , FILTER_SANITIZE_STRING );
    $vat_number = filter_var( $_POST['vat_number'] , FILTER_SANITIZE_NUMBER_INT );
    $invoice_terms = filter_var( $_POST['invoice_terms'] , FILTER_SANITIZE_NUMBER_INT );
    $website = filter_var( $_POST['website'] , FILTER_VALIDATE_URL );
    $hold = filter_var( $_POST['hold'] , FILTER_SANITIZE_NUMBER_INT );
    if( isset( $_POST['supplier'] ) ) {
        $supplier = 1;
    } else {
        $supplier = 0;
    }

    $update = $db->prepare("
        UPDATE `customers` SET
            `name` =:name,
            `company_number` =:company_name,
            `vat_number` =:vat_number,
            `invoice_terms` =:invoice_terms,
            `website` =:website,
            `hold` =:hold,
            `supplier` =:supplier
        WHERE `id` =:id
    ");
    try {
        $update->execute([
            ':name' => $name,
            ':company_name' => $company_number,
            ':vat_number' => $vat_number,
            ':invoice_terms' => $invoice_terms,
            ':website' => $website,
            ':hold' => $hold,
            ':supplier' => $supplier,
            ':id' => $id
        ]);
    } catch( PDOException $e ) {
        print_r( $e );
    }
    if( ! $update ) {
        die( $db->error() );
    } else {
        $updated = TRUE;
    }
}

// Submit address edit
if( isset( $_POST['submitAddressEdit'] ) ) {
    $addressID = filter_var( $_POST['submitAddressEdit'] , FILTER_SANITIZE_NUMBER_INT );
    $line1 = filter_var( $_POST['line1'] , FILTER_SANITIZE_STRING );
    $line2 = filter_var( $_POST['line2'] , FILTER_SANITIZE_STRING );
    $town = filter_var( $_POST['town'] , FILTER_SANITIZE_STRING );
    $postcode = filter_var( $_POST['postcode'] , FILTER_SANITIZE_STRING );

    $update = $db->prepare( "UPDATE `customers_addresses` SET `line1` =:line1 , `line2` =:line2 , `town` =:town , `postcode` =:postcode WHERE `id` =:addressID" );
    $update->execute( [ ':line1' => $line1 , ':line2' => $line2 , ':town' => $town , ':postcode' => $postcode , ':addressID' => $addressID ] );
    $updated = TRUE;
}

// Submit address new
if( isset( $_POST['submitAddressNew'] ) ) {
    $line1 = filter_var( $_POST['line1'] , FILTER_SANITIZE_STRING );
    $line2 = filter_var( $_POST['line2'] , FILTER_SANITIZE_STRING );
    $town = filter_var( $_POST['town'] , FILTER_SANITIZE_STRING );
    $postcode = filter_var( $_POST['postcode'] , FILTER_SANITIZE_STRING );

    $insert = $db->prepare( "INSERT INTO `customers_addresses` (`customer`,`line1`,`line2`,`town`,`postcode`) VALUES(:customer,:line1,:line2,:town,:postcode)" );
    $insert->execute( [ ':customer' => $id , ':line1' => $line1 , ':line2' => $line2 , ':town' => $town , ':postcode' => $postcode ] );
    $updated = TRUE;
}

// Submit address delete
if( isset( $_POST['submitAddressDelete'] ) ) {
    $deleteID = filter_var( $_POST['submitAddressDelete'] , FILTER_SANITIZE_NUMBER_INT );
    $delete = $db->prepare( "DELETE FROM `customers_addresses` WHERE `id` =:deleteID AND `customer` =:customerID" );
    $delete->execute( [ ':deleteID' => $deleteID , ':customerID' => $id ] );
    $updated = TRUE;
}

// Submit contact edit
if( isset( $_POST['submitContactEdit'] ) ) {
    $contact = filter_var( $_POST['submitContactEdit'] , FILTER_SANITIZE_NUMBER_INT );
    $name = filter_var( $_POST['name'] , FILTER_SANITIZE_STRING );
    $email = filter_var( $_POST['email'] , FILTER_VALIDATE_EMAIL );
    $telephone = filter_var( $_POST['telephone'] , FILTER_SANITIZE_STRING );

    $update = $db->prepare( "UPDATE `customers_contacts` SET `name` =:name , `email` =:email , `telephone` =:telephone WHERE `id` =:contactID AND `customer` =:customerID " );
    $update->execute( [ ':name' => $name , ':email' => $email , ':telephone' => $telephone , ':contactID' => $contact , ':customerID' => $id ] );
    $updated = TRUE;
}

// Submit contact delete
if( isset( $_POST['submitContactDelete'] ) ) {
    $deleteID = filter_var( $_POST['submitContactDelete'] , FILTER_SANITIZE_NUMBER_INT );
    $delete = $db->prepare( "DELETE FROM `customers_contacts` WHERE `id` =:contactID AND `customer` =:customerID" );
    $delete->execute( [ ':contactID' => $deleteID , ':customerID' => $id ] );
    $saved = TRUE;
}

// Submit contact new
if( isset( $_POST['submitContactNew'] ) ) {
    $name = filter_var( $_POST['name'] , FILTER_SANITIZE_STRING );
    $email = filter_var( $_POST['email'] , FILTER_VALIDATE_EMAIL );
    $telephone = filter_var( $_POST['telephone'] , FILTER_SANITIZE_STRING );

    $insert = $db->prepare( "INSERT INTO `customers_contacts` (`name`,`email`,`telephone`,`customer`) VALUES(:name,:email,:telephone,:customer)" );
    $insert->execute( [ ':name' => $name , ':email' => $email , ':telephone' => $telephone , ':customer' => $id ] );
    $updated = TRUE;
}

// Submit delete account
if( isset( $_POST['delete'] ) ) {
    $contacts = $db->prepare( "DELETE FROM `customers_contacts` WHERE `customer` =:customerID" );
    $contacts->execute( [ ':customerID' => $id ] );
    $addresses = $db->prepare( "DELETE FROM `customers_addresses` WHERE `customer` =:customerID" );
    $addresses->execute( [ ':customerID' => $id ] );
    $customer = $db->prepare( "DELETE FROM `customers` WHERE `id` =:customerID" );
    $customer->execute( [ ':customerID' => $id ] );
    go( "index.php?l=customer_browse" );
}

// Get current entries
$getCustomer = $db->prepare( "SELECT * FROM `customers` WHERE `id` =:id LIMIT 1" );
$getCustomer->execute( [ ':id' => $id ] );
$customer = $getCustomer->fetch( PDO::FETCH_ASSOC );
?>
<div class="row">
    <div class="col">
        <h1>Customer Record:</h1>
        <hr />
        <?php
        if( isset( $updated ) ) {
            echo "<div class='alert alert-success'>Updated!</div>";
        }
        if( $customer['hold'] == 1 ) {
            echo "<div class='alert alert-warning'>Customer is on hold!</div>";
        }
        ?>
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php?l=customer_browse">Customers</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $customer['name'] ?></li>
            </ol>
        </nav>
    </div>
</div>
<form method="post">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header"><strong>Details:</strong></div>
                <div class="card-body">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Name:</span></div>
                        <input type="text" name="name" class="form-control" value="<?php echo $customer['name']; ?>">
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Company No:</span></div>
                        <input type="text" name="company_number" class="form-control" value="<?php echo $customer['company_number']; ?>">
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">VAT No:</span></div>
                        <input type="text" name="vat_number" class="form-control" value="<?php echo $customer['vat_number']; ?>">
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Invoice settlement:</span></div>
                        <input type="text" name="invoice_terms" class="form-control" value="<?php echo $customer['invoice_terms']; ?>">
                        <div class="input-group-append"><span class="input-group-text">Days</span></div>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Website:</span></div>
                        <input type="text" name="website" class="form-control" value="<?php echo $customer['website']; ?>">
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Status:</span></div>
                        <select name="hold" class="form-control">
                            <?php
                            switch( (int)$customer['hold'] ) {
                                case 1:
                                    echo "<option value='1' selected><span style='color:red'>On Hold</span></option>";
                                    echo "<option value='0'>Active</option>";
                                    break;
                                case 0:
                                    echo "<option value='1'><span style='color:red'>On Hold</span></option>";
                                    echo "<option value='0' selected>Active</option>";
                                    break;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <?php
                        switch( $customer['supplier'] ) {
                            case 0:
                                $checked = "";
                                break;
                            case 1:
                                $checked = "checked";
                                break;
                        }
                        ?>
                        <div class="btn-group" role="group" aria-label="Is this a supplier?">
                            <input name='supplier' type="checkbox" class="btn-check" id="supplier" autocomplete="off" <?php echo $checked; ?>>
                            <label class="btn btn-outline-primary" for="supplier">Supplier</label>
                        </div>
                    </div>
                    <p style="margin-top: 5px;"><button type="submit" name="submit" class="btn btn-success">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header"><strong>Addresses:</strong></div>
            <div class="card-body">
                <table class="table table-bordered table-stripe">
                    <tr>
                        <th>Line1</th>
                        <th>Line2</th>
                        <th>Town/City</th>
                        <th>Postcode</th>
                        <th colspan="2"></th>
                    </tr>
                    <?php
                    $getAddresses = $db->prepare( "SELECT * FROM `customers_addresses` WHERE `customer` =:id" );
                    $getAddresses->execute( [ ':id' => $id ] );
                    while( $address = $getAddresses->fetch( PDO::FETCH_ASSOC ) ) {
                        echo "<tr>";
                        echo "<td>" . $address['line1'] . "</td>";
                        echo "<td>" . $address['line2'] . "</td>";
                        echo "<td>" . $address['town'] . "</td>";
                        echo "<td>" . $address['postcode'] . "</td>";
                        // Edit
                        echo "<td align='center'>";
                        $modal = "address_" . $address['id'];
                        $dialog = "
                            <div class='input-group'>
                                <div class='input-group-prepend'><span class='input-group-text'>Line 1:</span></div>
                                <input type='text' class='form-control' name='line1' value='" . $address['line1'] . "'>
                            </div>
                            <div class='input-group'>
                                <div class='input-group-prepend'><span class='input-group-text'>Line 2:</span></div>
                                <input type='text' class='form-control' name='line2' value='" . $address['line2'] . "'>
                            </div>
                            <div class='input-group'>
                                <div class='input-group-prepend'><span class='input-group-text'>Town:</span></div>
                                <input type='text' class='form-control' name='town' value='" . $address['town'] . "'>
                            </div>
                            <div class='input-group'>
                                <div class='input-group-prepend'><span class='input-group-text'>Postcode:</span></div>
                                <input type='text' class='form-control' name='postcode' value='" . $address['postcode'] . "'>
                            </div>
                            <input name='submitAddressEdit' type='hidden' value='" . $address['id'] . "'>
                        ";
                        modal( $modal , "Edit address" , $dialog , "Save Cancel" );
                        modalButton( $modal , "Edit" , "Edit address" );
                        echo "</td>";
                        // Delete
                        echo "<td>";
                        $modal = "deleteaddress_" . $address['id'];
                        $dialog = "
                            Are you sure you want to delete this address?
                            <input type='hidden' name='submitAddressDelete' value='" . $address['id'] . "'>
                        ";
                        modal( $modal , "Delete?" , $dialog , "Yes No" );
                        modalButton_red( $modal , "Delete" );
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                    <tr>
                        <td colspan="6" align="center">
                            <?php
                            $dialog = "
                                <div class='input-group'>
                                    <div class='input-group-prepend'><span class='input-group-text'>Line 1:</span></div>
                                    <input type='text' class='form-control' name='line1' value=''>
                                </div>
                                <div class='input-group'>
                                    <div class='input-group-prepend'><span class='input-group-text'>Line 2:</span></div>
                                    <input type='text' class='form-control' name='line2' value=''>
                                </div>
                                <div class='input-group'>
                                    <div class='input-group-prepend'><span class='input-group-text'>Town:</span></div>
                                    <input type='text' class='form-control' name='town' value=''>
                                </div>
                                <div class='input-group'>
                                    <div class='input-group-prepend'><span class='input-group-text'>Postcode:</span></div>
                                    <input type='text' class='form-control' name='postcode' value=''>
                                </div>
                                <input name='submitAddressNew' type='hidden' value=''>
                            ";
                            modal( "addressNew" , "New Address" , $dialog , "Save Cancel" );
                            modalButton_green( "addressNew" , "New" );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header"><strong>Contacts:</strong></div>
            <div class="card-body">
                <table class="table table-bordered table-stripe">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Telephone</th>
                        <th colspan="2"></th>
                    </tr>
                    <?php
                    $getContacts = $db->prepare( "SELECT * FROM `customers_contacts` WHERE `customer` =:id" );
                    $getContacts->execute( [ ':id' => $id ] );
                    while( $contact = $getContacts->fetch( PDO::FETCH_ASSOC ) ) {
                        echo "<tr>";
                        echo "<td>" . $contact['name'] . "</td>";
                        echo "<td>" . $contact['email'] . "</td>";
                        echo "<td>" . $contact['telephone'] . "</td>";
                        // Edit
                        echo "<td width='1'>";
                        $modal = "contactedit_" . $contact['id'];
                        $dialog = "
                            <div class='input-group'>
                                <div class='input-group-prepend'><span class='input-group-text'>Name:</span></div>
                                <input type='text' class='form-control' name='name' value='" . $contact['name'] . "'>
                            </div> 
                            <div class='input-group'>
                                <div class='input-group-prepend'><span class='input-group-text'>Email:</span></div>
                                <input type='text' class='form-control' name='email' value='" . $contact['email'] . "'>
                            </div> 
                            <div class='input-group'>
                                <div class='input-group-prepend'><span class='input-group-text'>Telephone:</span></div>
                                <input type='text' class='form-control' name='telephone' value='" . $contact['telephone'] . "'>
                            </div>
                            <input type='hidden' name='submitContactEdit' value='" . $contact['id'] . "'>
                        ";
                        modal( $modal , "Edit contact" , $dialog , "Save Cancel" );
                        modalButton( $modal , "Edit" );
                        echo "</td>"; 
                        // Delete
                        echo "<td width='1'>";
                        $modal = "contactdelete_" . $contact['id'];
                        $dialog = "
                            Are you sure you want to delete this contact?
                            <input type='hidden' name='submitContactDelete' value='" . $contact['id'] . "'>
                        ";
                        modal( $modal , "Delete?" , $dialog , "Yes No" );
                        modalButton_red( $modal , "Delete" );
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                    <td colspan="5" align="center">
                        <?php
                        $modal = "newcontact";
                        $dialog = "
                            <div class='input-group'>
                                <div class='input-group-prepend'><span class='input-group-text'>Name:</span></div>
                                <input type='text' class='form-control' name='name' value=''>
                            </div> 
                            <div class='input-group'>
                                <div class='input-group-prepend'><span class='input-group-text'>Email:</span></div>
                                <input type='text' class='form-control' name='email' value=''>
                            </div> 
                            <div class='input-group'>
                                <div class='input-group-prepend'><span class='input-group-text'>Telephone:</span></div>
                                <input type='text' class='form-control' name='telephone' value=''>
                            </div>
                            <input type='hidden' name='submitContactNew' value=''>
                        ";
                        modal( $modal , "New contact" , $dialog , "Save Cancel" );
                        modalButton_green( $modal , "New" );
                        ?>
                    </td>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">&nbsp;</div>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header"><strong>Jobs:</strong></div>
            <div class="card-body">
                <table class="table table-bordered table-stripped" id="revenue">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Duration (days)</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $getJobs = $db->prepare( "SELECT * FROM `jobs` WHERE `customer` =:id ORDER BY `startdate` DESC" );
                        $getJobs->execute( [ ':id' => $id ] );
                        $getLines = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `job` =:id" );
                        while( $job = $getJobs->fetch( PDO::FETCH_ASSOC ) ) {
                            echo "<tr>";
                            echo "<td>" . $job['id'] . "</td>";
                            echo "<td><a href='index.php?l=job_view&id=" . $job['id'] . "'>" . $job['name'] . "</a></td>";
                            echo "<td>" . date( "d/m/Y" , strtotime( $job['startdate'] ) ) . "</td>";
                            echo "<td>" . date( "d/m/Y" , strtotime( $job['enddate'] ) ) . "</td>";
                            echo "<td>" . duration( $job['startdate'] , $job['enddate'] ) . "</td>";
                            // Revenue
                            echo "<td>";
                            $getLines->execute( [ ':id' => $job['id'] ] );
                            $rev = 0.0;
                            while( $line = $getLines->fetch( PDO::FETCH_ASSOC ) ) {
                                $line = (double)$line['price'] * (int)$line['qty'] * (double)$line['discount'] * duration( $job['startdate'] , $job['enddate'] );
                                $rev = $rev + $line;
                            }
                            echo company( 'currencysymbol' ) . price( $rev );
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">&nbsp;</div>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header"><strong>Actions:</strong></div>
            <div class="card-body">
                <?php
                modalButton_red( "delete" , "Delete Account" );
                $dialog = "
                    Are you sure you want to delete this account?
                    <input type='hidden' name='delete'>
                ";
                modal( "delete" , "Delete?" , $dialog , "Yes No" );
                ?>
            </div>
        </div>
    </div>
</div>
