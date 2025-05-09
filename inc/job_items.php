<ul class="item_list">
    <?php
    function getLines( $parent = 0 , $cat = NULL ) {
        global $db;
        global $id;
        global $job;
        if( $parent == 0  ) {
            $getLineItems = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `job` =:jobID AND `cat` =:cat AND `parent` =:parent" );
            $getLineItems->execute( [ ':jobID' => $id , ':cat' => $cat , ':parent' => $parent ] );
        } else {
            $getLineItems = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `job` =:jobID AND `parent` =:parent" );
            $getLineItems->execute( [ ':jobID' => $id , ':parent' => $parent ] );
        }
        while( $line = $getLineItems->fetch( PDO::FETCH_ASSOC ) ) {
            $avlb = avlb( (int)$line['kit'] , $job['startdate'] , $job['enddate'] );
            if( $parent == 0 ) {
                echo "<li class='line toplevel'>";
            } else {
                echo "<li class='line'>";
            }
            if( isset( $_GET['editline'] ) ) {
                $selectedLine = filter_var( $_GET['editline'] , FILTER_SANITIZE_NUMBER_INT );
                if( $selectedLine == (int)$line['id'] ) {
                    // this line is selected
                    $span =  "<span class='line-selected'>";
                    $end = "</span>";
                } else {
                    $end = NULL;
                    $span = NULL;
                }
            } else {
                $end = NULL;
                $span = NULL;
            }
            echo $span;
            echo $line['qty'] . "x ";
            if( $line['dispatch'] == 0 ) {
                echo "<a class='line_select' href='index.php?l=job_view&id=$id&editline=" . $line['id'] . "'>";
            }
            if( $line['linetype'] !== "hire" ) {
                $avlb = 1000000000000000000000;
            }
            if( $avlb < 0 ) {
                echo "<span style='color:red' data-bs-toggle='tooltip' data-bs-placement='top' title='Stock shortage'>";
                echo $line['itemName'];
                echo "</span>";
            } else {
                echo $line['itemName'];
            }
            $e = (int)$avlb;
            if( $line['linetype'] == "hire" ) {
                echo " [ " . $e . " AVLB] ";
            }
            if( $line['dispatch'] == 0 ) {
                echo "</a>";
            }
            echo $end;
            echo "&nbsp;[" . company( "currencysymbol" ) . price( $line['price'] ) . "]";
            echo "&nbsp;(" . ucfirst( $line['linetype'] ) . ")";
            // Accessory type
            if( ! empty( $line['accType'] ) ) {
                switch( $line['accType'] ) {
                    case "component":
                        $accType = "Component";
                        break;
                    case "safety":
                        $accType = "Safety item";
                        break;
                    case "accessory":
                        $accType = "Accessory";
                        break;
                    default:
                        $accType = NULL;
                        break;
                }
                echo "&nbsp; - <em style='color: #CCC;'>" . $accType . "</em>";
            }
            if( $line['dispatch'] == 0 && $line['mandatory'] == 0 ) {
                echo "&nbsp;<a class='line_link' href='index.php?l=job_view&id=$id&deleteline=" . $line['id']  . "'>X</a>";
            } else {
                if( $line['dispatch'] == 1 && $line['return'] == 0  ) {
                    echo "&nbsp; - <em class='status'>DISPATCHED</em>";
                } else if( $line['dispatch'] == 1 && $line['return'] == 1 ) {
                    echo "&nbsp; - <em class='status'>RETURNED</em>";
                }
            }
            echo "<ul class='sub'>";
            if( ! empty( $line['notes'] ) ) {
                echo "<li><em class='line-nones'>" . $line['notes'] . "</em></li>";
            }
            $parent = $line['id'];
            getLines( $parent , $cat );
            echo "</ul>";
            echo "</li>";
            $parent = 0;
        }
    }
    // Items with no cat
    getLines( 0 , 0 );
    $getCats = $db->prepare( "SELECT * FROM `jobs_cat` WHERE `job` =:jobID" );
    $getCats->execute( [ ':jobID' => $id ] );
    while( $cat = $getCats->fetch( PDO::FETCH_ASSOC ) ) {
        echo "<li>";
        echo "<strong>" . $cat['cat'] . "&nbsp;<a class='line_link' href='index.php?l=job_view&id=$id&deletecat=" . $cat['id'] . "'>X</a></strong>";
        // Get items
        echo "<ul class='cat'>";
        getLines( 0 , $cat['id'] );
        $parent = 0;
        echo "</ul>";
        echo "</li>"; // End of this cat
    }
    ?>
</ul>