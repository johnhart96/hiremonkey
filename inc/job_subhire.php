<form method="post">
    <p>
        <button type="submit" name="submitSubhire" class="btn btn-success">Save</button>
    </p>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price (<?php echo company( 'currencysymbol' ); ?>)</th>
                <th>Cost (<?php echo company( 'currencysymbol' ); ?>)</th>
                <th>Supplier</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $getSubhireLines = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `linetype` ='subhire' AND `job` =:jobID ORDER BY `id` ASC" );
            $getSubhireLines->execute( [ ':jobID' => $id ] );
            while( $line = $getSubhireLines->fetch( PDO::FETCH_ASSOC ) ) {
                echo "<tr>";

                // Name
                if( $line['parent'] == 0 ) {
                    echo "<td><strong>" . $line['itemName'] . "</strong></td>";
                } else {
                    echo "<td>" . $line['itemName'] . "</td>";
                }

                // Qty
                $offset_qty = $line['id'] . "_qty";
                echo "<td><input type='text' class='form-control' name='$offset_qty' value='" . $line['qty'] . "'></td>";

                // Price
                $offset_price = $line['id'] . "_price";
                echo "<td><input type='text' class='form-control' name='$offset_price' value='" . $line['price'] . "'></td>";

                // Cost
                $offset_cost = $line['id'] . "_cost";
                echo "<td><input type='text' class='form-control' name='$offset_cost' value='" . $line['cost'] . "'></td>";

                // Supplier
                echo "<td>";
                $offset_supplier = $line['id'] . "_supplier";
                echo "<select name='$offset_supplier' class='form-control'>";
                $getSuppliers = $db->query( "SELECT * FROM `customers` WHERE `supplier` =1 AND `hold` =0" );
                while( $sup = $getSuppliers->fetch( PDO::FETCH_ASSOC ) ) {
                    if( $sup['id'] == $line['supplier'] ) {
                        echo "<option value='" . $sup['id'] . "' selected>" . $sup['name'] . "</option>";
                    } else {
                        echo "<option value='" . $sup['id'] . "'>" . $sup['name'] . "</option>";
                    }
                }
                echo "</select>";
                echo "</td>";

                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</form>