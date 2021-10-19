<br />
<form method="post">
    <p>
        <button type="submit" name="submitCosts" class="btn btn-success">Save</button>
    </p>
    <?php
    $getJobCats = $db->prepare( "SELECT * FROM `jobs_cat` WHERE `job` =:jobID" );
    $getJobCats->execute( [ ':jobID' => $id ] );
    function getItems( $job , $cat , $parent = 0 ) {
        global $db;
        global $days;
        if( $parent == 0 ) {
            $getItems = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `job` =:jobID AND `cat` =:catID AND `parent` =0" );
            $getItems->execute( [ ':jobID' => $job , ':catID' => $cat ] );
        } else {
            $getItems = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `job` =:jobID AND `parent` =:parent" );
            $getItems->execute( [ ':jobID' => $job , ':parent' => $parent ] );
        }
        while( $item = $getItems->fetch( PDO::FETCH_ASSOC ) ) {
            echo "<tr>";
            // Item name
            echo "<td style='text-align:left'>";
            if( $parent == 0 ) {
                echo "<strong>";
                echo $item['itemName'];
                echo "</strong>";
            } else {
                echo $item['itemName'];
            }
            echo "</td>";
            // Type
            echo "<td>" . ucfirst( $item['linetype'] ) . "</td>";
            // Cost
            $costName = $item['id'] . "_cost";
            $cost = $item['cost'];
            $_SESSION['totalCost'] = $_SESSION['totalCost'] + $cost;
            echo "<td><input class='costbox' name='$costName' value='$cost'></td>";
            // Price
            $priceName = $item['id'] . "_price";
            $price = $item['price'];
            echo "<td><input class='costbox' name='$priceName' value='$price'></td>";
            // Qty
            echo "<td>";
            $qty = (int)$item['qty'];
            echo $qty;
            echo "</td>";
            // Discount
            echo "<td>";
            $discountName = $item['id'] . "_discount";
            $discount = discount_to_percent( $item['discount'] );
            echo "<input class='costbox' name='$discountName' value='$discount'>";
            echo "</td>";
            // Line total
            echo "<td>";
            $lineTotal = $price * (double)$item['discount'] * $qty * $days;
            echo price( $lineTotal );
            $_SESSION['totalPrice'] = $_SESSION['totalPrice'] + $lineTotal;
            echo "</td>";
            // Profit
            $profit = (double)$lineTotal - (double)$item['cost'];
            $_SESSION['totalProfit'] = $_SESSION['totalProfit'] + $profit;
            if( $profit > 0 ) {
                echo "<td align='center' style='background: green; color: #FFFFFF'>";
            } else if( $profit == 0.0 ) {
                echo "<td align='center'  style='background: orange'>";
            } else {
                echo "<td align='center'  style='background: red'>";
            }
            echo company( "currencysymbol" );
            echo price( $profit);
            echo "</td>";
            echo "</tr>";
            $parent = (int)$item['id'];
            getItems( $job , $cat , $parent );
        }
    }
    $_SESSION['grandTotal'] = 0.0;
    while( $cat = $getJobCats->fetch( PDO::FETCH_ASSOC ) ) {
        $_SESSION['totalCost'] = 0.0;
        $_SESSION['totalPrice'] = 0.0;
        $_SESSION['totalProfit'] = 0.0;
        echo "<h3>" . $cat['cat'] . ":</h3>";
        echo "<table class='table table-bordered table-stripe' style='text-align:center'>";
        // table head
        echo "<tr>";
        echo "<th width='40%' style='text-align:left'>Item</th>";
        echo "<th width='10%'>Type</th>";
        echo "<th width='10%'>Unit Cost (" . company( "currencysymbol" )  . ")</th>";
        echo "<th width='10%'>Unit Price (" . company( "currencysymbol" )  . ")</th>";
        echo "<th width='5%'>Qty</th>";
        echo "<th width='5%'>Discount (%)</th>";
        echo "<th width='10%'>Line Total (" . company( "currencysymbol" ) . ")</th>";



        echo "<th width='10%'>NETT</th>";
        getItems( $id , (int)$cat['id'] , 0 );
        echo "</tr>";
        // Totals
        echo "<tr>";
        echo "<th>&nbsp;</th>";
        echo "<th>&nbsp;</th>";
        // Total cost
        echo "<th>" . company( "currencysymbol" ) . price( $_SESSION['totalCost'] ) . "</th>";
        echo "<th>&nbsp;</th>";
        echo "<th>&nbsp;</th>";
        echo "<th>&nbsp;</th>";
        // Total price
        echo "<th>" . company( "currencysymbol" ) . price( $_SESSION['totalPrice'] ) . "</th>";
        // Total NETT
        if( $_SESSION['totalProfit'] > 0 ) {
            echo "<th align='center' style='background: green; color: #FFFFFF'>";
        } else if( $_SESSION['totalProfit'] < 0 ) {
            echo "<td align='center'  style='background: red'>";
        } else {
            echo "<td align='center'  style='background: orange'>";
        }
        echo company( "currencysymbol" ) . price( $_SESSION['totalProfit'] );
        echo "</th>";
        echo "</tr>";
        echo "</table>";
        $_SESSION['grandTotal'] = $_SESSION['grandTotal'] + $_SESSION['totalPrice'];
    }
    ?>
    <table class='table table-bordered'>
        <tr>
            <td width='80%' colspan='5'><strong>Total Price:</td>
            <td width='10%' style='text-align:center;'><strong><?php echo company( 'currencysymbol' ) . $_SESSION['grandTotal']; ?></strong></td>
            <td width='10%'></td>
        <tr>
    </table>
</form>