<script>
  // Set job name
  document.getElementById("jobtitle").innerHTML = "<?php echo $job['name']; ?>";

  // Get inventory and add to new item box.
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

<!-- Actions -->
<div class="row">
    <div class="col">
        <div class="btn-group">
            <?php
            // New category
            if( (int)$job['complete'] == 0 ) {
                modalButton_green( "newcat" , "New category" );
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
            }
            // Job Type
            if( $job['complete'] == 0 ) {
                modalButton( "changetype" , "Job type" );
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
            }

            // Cancel job
            if( $job['complete'] ==0 ) {
                modalButton_red( "delete" , "Cancel" );
                $dialog = "
                    Are you sure you want to cancel this job?
                    <input type='hidden' name='cancelJob' value='$id'>
                ";
                modal( "delete" , "Cancel job?" , $dialog , "Yes No" );
            }

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
            modal( "undo" , "Reverse?" , $dialog , "Confirm Cancel" );
            modalButton( "undo" , "Reverse" );

            // Complete
            if( $job['jobType'] == "order" && $job['complete'] == 0 ) {
                modalButton( "complete" , "Complete" );
                $dialog = "
                    Are you sure you want to complete this job?
                    <input type='hidden' name='completeJob' value='" . $job['id'] . "'>
                ";
                modal( "complete" , "Complete job" , $dialog , "Yes No" );
            }

            // Print document
            modalButton( "doc" , "Print/Export" );
            $dialog = "
                <p>Please select the document you want to open</p>
                <div class='input-group'>
                    <select name='doc' class='form-control'>
                        <option selected disabled></option>
                        <option value='quote'>Quotation</option>
                        <option value='subhire'>Subhire PO</option>
                        <option value='insurance'>Insurance Values</option>
                        <option value='order'>Order Confirmation</option>
                        <option value='prep'>Picking list</option>
                        <option value='dispatch'>Dispatch Note</option>
                        <option value='return'>Return Note</option>
                        <option value='invoice'>Customer Invoice</option>
                    </select>
                </div>
                <input type='hidden' name='submitOpenDoc'>
            ";
            modal( "doc" , "Print/Export" , $dialog , "Open Cancel" );

            // Invoicing
            if( $job['jobType'] == "order" ) {
                echo "<a href='index.php?l=invoicing_view&id=$id' class='btn btn-primary'>Invoicing</a>"; 
            }

            // Pricing Lock?
            if( (int)$job['price_lock'] == 1 ) {
                // Pricing locked
                echo "<a href='index.php?l=job_view&id=$id&unlock' class='btn btn-danger'>Unlock " . company( "currencysymbol" ) . "</a>";
            } else {
                // Pricing not locked
                echo "<a href='index.php?l=job_view&id=$id&lock' class='btn btn-danger'>Lock " . company( "currencysymbol" ) . "</a>";
            }
            ?>
        </div>
    </div>
</div>
<div class="row">&nbsp;</div>


<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header" id="header-head"><strong><?php echo ucfirst( $job['jobType'] ); ?> Header:</strong></div>
            <div class="card-body" id="header-body" style="display: none;">
                <?php
                if( (int)$job['complete'] ==1 ) {
                    echo "<div class='alert alert-success'>Job is complete!</div>";
                }
                if( (int)$job['price_lock'] == 1 ) {
                    echo "<div class='alert alert-info'>Pricing is locked!</div>";
                }
                ?>
                <form method="post">
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Name:</span></div>
                        <input type="text" name="name" class="form-control" value="<?php echo $job['name']; ?>">
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Start Date:</span></div>
                        <input type="text" name="startdate" placeholder="YYYY-MM-DD" class="form-control" value="<?php echo $job['startdate']; ?>">
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">End Date:</span></div>
                        <input type="text" name="enddate" placeholder="YYYY-MM-DD" class="form-control" value="<?php echo $job['enddate']; ?>">
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Address:</span></div>
                        <select required name="address" class="form-control">
                            <?php
                            $currentAddress = (int)$job['address'];
                            $getAddressess = $db->prepare( "SELECT * FROM `customers_addresses` WHERE `customer` =:customerID" );
                            $getAddressess->execute( [ ':customerID' => (int)$job['customer'] ] );
                            while( $address = $getAddressess->fetch( PDO::FETCH_ASSOC ) ) {
                                if( $currentAddress ==0 ) {
                                    echo "<option selected disabled value='1'>NONE SET!</option>";
                                }
                                if( (int)$address['id'] == $currentAddress && $currentAddress !==0 ) {
                                    echo "<option value='" . $address['id'] . "' selected>" . $address['line1'] , ", " . $address['line2'] . ", " . $address['town'] . ", " . $address['postcode'] . "</option>"; 
                                } else {
                                    echo "<option value='" . $address['id'] . "'>" . $address['line1'] , ", " . $address['line2'] . ", " . $address['town'] . ", " . $address['postcode'] . "</option>"; 
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Contact:</span></div>
                        <select required name="contact" class="form-control">
                            <?php
                            $currentContact = (int)$job['contact'];
                            $getContacts = $db->prepare( "SELECT * FROM `customers_contacts` WHERE `customer` =:custID" );
                            $getContacts->execute( [ ':custID' => $job['customer'] ] );
                            while( $contact = $getContacts->fetch( PDO::FETCH_ASSOC ) ) {
                                if( $currentContact == 0 ) {
                                    echo "<option selected disabled value='1'>NONE SET!</option>";
                                }
                                if( (int)$contact['id'] == $currentContact && $currentContact !==0 ) {
                                    echo "<option selected value='" . $contact['id'] . "'>" . $contact['name'] . "</option>";
                                } else {
                                    echo "<option value='" . $contact['id'] . "'>" . $contact['name'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Quote status:</span></div>
                        <select name="quoteAgreed" class="form-control">
                            <?php
                            if( (int)$job['quoteAgreed'] ==1 ) {
                                echo "<option selected value='1'>Agreed</option>";
                                echo "<option value='0'>Non Agreed</option>";
                            } else {
                                echo "<option value='1'>Agreed</option>";
                                echo "<option selected value='0'>Not Agreed</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Type:</span></div>
                        <input disabled type="text" name="jobType" class="form-control" value="<?php echo ucfirst( $job['jobType'] ); ?>">
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">Total days:</span></div>
                        <?php
                        $startDate = strtotime( $job['startdate'] );
                        $endDate = strtotime( $job['enddate'] );
                        $diff = $endDate - $startDate;
                        $years = floor( $diff / (365*60*60*24) );
                        $months = floor( ( $diff - $years * 365*60*60*24 ) / ( 30*60*60*24 ) ); 
                        $days = floor( ( $diff - $years * 365*60*60*24 - $months*30*60*60*24 ) / ( 60*60*24 ) ); 
                        $days ++;

                        ?>
                        <input disabled type="text" name="totalDays" class="form-control" value="<?php echo $days; ?>">
                    </div>
                    <p>&nbsp;</p>
                    <button type="submit" name="submitHeader" class="btn btn-success">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>