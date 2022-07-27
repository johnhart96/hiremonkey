<form method="post">
    <p>
        <button type="submit" name="submitServices" class="btn btn-success">Save</button>
    </p>
    <table class="table table-bordered table-striped table-dark">
        <thead>
            <tr>
                <th>Service</th>
                <th>QTY</th>
                <th>Price (<?php echo company( 'currencysymbol' ); ?>)</th>
                <th>Start date</th>
                <th>End date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $getServices = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `linetype` ='text' AND `job` =:jobID" );
            $getServices->execute( [ ':jobID' => $id ] );
            while( $service = $getServices->fetch( PDO::FETCH_ASSOC ) ) {
                echo "<tr>";
                // Name
                $name = $service['id'] . "_name";
                echo "<td><input type='text' name='$name' value='" . $service['itemName'] . "' class='form-control'></td>";

                // Qty
                $qtyName = $service['id'] . "_qty";
                echo "<td><input type='text' name='$qtyName' value='" . $service['qty'] . "' class='form-control'></td>";

                // Price
                $priceName = $service['id'] . "_price";
                echo "<td><input type='text' name='$priceName' value='" . $service['price'] . "' class='form-control'></td>";

                // Start date
                $startDateName = $service['id'] . "_startdate";
                echo "<td><input type='text' name='$startDateName' value='" . date( "Y-m-d H:i" , strtotime( $service['service_startdate']  ) ) . "' class='form-control' placeholder='YYYY-MM-DD HH:MM'></td>";

                // End date
                $endDateName = $service['id'] . "_enddate";
                echo "<td><input type='text' name='$endDateName' value='" . date( "Y-m-d H:i" , strtotime( $service['service_enddate']  ) ) . "' class='form-control' placeholder='YYYY-MM-DD HH:MM'></td>";
                
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</form>