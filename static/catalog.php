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
// INIT
require '../inc/version.php';
require '../inc/usrPath.php';
require '../inc/functions.php';
session_start();
$file = usrPath . "/" . $_SESSION['company'];
$db = new PDO( 'sqlite:' . $file );

$getCompany = $db->query( "SELECT * FROM `company` ORDER BY `id` DESC LIMIT 1" );
$company = $getCompany->fetch( PDO::FETCH_ASSOC );

// Trial restriction
if( trial() ) {
    die( "Catalog is not avalible with a trial licence!" );
}
?>
<html>
    <head>
        <title><?php echo $company['name']; ?> - Hire Catalog (<?php echo date( "d/m/Y" ); ?>)</title>
        <link href="../../node_modules/bootstrap/dist/css/bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="../css/documents.css" rel="stylesheet" type="text/css" />
        <style>
            @media print {
                .pbreak { page-break-after: always; }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">&nbsp;</div>
            <div class="row">
                <div class="col">
                    <center>
                        <?php
                        if( ! empty( company( 'logo' ) ) && ! trial() ) {
                            echo "<img src='" . company( 'logo' ) . "'>";
                        }
                        ?>
                        <h1><?php echo $company['name']; ?> - Hire Catalog (<?php echo date( "d/m/Y" ); ?>)</h1>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>
                            <?php
                            echo str_replace( "https://" , "" , company( 'website' ) ) . "<br />";
                            echo company( 'email' ) . "<br />";
                            ?>
                        </p>
                    </center>
                    
                </div>
            </div>
            <p class="pbreak">&nbsp;</p>
            <div class="row">
                <div class="col">
                    <h2><a name='contense'>Contents</a></h2>
                    <?php
                    $getCats = $db->query( "SELECT * FROM `categories`" );
                    echo "<ol>";
                    while( $cat = $getCats->fetch( PDO::FETCH_ASSOC ) ) {
                        echo "<li><a href='#" . $cat['name'] . "'>" . $cat['name'] . "</a></li>";
                    }  
                    echo "</ol>";
                    ?>
                </div>
            </div>
            <p class='pbreak'></p>

            <?php
            // Get all cats
            $getCats = $db->query( "SELECT * FROM `categories`" );
            $getProducts = $db->prepare( "SELECT * FROM `kit` WHERE `cat` =:catID AND `toplevel` =1" );
            function getAccessories( $product ) {
                global $db;
                $getAccessories = $db->prepare( "SELECT * FROM `kit_accessories` WHERE `kit` =:kitID AND `type` != 'spare'" );
                $getAccessories->execute( [ ':kitID' => $product ] );
                $getProduct = $db->prepare( "SELECT * FROM `kit` WHERE `id` =:productID" );
                while( $accessory = $getAccessories->fetch( PDO::FETCH_ASSOC ) ) {
                    $getProduct->execute( [ ':productID' => $accessory['accessory'] ] );
                    $acc = $getProduct->fetch( PDO::FETCH_ASSOC );
                    echo "&nbsp;&nbsp;" . $accessory['qty'] . "x " . $acc['name'];
                    echo "<br />";
                    getAccessories( $accessory['accessory'] );
                }
            }
            while( $cat = $getCats->fetch( PDO::FETCH_ASSOC ) ) {
                echo "<div class='row'>";
                echo "<div class='col'>";
                echo "<h2><a name='" . $cat['name'] . "'>" . $cat['name'] . "</a></h2>";
                // Get products
                echo "<table class='table table-bordered table-striped'>";
                $catID = (int)$cat['id'];
                $getProducts->execute( [ ':catID' => $catID ] );
                while( $product = $getProducts->fetch( PDO::FETCH_ASSOC ) ) {
                    echo "<tr>";
                    // Image
                    echo "<td width='100'>";
                    if( empty( $product['img'] ) ) {
                        echo "<img width='100' src='noimg.jpeg'>"; 
                    } else {
                        echo "<img width='100' src='" . $product['img'] . "'>"; 
                    }
                    echo "</td>";

                    // Body
                    echo "<td>";
                    echo "<h3>" . $product['name'] . "</h3>";
                    echo "<p>" . $product['notes'] . "</p>";

                    // Accessories
                    echo "<p>";
                    echo "<strong>Includes:</strong> <br />";
                    $kitID = $product['id'];
                    getAccessories( $kitID );
                    
                    echo "</p>";
                    echo "</td>";

                    // Price
                    if( isset( $_SESSION['catalog_showpricers'] ) ) {
                        echo "<td align='center'>" . company( 'currencysymbol' ) . price( $product['price'] ) . "/day</td>";
                    }

                    echo "</tr>";
                }
                echo "</table>";
                echo "</div>";
                echo "</div>";
                echo "<p class='pbreak'></p>";
            }
            ?>

        </div>  
    </body>
</html>