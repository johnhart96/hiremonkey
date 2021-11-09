<?php
// Submit header
if( isset( $_POST['submitHeader'] ) ) {
    $name = filter_var( $_POST['name'] , FILTER_SANITIZE_STRING );
    $startdate = filter_var( $_POST['startdate'] , FILTER_SANITIZE_STRING );
    $enddate = filter_var( $_POST['enddate'] , FILTER_SANITIZE_STRING );
    if( empty( $_POST['address'] ) ) {
        $address = 0;
    } else {
        $address = filter_var( $_POST['address'] , FILTER_SANITIZE_NUMBER_INT );
    }
    if( empty( $_POST['contact'] ) ) {
        $contact = 0;
    } else {
        $contact = filter_var( $_POST['contact'] , FILTER_SANITIZE_NUMBER_INT );
    }
    $quoteAgreed = filter_var( $_POST['quoteAgreed'] , FILTER_SANITIZE_NUMBER_INT );

    $update = $db->prepare("
        UPDATE `jobs` SET
            `name` =:jobName,
            `startdate` =:startdate,
            `enddate` =:enddate,
            `address` =:addre,
            `contact` =:contact,
            `quoteAgreed` =:quoteAgreed
        WHERE `id` =:jobID
    ");
    $update->execute([
        ':jobName' => $name,
        ':startdate' => $startdate,
        ':enddate' => $enddate,
        ':addre' => $address,
        ':contact' => $contact,
        ':quoteAgreed' => $quoteAgreed,
        ':jobID' => $id
    ]);

    $saved = true;
}

// Submit new category
if( isset( $_POST['submitNewCat'] ) ) {
    $cat = filter_var( $_POST['cat'] , FILTER_SANITIZE_STRING );
    $insert = $db->prepare( "INSERT INTO `jobs_cat` (`job`,`cat`) VALUES(:job,:cat)" );
    $insert->execute( [ ':job' => $id , ':cat' => $cat ] );
}

// Submit delete a category
if( isset( $_GET['deletecat'] ) ) {
    $catToDelete = filter_var( $_GET['deletecat'] , FILTER_SANITIZE_NUMBER_INT );
    // Move line items from this cat to 0
    $moveLines = $db->prepare( "UPDATE `jobs_lines` SET `cat` =0 WHERE `job` =:jobID AND `cat` =:catID" );
    $moveLines->execute( [ ':jobID' => $id , ':catID' => $catToDelete ] );
    // Delete the cat
    $delete = $db->prepare( "DELETE FROM `jobs_cat` WHERE `id` =:catID" );
    $delete->execute( [ ':catID' => $catToDelete ] );
}

// Submit line item edit
if( isset( $_POST['submitLineEdit'] ) ) {
    $line = filter_var( $_POST['submitLineEdit'] , FILTER_SANITIZE_NUMBER_INT );
    $cat = filter_var( $_POST['cat'] , FILTER_SANITIZE_NUMBER_INT );
    $price = filter_var( $_POST['price'] , FILTER_VALIDATE_FLOAT );
    $notes = filter_var( $_POST['notes'] , FILTER_SANITIZE_STRING );
    $lineType = filter_var( $_POST['lineType'] , FILTER_SANITIZE_STRING );
    echo $lineType;
    if( ! empty( $_POST['qty'] ) ) {
        $qty = filter_var( $_POST['qty'] , FILTER_SANITIZE_NUMBER_INT );
    } else {
        $qty = 1;
    }
    if( $lineType == "hire" ) {
        $stockEffect = $qty *-1;
        if( $stockEffect == 0 ) {
            die( "0 stock effect encountered" );
        }
    } else {
        $stockEffect = 0;
    }
    if( isset( $_POST['qty'] ) ) {
        $update = $db->prepare("
            UPDATE `jobs_lines` SET
                `cat` =:cat,
                `price` =:price,
                `qty` =:qty,
                `stockeffect` =:stockEffect,
                `notes` =:notes,
                `lineType` =:lineType
            WHERE `id` =:lineID AND `job` =:jobID
        ");
        $update->execute([
            ':cat' => $cat,
            ':price' => $price,
            ':lineID' => $line,
            ':qty' => $qty,
            ':jobID' => $id,
            ':notes' => $notes,
            ':stockEffect' => $stockEffect,
            ':lineType' => $lineType
        ]);
    } else {
        $update = $db->prepare("
            UPDATE `jobs_lines` SET
                `cat` =:cat,
                `price` =:price,
                `notes` =:notes
            WHERE `id` =:lineID AND `job` =:jobID
        ");
        $update->execute([
            ':cat' => $cat,
            ':price' => $price,
            ':lineID' => $line,
            ':jobID' => $id,
            ':notes' => $notes,
        ]);
    }
}

// Submit line delete
if( isset( $_GET['deleteline'] ) ) {
    $lineToDelete = filter_var( $_GET['deleteline'] , FILTER_SANITIZE_NUMBER_INT );
    // Find child items
    function deleteChildren( $delete ) {
        global $db;
        global $id;
        $getChildren = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `job` =:jobID AND `parent` =:lineID" );
        $getChildren->execute( [ ':jobID' => $id , ':lineID' => $delete ] );
        while( $row = $getChildren->fetch( PDO::FETCH_ASSOC ) ) {
            deleteChildren( $row['id'] );
        }
        $deleteThisItem = $db->prepare( "DELETE FROM `jobs_lines` WHERE `job` =:jobID AND `id` =:lineID" );
        $deleteThisItem->execute( [ ':jobID' => $id , ':lineID' => $delete ] );
    }
    deleteChildren( $lineToDelete );
}

// Submit line new
if( isset( $_POST['submitNewItem'] ) ) {
    $new = filter_var( $_POST['newitem'] , FILTER_SANITIZE_STRING );
    $qty = filter_var( $_POST['qty'] , FILTER_SANITIZE_NUMBER_INT );

    function newLine( $new , $qty , $ignoreStock = FALSE , $parent = 0 , $mandatory = 0, $accType = NULL , $price = NULL ) {
        global $db;
        global $id;
        // Find item ID
        $getKitDetails = $db->prepare( "SELECT * FROM `kit` WHERE `name` =:kitName LIMIT 1" );
        $getKitDetails->execute( [ ':kitName' => $new ] );
        $fetch = $getKitDetails->fetch( PDO::FETCH_ASSOC );
        if( isset( $fetch['id'] ) ) {
            require 'inc/job_select.php';
            // Found hire item
            $error = FALSE;
            $kitID = $fetch['id'];
            require 'inc/job_select.php';
            $avlb = avlb( $kitID , $job['startdate'] , $job['enddate'] );

            echo "<script>console.log('Avlb: $avlb')</script>";
            if( $avlb < $qty && ! $ignoreStock ) {
                // Not enough in stock
                $error = "<strong>Stock Shortage:</strong> You asked for " . $qty . ", but you only have " . $avlb . " available";
            } else {
                // Good to add this to the job
                $insert = $db->prepare( "INSERT INTO `jobs_lines` (`job`,`linetype`,`stockEffect`,`price`,`cat`,`qty`,`kit`,`itemName`,`parent`,`mandatory`,`accType`) VALUES(:jobID,:linetype,:stockeffect,:price,:cat,:qty,:kit,:itemName,:parent,:mandatory,:accType)" );
                
                // Check if the cat already exists on this job
                $getKitCat = $db->prepare( "SELECT * FROM `categories` WHERE `id` =:catID" );
                $getKitCat->execute( [ ':catID' => (int)$fetch['cat'] ] );
                $cat = $getKitCat->fetch( PDO::FETCH_ASSOC );
                $catName = $cat['name'];
                $searchJobCat = $db->prepare( "SELECT * FROM `jobs_cat` WHERE `cat` =:catName AND `job` =:jobID LIMIT 1" );
                $searchJobCat->execute( [ ':catName' => $catName , ':jobID' => $id ] );
                $jobCat = $searchJobCat->fetch( PDO::FETCH_ASSOC );
                if( isset( $jobCat['id'] ) ) {
                    // Cat exists
                    $catToUse = (int)$jobCat['id'];
                } else {
                    // cat does not exist
                    $insertCat = $db->prepare( "INSERT INTO `jobs_cat` (`cat`,`job`) VALUES(:catName,:jobID)" );
                    $insertCat->execute( [ ':catName' => $catName , ':jobID' => $id ] );
                    $getLastAdded = $db->query( "SELECT * FROM `jobs_cat` ORDER BY `id` DESC LIMIT 1" );
                    $lastAdded = $getLastAdded->fetch( PDO::FETCH_ASSOC );
                    $catToUse = (int)$lastAdded['id'];
                }
                $stockEffect = (int)$qty * -1;
                if( $stockEffect == 0 ) {
                    die( "Stock effect cannot be 0" );
                }

                // Price handel
                if( empty( $price ) ) {
                    $price = $fetch['price'];
                }
                // Add the line into the job
                $insert->execute([
                    ':jobID' => $id,
                    ':linetype' => 'hire',
                    ':stockeffect' => $stockEffect,
                    ':price' => $price,
                    ':cat' => $catToUse,
                    ':qty' => $qty,
                    ':kit' => $fetch['id'],
                    ':itemName' => $fetch['name'],
                    ':parent' => $parent,
                    ':mandatory' => $mandatory,
                    ':accType' => $accType
                ]);

                // Get last entry
                $getLast = $db->query( "SELECT * FROM `jobs_lines` ORDER BY `id` DESC LIMIT 1" );
                $last = $getLast->fetch( PDO::FETCH_ASSOC );
                $parent = (int)$last['id'];
                
                // Get Accessories
                $getAccessories = $db->prepare( "SELECT * FROM `kit_accessories` WHERE `kit` =:kitID" );
                $getAccessories->execute( [ ':kitID' => $fetch['id'] ] );
                while( $acc = $getAccessories->fetch( PDO::FETCH_ASSOC ) ) {
                    echo "<script>console.log('Has accessory');</script>";
                    $accessoryID = $acc['accessory'];
                    $mandatory = $acc['mandatory'];
                    $getAccessory = $db->prepare( "SELECT * FROM `kit` WHERE `id` =:accID LIMIT 1" );
                    $getAccessory->execute( [ ':accID' => $accessoryID ] );
                    $f = $getAccessory->fetch( PDO::FETCH_ASSOC );
                    $accName = $f['name'];
                    echo "<script>console.log('$accName');</script>";
                    if( $acc['type'] !== "spare" ) {
                        $error = newLine( $accName , (int)$acc['qty'] * $qty , TRUE , $parent , $mandatory , $acc['type'] , $acc['price'] );
                    } else {
                        // Spare
                        $insert = $db->prepare( "INSERT INTO `jobs_lines` (`job`,`linetype`,`stockEffect`,`price`,`cat`,`qty`,`kit`,`itemName`,`parent`,`mandatory`,`accType`,`notes`) VALUES(:jobID,:linetype,:stockeffect,:price,:cat,:qty,:kit,:itemName,:parent,:mandatory,:accType,:notes)" );
                        $insert->execute([
                            ':jobID' => $id,
                            ':linetype' => 'hire',
                            ':stockeffect' => -1,
                            ':price' => 0.0,
                            ':cat' => $catToUse,
                            ':qty' => 1,
                            ':kit' => $fetch['id'],
                            ':itemName' => $fetch['name'],
                            ':parent' => $parent,
                            ':mandatory' => $mandatory,
                            ':accType' => $accType,
                            ':notes' => 'Spare'
                        ]);
                    }
                } 
            }
        } else {
            // text line
            echo "<script>console.log('text line');</script>";
            require 'inc/job_select.php';
            $insert = $db->prepare( "INSERT INTO `jobs_lines` (`job`,`linetype`,`price`,`itemName`,`qty`) VALUES(:jobID,:linetype,:price,:itemname,:qty)" );
            $insert->execute([
                ':jobID' => $id,
                ':linetype' => 'text',
                ':price' => 0.0,
                ':qty' => $qty,
                ':itemname' => $new
            ]);
        }
        if( isset( $error ) ) {
            return $error;
        }
    }
    $error = newLine( $new , $qty , TRUE , 0 );
}
// Cancel the job
if( isset( $_POST['cancelJob'] ) ) {
    $jobToCancel = filter_var( $_POST['cancelJob'] , FILTER_SANITIZE_NUMBER_INT );
    $delLines = $db->prepare( "DELETE FROM `jobs_lines` WHERE `job` =:jobID" );
    $delJob = $db->prepare( "DELETE FROM `jobs` WHERE `id` =:jobID" );
    $delLines->execute( [ ':jobID' => $jobToCancel ] );
    $delJob->execute( [ ':jobID' => $jobToCancel ] );
    go( "index.php?l=job_browse&canceled" );
}
// Change order type
if( isset( $_POST['submitChangeType'] ) ) {
    $order = filter_var( $_POST['submitChangeType'] , FILTER_SANITIZE_NUMBER_INT );
    $type = filter_var( $_POST['changeType'] , FILTER_SANITIZE_STRING );
    $changeType = $db->prepare( "UPDATE `jobs` SET `jobType` =:jobType WHERE `id` =:jobID" );
    $changeType->execute( [ ':jobType' => $type , ':jobID' => $order ] );
}
// Submit costs
if( isset( $_POST['submitCosts'] ) ) {
    $id = filter_var( $_GET['id'] , FILTER_SANITIZE_NUMBER_INT );
    $getLines = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `job` =:jobID" );
    $updateLine = $db->prepare( "UPDATE `jobs_lines` SET `price` =:price ,  `cost` =:cost , `discount` =:discount WHERE `id` =:lineID AND `job` =:jobID" );
    $getLines->execute( [ ':jobID' => $id ] );
    while( $line = $getLines->fetch( PDO::FETCH_ASSOC ) ) {
        $offset_cost = $line['id'] . "_cost";
        $offset_price = $line['id'] . "_price";
        $offset_discount = $line['id'] . "_discount";
        $cost = filter_var( $_POST[$offset_cost] , FILTER_VALIDATE_FLOAT );
        $price = filter_var( $_POST[$offset_price] , FILTER_VALIDATE_FLOAT );
        $discount = discount_to_decimel( filter_var( $_POST[$offset_discount] , FILTER_VALIDATE_FLOAT ) );
        if( isset( $_POST[$offset_cost] ) && isset( $_POST[$offset_price] ) ) {
            $updateLine->execute( [ ':price' => $price , ':cost' => $cost , ':lineID' => (int)$line['id'] , ':discount' => $discount , ':jobID' => $id ] );
        }
    }
}

// Submit shipping
if( isset( $_POST['submitShipping'] ) ) {
    $id = filter_var( $_GET['id'] , FILTER_SANITIZE_NUMBER_INT );
    $getLines = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `job` =:jobID" );
    $getLines->execute( [ ':jobID' => $id ] );
    $date = date( "Y-m-d H:i" );
    $dispatchLine = $db->prepare( "UPDATE `jobs_lines` SET `dispatch` =1,  `dispatch_date` =:today WHERE `id` =:lineID AND `job` =:jobID");
    $returnLine = $db->prepare( "UPDATE `jobs_lines` SET `return` =1, `return_date` =:today WHERE `id` =:lineID AND `job` =:jobID ");
    while( $line = $getLines->fetch( PDO::FETCH_ASSOC ) ) {
        // Dispatch
        $dispatchID = "dispatch_" . $line['id'];
        if( isset( $_POST[$dispatchID] ) ) {
            $dispatchLine->execute( [ ':today' => $date , ':lineID' => $line['id'] , ':jobID' => $id ] );
        }
        // Return
        $returnID = "return_" . $line['id'];
        if( isset( $_POST[$returnID] ) ) {
            $returnLine->execute( [ ':today' => $date , ':lineID' => $line['id'] , ':jobID' => $id ] );
        }
    }
}
// Submit undo
if( isset( $_POST['submitUndo'] ) ) {
    $action = filter_var( $_POST['action'] , FILTER_SANITIZE_STRING );
    $id = filter_var( $_GET['id'] , FILTER_SANITIZE_NUMBER_INT );
    if( $action == "return" ) {
        $processReturn = $db->prepare( "UPDATE `jobs_lines` SET `return` =0 , `return_date` =NULL , `complete` =0 WHERE `job` =:jobID" );
        $processReturn->execute( [ ':jobID' => $id ] ); 
    } else if( $action == "dispatch" ) {
        $processDispatch = $db->prepare( "UPDATE `jobs_lines` SET `return` =0 , `return_date` =NULL , `dispatch` =0 , `dispatch_date` =NULL WHERE `job` =:jobID" );
        $processDispatch->execute( [ ':jobID' => $id ] ); 
    } else if( $action == "complete" ) {
        $processComplete = $db->prepare( "UPDATE `jobs` SET `complete` =0 WHERE `id` =:jobID" );
        $processComplete->execute( [ ':jobID' => $id ] );
    }
}
// Dispatch all
if( isset( $_GET['dispatch'] ) ) {
    $id = filter_var( $_GET['id'] , FILTER_SANITIZE_NUMBER_INT );
    $today = date( "Y-m-d H:i" );
    $dispatch = $db->prepare( "UPDATE `jobs_lines` SET `dispatch` =1 , `dispatch_date` =:today , `return` =0 , `return_date` =NULL WHERE `job` =:jobID" );
    $dispatch->execute( [ ':today' => $today , ':jobID' => $id ] );
    go( "index.php?l=job_shipping&id=$id" );
}
// Return All
if( isset( $_GET['return'] ) ) {
    $id = filter_var( $_GET['id'] , FILTER_SANITIZE_NUMBER_INT );
    $today = date( "Y-m-d H:i" );
    $return = $db->prepare( "UPDATE `jobs_lines` SET `return` =1 , `return_date` =:today WHERE `job` =:jobID" );
    $return->execute( [ ':today' => $today , ':jobID' => $id ] );
    go( "index.php?l=job_shipping&id=$id" );
}
// Complete job
if( isset( $_POST['completeJob'] ) ) {
    $id = filter_var( $_GET['id'] , FILTER_SANITIZE_NUMBER_INT );
    $completeJob = $db->prepare( "UPDATE `jobs` SET `complete` =1 WHERE `id` =:jobID" );
    $completeJob->execute( [ ':jobID' => $id ] );
}
// Document open
if( isset( $_POST['submitOpenDoc'] ) ) {
    $doc = filter_var( $_POST['doc'] , FILTER_SANITIZE_STRING );
    echo "<script>window.open('static/$doc.php?id=$id');</script>";
}
// Submit services
if( isset( $_POST['submitServices'] ) ) {
    $getServices = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `linetype` ='text' AND `job` =:jobID" );
    $getServices->execute( [ ':jobID' => $id ] );
    $updateLine = $db->prepare( "UPDATE `jobs_lines` SET `itemName` =:itemName, `qty` =:qty, `price` =:price, `service_startdate` =:startdate, `service_enddate` =:enddate WHERE `job` =:job AND `id` =:lineID" );
    while( $row = $getServices->fetch( PDO::FETCH_ASSOC ) ) {
        $itemNameLine = $row['id'] . "_name";
        $priceName = $row['id'] . "_price";
        $qtyName = $row['id'] . "_qty";
        $startDate = $row['id'] . "_startdate";
        $endDate = $row['id'] . "_enddate";
        if( isset( $_POST[$itemNameLine] ) ) {
            $updateLine->execute([
                ':lineID' => $row['id'],
                ':job' => $id,
                
                ':itemName' => filter_var( $_POST[$itemNameLine] , FILTER_SANITIZE_STRING ),
                ':qty' => filter_var( $_POST[$qtyName] , FILTER_SANITIZE_NUMBER_INT ),
                ':price' => filter_var( $_POST[$priceName] , FILTER_VALIDATE_FLOAT ),
                ':startdate' => filter_var( $_POST[$startDate] , FILTER_SANITIZE_STRING ),
                ':enddate' => filter_var( $_POST[$endDate] , FILTER_SANITIZE_STRING )
            ]);
        } 
    }
}

// Submit subhire
if( isset( $_POST['submitSubhire'] ) ) {
    $updateLine = $db->prepare( "UPDATE `jobs_lines` SET `cost` =:cost, `price` =:price, `qty` =:qty, `supplier` =:supplier WHERE `id` =:lineID AND `job` =:jobID" );
    $getSubhireLines = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `linetype` ='subhire' AND `job` =:jobID ORDER BY `id` ASC" );
    $getSubhireLines->execute( [ ':jobID' => $id ] );
    while( $line = $getSubhireLines->fetch( PDO::FETCH_ASSOC ) ) {
        $offset_price = $line['id'] . "_price";
        $offset_qty = $line['id'] . "_qty";
        $offset_cost = $line['id'] . "_cost";
        $offset_supplier = $line['id'] . "_supplier";
        $updateLine->execute([
            ':lineID' => $line['id'],
            ':jobID' => $id,
            ':price' => filter_var( $_POST[$offset_price] , FILTER_VALIDATE_FLOAT ),
            ':qty' => filter_var( $_POST[$offset_qty] , FILTER_SANITIZE_NUMBER_INT ),
            ':cost' => filter_var( $_POST[$offset_cost] , FILTER_VALIDATE_FLOAT ),
            ':supplier' => filter_var( $_POST[$offset_supplier] , FILTER_SANITIZE_NUMBER_INT )
        ]);
    }
}
?>