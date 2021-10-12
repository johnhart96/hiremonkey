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
            echo "<td>";
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
            echo "<td><input class='costbox' name='$costName' value='$cost'></td>";
            // Price
            $priceName = $item['id'] . "_price";
            $price = $item['price'];
            echo "<td><input class='costbox' name='$priceName' value='$price'></td>";
            // Profit
            $profit = (double)$item['price'] - (double)$item['cost'];
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
    while( $cat = $getJobCats->fetch( PDO::FETCH_ASSOC ) ) {
        echo "<h3>" . $cat['cat'] . ":</h3>";
        echo "<table class='table table-bordered table-stripe'>";
        // table head
        echo "<tr>";
        echo "<th width='60%'>Item</th>";
        echo "<th width='10%'>Type</th>";
        echo "<th width='10%'>Cost (" . company( "currencysymbol" )  . ")</th>";
        echo "<th width='10%'>Price (" . company( "currencysymbol" )  . ")</th>";
        echo "<th width='10%'>Profit</th>";
        getItems( $id , (int)$cat['id'] , 0 );
        echo "</tr>";
        echo "</table>";
    }
    ?>
</form>