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
?>
<div class="container">
    <div class="row">
        <div class="col">
            <div class="box">
                <table class="table">
                    <tr>
                        <td>
                            <h1>Purchase Order</h1>
                            <p>
                                <?php echo company( 'website' ); ?><br />
                                <?php echo company( 'email' ); ?><br />
                                <?php echo company( 'telephone' ); ?>
                            </p>
                        </td>
                        <td style="text-align: right">
                            <?php
                            if( ! empty( company( 'logo' ) ) && ! trial() ) {
                                echo "<img src='" . company( 'logo' ) . "'>";
                            }
                            ?>
                            <h1><?php echo company( 'name' ); ?></h1>
                            <p>
                                <?php
                                if( ! empty( company( 'address_line1' ) ) ) {
                                    echo company( 'address_line1' ) . "<br />";
                                }
                                if( ! empty( company( 'address_line2' ) ) ) {
                                    echo company( 'address_line2' ) . "<br />";
                                }
                                if( ! empty( company( 'town' ) ) ) {
                                    echo company( 'town' ) . "<br />";
                                }
                                if( ! empty( company( 'postcode' ) ) ) {
                                    echo company( 'postcode' ) . "<br />";
                                }
                                ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="line"><hr /></div>
    <div class="row">
        <div class="col">
            <div class="box" style="height: 300px; !important;">
                <p>
                    <strong>Purchase order to:</strong><br />
                    <?php
                    echo "<strong>" . customer( $supplier ) . "</strong><br />";
                    $getAddress = $db->prepare( "SELECT * FROM `customers_addresses` WHERE `customer` =:supplier ORDER BY `id` ASC LIMIT 1" );
                    $getAddress->execute( [ ':supplier' => $supplier ] );
                    $fetch = $getAddress->fetch( PDO::FETCH_ASSOC );
                    if( ! empty( $fetch['line1'] ) ) {
                        echo $fetch['line1'] . "<br />";
                    }
                    if( ! empty( $fetch['line2'] ) ) {
                        echo $fetch['line2'] . "<br />";
                    }
                    if( ! empty( $fetch['town'] ) ) {
                        echo $fetch['town'] . "<br />";
                    }
                    if( ! empty( $fetch['postcode'] ) ) {
                        echo $fetch['postcode'] . "<br />";
                    }
                    ?>
                </p>
            </div>
        </div> 
        <div class="col">
            <div class="box" style="height: 300px; !important;">
                <p>
                    <strong>Job Details:</strong><br />
                </p>
                <table class='table table-bordered'>
                    <tr>
                        <th width='30%'>PO#:</th>
                        <td><?php echo $job['id'] . "-" . $pocount; ?></td>
                    </tr>
                    <tr>
                        <th width='30%'>Job name:</th>
                        <td><?php echo $job['name']; ?></td>
                    </tr>
                    </tr>
                    <tr>
                        <th width='30%'>Date:</th>
                        <td><?php echo date( "d/m/Y" ) ?></td>
                    </tr>
                    <tr>
                        <th width='30%'>Start date:</th>
                        <td><?php echo date( "d/m/Y" , strtotime( $job['startdate'] ) ); ?></td>
                    </tr>
                    <tr>
                        <th width='30%'>End date:</th>
                        <td><?php echo date( "d/m/Y" , strtotime( $job['enddate'] ) ); ?></td>
                    </tr>
                    <tr>
                        <th width='30%'>Duration:</th>
                        <td>
                            <?php
                            $startDate = strtotime( $job['startdate'] );
                            $endDate = strtotime( $job['enddate'] );
                            $diff = $endDate - $startDate;
                            $years = floor( $diff / (365*60*60*24) );
                            $months = floor( ( $diff - $years * 365*60*60*24 ) / ( 30*60*60*24 ) ); 
                            $days = floor( ( $diff - $years * 365*60*60*24 - $months*30*60*60*24 ) / ( 60*60*24 ) );
                            $days ++;
                            echo $days . " Days";
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>              
    </div>
    <div class="line"><hr /></div>
    <div class="row">
        <div class="col">
            <table class="table table-bordered table-striped">
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Line Total</th>
                </tr>
                <?php
                $grandTotal = 0.0;
                $getLines = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `linetype` ='subhire' AND `job` =:jobID AND `supplier` =:supplierID ORDER BY `id` ASC" );
                $getLines->execute( [ ':jobID' => $id , ':supplierID' => $supplier ] );
                while( $line = $getLines->fetch( PDO::FETCH_ASSOC ) ) {
                    echo "<tr>";
                    echo "<td>";
                    if( $line['parent'] == 0 ) {
                        echo "<strong>" . $line['itemName'] . "</strong>";
                    } else {
                        echo $line['itemName'];
                    }
                    echo "<td>" . $line['qty'] . "</td>";
                    $each = $line['cost'] / $line['qty'];
                    echo "<td>" . company( 'currencysymbol') . price( $each ) . "</td>";
                    echo "<td>" . company( 'currencysymbol') .  price( $line['cost'] ) . "</td>";
                    echo "</td>";
                    echo "</tr>";
                    $grandTotal = $grandTotal + $line['cost'];
                }
                ?>
                <tr>
                    <td colspan='3'>&nbsp;</td>
                    <th><?php echo company( 'currencysymbol') .  price( $grandTotal ); ?></th> 
                </tr>
            </table>
        </div>
    </div>
</div>