<div class="row">
    <div class="col">
        <h1>Repairs</h1>
        <hr />
    </div>
</div>
<div class="row">
    <div class="col">
        <form method="post" id="search">
            <div class="input-group">
                <select name="filter" class="form-control">
                    <?php
                    function checked( $ch ) {
                        if( isset( $_POST['filter'] ) ) {
                            $filter = filter_var( $_POST['filter'] , FILTER_UNSAFE_RAW );
                            if( $ch == $filter ) {
                                return "selected";
                            }
                        } else {
                            if( $ch == "active" ) {
                                return "selected";
                            }
                        }
                    }
                    echo "<option value='active' " . checked( "active" ) . ">Active</option>"; 
                    echo "<option value='complete' " . checked( "complete" ) . ">Complete</option>"; 
                    ?>
                </select>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Show</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col">
        <?php
        if( isset( $_POST['filter'] ) ) {
            $filter = filter_var( $_POST['filter'] , FILTER_UNSAFE_RAW );
            if( $filter == "active" ) {
                $search = $db->query( "SELECT * FROM `kit_repairs` WHERE `complete` =0" );
            } else {
                $search = $db->query( "SELECT * FROM `kit_repairs` WHERE `complete` =1" );
            }
        } else {
            $search = $db->query( "SELECT * FROM `kit_repairs` WHERE `complete` =0" );
        }
        ?>
        <table id="repairs" class="table table-bordered table-striped table-dark">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Description</th>
                    <th>Equipment</th>
                    <th>Start Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $getStockEntry = $db->prepare( "SELECT * FROM `kit_stock` WHERE `id` =:stockID" );
                $getKit = $db->prepare( "SELECT * FROM `kit` WHERE `id` =:kitID" );
                while( $repair = $search->fetch( PDO::FETCH_ASSOC ) ) {
                    echo "<tr>";
                    // ID
                    echo "<td><a href='index.php?l=" . $repair['id'] . "'>" . $repair['id'] . "</a></td>";
                    // Description
                    echo "<td><a href='index.php?l=" . $repair['id'] . "'>" . $repair['description'] . "</a></td>";
                    // Equipment
                    echo "<td>";
                    $kitID = $repair['kit'];
                    $getStockEntry->execute( [ ':stockID' => $kitID ] );
                    $stockEntry = $getStockEntry->fetch( PDO::FETCH_ASSOC );
                    $kitType = $stockEntry['kit'];
                    $getKit->execute( [ ':kitID' => $kitType ] );
                    $kit = $getKit->fetch( PDO::FETCH_ASSOC );
                    echo "<a href='index.php?l=kit_view&id=" . $kitType . "'>";
                    if( $stockEntry['serialized'] == 1 ) {
                        echo $kit['name'] . " (" . $stockEntry['serialnumber'] . ")";
                    } else {
                        echo $repair['stockeffect'] * -1 . "x " . $kit['name'];
                    }
                    echo "</a>";
                    echo "</td>";

                    // Start Date
                    echo "<td>" . date( "d/m/Y" , strtotime( $repair['startdate']  ) ). "</td>";

                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>        
    </div>
</div>