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
        <h1>Contacts:</h1>
        <hr />
    </div>
</div>
<div class="row">
    <div class="col">
        <form method="post" id="search">
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text">Search:</span></div>
                <input autofocus name="search" class="form-control" placeholder="Skidrow theatre" value="<?php if( isset( $_POST['search'] ) ) { echo filter_var( $_POST['search'] , FILTER_UNSAFE_RAW ); } ?>" >
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col">
        <table id="customers" class="table table-bordered table-striped table-dark" id="customers">
            <thead>
                <tr>
                    <th>Customers</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if( isset( $_POST['search'] ) ) {
                    $search = filter_var( $_POST['search'] , FILTER_UNSAFE_RAW );
                    $getCustomers = $db->prepare( "SELECT * FROM `customers` WHERE instr(`name`,:searchTerm) --case-insensitive " );
                    $getCustomers->execute( [ ':searchTerm' => $search ] );
                } else {
                    $getCustomers = $db->query( "SELECT * FROM `customers`" );
                }
                while( $row = $getCustomers->fetch( PDO::FETCH_ASSOC ) ) {
                    echo "<tr>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td width='1'><a href='index.php?l=customer_view&id=" . $row['id'] . "' class='btn btn-primary'>View</a></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>