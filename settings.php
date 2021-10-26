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
    $address_line1 = filter_var( $_POST['address_line1'] , FILTER_SANITIZE_STRING );
    $address_line2 = filter_var( $_POST['address_line2'] , FILTER_SANITIZE_STRING );
    $town = filter_var( $_POST['town'] , FILTER_SANITIZE_STRING );
    $postcode = filter_var( $_POST['postcode'] , FILTER_SANITIZE_STRING );
    $telephone = filter_var( $_POST['telephone'] , FILTER_SANITIZE_STRING );
    $website = filter_var( $_POST['website'] , FILTER_VALIDATE_URL );
    $email = filter_var( $_POST['email'] , FILTER_VALIDATE_EMAIL );
    $currencysymbol = filter_var( $_POST['currencysymbol'] , FILTER_SANITIZE_STRING );
    $logo = filter_var( $_POST['logo'] , FILTER_VALIDATE_URL );

    $update = $db->prepare("
        UPDATE `company` SET
            `name` =:name,
            `address_line1` =:address_line1,
            `address_line2` =:address_line2,
            `town` =:town,
            `postcode` =:postcode,
            `telephone` =:telephone,
            `website` =:website,
            `email` =:email,
            `currencysymbol` =:currencysymbol,
            `logo` =:logo
        WHERE `id` =1
    ");
    $update->execute([
        ':name' => $name,
        ':address_line1' => $address_line1,
        ':address_line2' => $address_line2,
        ':town' => $town,
        ':postcode' => $postcode,
        ':telephone' => $telephone,
        ':website' => $website,
        ':email' => $email,
        ':currencysymbol' => $currencysymbol,
        ':logo' => $logo
    ]);
    $saved = TRUE;
}
?>
<div class="row">
    <div class="col">
        <h1>Settings:</h1>
        <hr />
        <?php
        if( isset( $saved ) ) {
            echo "<div class='alert alert-success'>Changes saved!</div>";
        }
        ?>
    </div>
</div>
<div class="row">
    <div class="col">
        <ul class="nav nav-tabs">
            <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php?l=settings">Company</a></li>
            <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=settings_licence">Licence</a></li>
            <li class="nav-item"><a class="nav-link" aria-current="page" href="index.php?l=settings_sloc">Storage Locations</a></li>
        </ul>
    </div>
</div>
<div class="row">&nbsp;</div>
<div class="row">
    <div class="col">
        <form method="post" id="settings">
            <?php
            $getCompany = $db->query( "SELECT * FROM `company` WHERE `id` =1 LIMIT 1" );
            $company = $getCompany->fetch( PDO::FETCH_ASSOC );
            ?>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Name:</span></div>
                <input type="text" name="name" value="<?php echo $company['name']; ?>" class="form-control">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Address Line 1:</span></div>
                <input type="text" name="address_line1" value="<?php echo $company['address_line1']; ?>" class="form-control">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Address Line 2:</span></div>
                <input type="text" name="address_line2" value="<?php echo $company['address_line2']; ?>" class="form-control">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Town:</span></div>
                <input type="text" name="town" value="<?php echo $company['town']; ?>" class="form-control">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Postcode:</span></div>
                <input type="text" name="postcode" value="<?php echo $company['postcode']; ?>" class="form-control">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Telephone:</span></div>
                <input type="text" name="telephone" value="<?php echo $company['telephone']; ?>" class="form-control">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Website:</span></div>
                <input type="text" name="website" value="<?php echo $company['website']; ?>" class="form-control">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Email:</span></div>
                <input type="text" name="email" value="<?php echo $company['email']; ?>" class="form-control">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Currency:</span></div>
                <input type="text" name="currencysymbol" value="<?php echo $company['currencysymbol']; ?>" class="form-control">
            </div>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Logo:</span></div>
                <input type="text" name="logo" value="<?php echo $company['logo']; ?>" class="form-control">
            </div>
            <p>&nbsp;</p>
            <p><button type="submit" name="submit" class="btn btn-success">Save</button></p>
        </form>
    </div>
</div>