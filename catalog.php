<?php
/**
 * company_select.php
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
    if( isset( $_POST['showprices'] ) ) {
        $_SESSION['catalog_showpricers'] = TRUE;
    } else {
        if( isset( $_SESSION['catalog_showpricers'] ) ) {
            unset( $_SESSION['catalog_showpricers'] );
        }
    }


    // Open Catalog
    echo "<script>window.open('static/catalog.php');</script>";
}
?>
<form method="post">
    <div class="row">
        <div class="col">
            <h1>Catalog</h1>
            <hr />
        </div>
    </div>
    <div class="row">
        <div class="col">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php?l=kit_browse">Jobs</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Catalog</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header"><strong>Generate Catalog</strong></div>
                <div class="card-body">
                    <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                        <input type="checkbox" class="btn-check" id="showprices" name="showprices" autocomplete="off">
                        <label class="btn btn-outline-primary" for="showprices">Show Prices</label>

                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" name="submit" class="btn btn-success">Generate</button>
                </div>
            </div>
        </div>
    </div>
</form>