<br />
<form method="post">
    <div class="btn-group" style="margin-bottom: 5px;">
        <button type="submit" name="submitShipping" class="btn btn-success">Save</button>
        <?php if( $job['jobType'] !== "quote" ) { ?>
            <a href="index.php?l=job_shipping&dispatch&id=<?php echo $id; ?>" class="btn btn-primary">Dispatch All</a>
            <a href="index.php?l=job_shipping&return&id=<?php echo $id; ?>" class="btn btn-primary">Return All</a>
        <?php } ?>
    </div>
    <?php
    $getJobCats = $db->prepare( "SELECT * FROM `jobs_cat` WHERE `job` =:jobID" );
    $getJobCats->execute( [ ':jobID' => $id ] );
    $_SESSION['weight'] = 0.0;
    function getItems( $job , $parent = 0 ) {
        global $db;
        if( $parent == 0 ) {
            $getItems = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `job` =:jobID AND `parent` =0" );
            $getItems->execute( [ ':jobID' => $job ] );
        } else {
            $getItems = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `job` =:jobID AND `parent` =:parent" );
            $getItems->execute( [ ':jobID' => $job , ':parent' => $parent ] );
        }
        $getKit = $db->prepare( "SELECT * FROM `kit` WHERE `id` =:kitID" );
        while( $item = $getItems->fetch( PDO::FETCH_ASSOC ) ) {
            echo "<tr>";
            // Item name
            echo "<td style='text-align: left'>";
            if( (int)$item['parent'] == 0 ) {
                echo "<strong>";
                echo $item['itemName'];
                echo "</strong>";
            } else {
                echo $item['itemName'];
            }
            echo "</td>";
            // Qty
            echo "<td>" . $item['qty'] . "</td>";
            // Type
            echo "<td>" . ucfirst( $item['linetype']  ). "</td>";
            // Storage Location
            echo "<td>";
            if( $item['linetype'] !== "text" ) {
                $getKit->execute( [ ':kitID' => $item['kit'] ] );
                $kit = $getKit->fetch( PDO::FETCH_ASSOC );
                $weight = (double)$kit['weight'];
                echo sloc($kit['sloc'] );
            } else {
                echo "<em>N/A</em>";
            }
            echo "</td>";
            // Weight
            $_SESSION['weight'] = $_SESSION['weight'] + $weight;
            echo "<td>"  .$weight . "</td>";
            // Dispatch
            echo "<td align='center' width='1'>";
            $checkname = "dispatch_" . $item['id'];
            if( (int)$item['dispatch'] !== 1 ) {
                echo "<input class='checkbox' type='checkbox' name='$checkname'>";
            } else {
                echo date( "d/m/Y H:i" , strtotime( $item['dispatch_date'] ) );
            }
            echo "</td>";

            // Return
            echo "<td align='center' width='1'>";
            $checkname = "return_" . $item['id'];
            if( (int)$item['dispatch'] == 1 ) {
                if( (int)$item['return'] !== 1 ) {
                    echo "<input class='checkbox' type='checkbox' name='$checkname'>";
                } else {
                    echo date( "d/m/Y H:i" , strtotime( $item['return_date'] ) );
                }
            }
            echo "</td>";
            
            echo "</tr>";
            $parent = (int)$item['id'];
            getItems( $job , $parent );
        }
    }
    ?>
    <table class="table table-bordered table-striped" style="text-align: center;">
        <tr>
            <th>Item</th>
            <th>Qty</th>
            <th>Type</th>
            <th>Storage Location</th>
            <th>Weight (KG)</th>
            <th>Dispatch</th>
            <th>Return</th>
        </tr>
        <?php
        getItems( $id , 0 );
        ?>
        <tr>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th><?php echo $_SESSION['weight']; ?>KG</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tr>
    </table>
</form>
