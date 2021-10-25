<script>
  $( function() {
    var availableTags = [
      <?php
      $getKit = $db->query( "SELECT * FROM `kit` WHERE `toplevel` =1 AND `active` =1" );
      while( $kit = $getKit->fetch( PDO::FETCH_ASSOC ) ) {
          echo "'" . $kit['name'] . "',";
      }
      ?>
    ];
    $( "#newitem" ).autocomplete({
      source: availableTags
    });
  } );
  </script>
<div class="col-2">
    <div class="card">
        <div class="card-header" id="actions-head"><strong>Actions:</strong></div>
        <div class="card-body" id="actions-body">
            <center>
                <div class="btn-group">
                    <?php
                    if( $job['complete'] == 0 ) {
                        modalButton( "changetype" , "Type" );
                    }
                    $dialog = "
                        <div class='input-group'>
                            <div class='input-group-prepend'><span class='input-group-text'>Type:</span></div>
                            <select class='form-control' name='changeType'>
                                <option selected disabled</option>
                                <option value='quote'>Quote</option>
                                <option value='order'>Order</option>
                            </select>
                        </div>
                        <input type='hidden' name='submitChangeType' value='$id'>
                    ";
                    modal( "changetype" , "Order Type" , $dialog , "Change Cancel" );
                    if( $job['complete'] ==0 ) {
                        modalButton_red( "delete" , "Cancel" );
                    }
                    $dialog = "
                        Are you sure you want to cancel this job?
                        <input type='hidden' name='cancelJob' value='$id'>
                    ";
                    modal( "delete" , "Cancel job?" , $dialog , "Yes No" );

                    // Undo
                    $dialog = "
                        <div class='input-group'>
                            <div class='input-group-prepend'><span class='input-group-text'>Action:</span></div>
                            <select name='action' class='form-control'>
                                <option value='0' selected disabled></option>
                                <option value='dispatch'>Dispatch</option>
                                <option value='return'>Return</option>
                                <option value='complete'>Job Completion</option>
                            </select>
                        </div>
                        <input type='hidden' name='submitUndo'>
                    ";
                    modal( "undo" , "Undo?" , $dialog , "Confirm Cancel" );
                    modalButton( "undo" , "Undo" );
                    ?>
                </div>
                <p>&nbsp;</p>
                <div class="btn-group">
                    <?php
                    if( (int)$job['complete'] == 0 ) {
                        modalButton_green( "newcat" , "New category" );
                    }
                    // New Category dialog
                    $newcat = "
                        <div class='input-group'>
                            <div class='input-group-prepend'>
                                <span class='input-group-text'>Category:</span>
                            </div>
                            <select name='cat' class='form-control'>
                    ";
                    $getCats = $db->query( "SELECT * FROM `categories`" );
                    while( $cat = $getCats->fetch( PDO::FETCH_ASSOC ) ) {
                        $newcat .= "<option>" . $cat['name'] . "</option>";
                    }
                    $newcat .= "</select></div><input type='hidden' name='submitNewCat'>";
                    modal( "newcat" , "New category" , $newcat , "Add Cancel" );

                    // Complete
                    if( $job['jobType'] == "order" && $job['complete'] == 0 ) {
                        modalButton( "complete" , "Complete" );
                        $dialog = "
                            Are you sure you want to complete this job?
                            <input type='hidden' name='completeJob' value='" . $job['id'] . "'>
                        ";
                        modal( "complete" , "Complete job" , $dialog , "Yes No" );
                    }
                    ?>
                </div>
                <p>&nbsp;</p>
                <div class="btn-group">
                    <?php
                    modalButton( "doc" , "Documents" );
                    $dialog = "
                        <p>Please select the document you want to open</p>
                        <div class='input-group'>
                            <select name='doc' class='form-control'>
                                <option selected disabled></option>
                                <option value='quote'>Quotation</option>
                                <option value='order'>Order Confirmation</option>
                                <option value='dispatch'>Dispatch Note</option>
                                <option value='prep'>Picking list</option>
                                <option value='subhire'>Subhire PO</option>
                                <option value='invoice'>Customer Invoice</option>
                            </select>
                        </div>
                        <input type='hidden' name='submitOpenDoc'>
                    ";
                    modal( "doc" , "Open document" , $dialog , "Open Cancel" );
                    echo "<a href='index.php?l=invoicing_view&id=$id' class='btn btn-primary'>Invoicing</a>"; 
                    ?>
                </div>
            </center>
        </div>
    </div>
    <?php if( $job['complete'] == 0 ) { ?>
        <p>&nbsp;</p>
        <div class="card">
            <div class="card-header" id="add-head"><strong>Add:</strong></div>
            <div class="card-body" id="add-body">
                <form method="post">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <input style="width: 50px; text-align:center" type="text" class="form-control" value="1" name="qty" placeholder="Qty">
                        </div>
                        <input type="text" name="newitem" id="newitem" class="form-control" placeholder="New item...">
                        <div class="input-group-append">
                            <button type="submit" name="submitNewItem" class="btn btn-success">+</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <p>&nbsp;</p>
        <div class="card">
            <div class="card-header" id="edit-head">
                <strong>Line Edit:</strong>
            </div>
            <div class="card-body" id="edit-body">
                <form method="post">
                    <?php
                    if( isset( $_GET['editline'] ) ) {
                        $selectedLine = filter_var( $_GET['editline'] , FILTER_SANITIZE_NUMBER_INT );
                        $getSelectedLine = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `job` =:jobID AND `id` =:lineID LIMIT 1" );
                        $getSelectedLine->execute( [ ':jobID' => $id , ':lineID' => $selectedLine ] );
                        $line = $getSelectedLine->fetch( PDO::FETCH_ASSOC );

                        // Line type
                        echo "<div class='input-group'>";
                        echo "<div class='input-group-prepend'><span class='input-group-text'>Type:</span></div>";
                        echo "<select name='lineType' class='form-control'>";
                        $types = array( 'hire', 'text', 'subhire' );
                        foreach( $types as $try ) {
                            if( $try == $line['linetype'] ) {
                                echo "<option value='$try' selected>" . ucfirst( $try ) . "</option>";
                            } else {
                                echo "<option value='$try'>" . ucfirst( $try ) . "</option>";
                            }
                        }
                        echo "</select>";
                        echo "</div>";
                    
                        // Cat
                        echo "<div class='input-group'>";
                        echo "<div class='input-group-prepend'><span class='input-group-text'>Category:</span></div>";
                        echo "<select required name='cat' class='form-control'>";
                        $getJobCats = $db->prepare( "SELECT * FROM `jobs_cat` WHERE `job` =:jobID" );
                        $getJobCats->execute( [ ':jobID' => $id ] );
                        while( $row = $getJobCats->fetch( PDO::FETCH_ASSOC ) ) {
                            echo "<option value='0'>None</option>";
                            if( (int)$row['id'] == (int)$line['cat'] ) {
                                echo "<option selected value='" . $row['id'] . "'>" . $row['cat'] . "</option>";
                            } else {
                                echo "<option value='" . $row['id'] . "'>" . $row['cat'] . "</option>";
                            }
                        }
                        echo "</select>";
                        echo "</div>";

                        // Price
                        echo "<div class='input-group'>";
                        echo "<div class='input-group-prepend'><span class='input-group-text'>Price: " . company( "currencysymbol") . "</span></div>";
                        echo "<input type='text' name='price' class='form-control' value='" . price( $line['price'] ) . "'>";
                        echo "</div>";

                        // Qty
                        echo "<div class='input-group'>";
                        echo "<div class='input-group-prepend'><span class='input-group-text'>Qty:</span></div>";
                        if( $line['mandatory'] == 1 ) {
                            $disabled = "disabled";
                        } else {
                            $disabled = "";
                        }
                        echo "<input " . $disabled . " type='text' name='qty' class='form-control' value='" . $line['qty'] . "'>";
                        echo "</div>";

                        // Notes
                        echo "<div class='form-floating'>";
                        echo "<textarea class='form-control' name='notes' id='notes' placeholder='Notes:'>" . $line['notes'] . "</textarea>";
                        echo "<label for='notes'>Notes:</label>";
                        echo "</div>";

                        // button
                        echo "<button name='submitLineEdit' value='" . $line['id'] . "' class='btn btn-success'>Change</button>";
                    } else {
                        echo "<center><em>No line selected</em></center>";
                    }
                    ?>
                </form>
            </div>
        </div>
    </div>
    <?php } ?>
    