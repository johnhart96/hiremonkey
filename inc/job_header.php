<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header" id="header-head"><strong><?php echo ucfirst( $job['jobType'] ); ?> Header:</strong></div>
            <div class="card-body" id="header-body">
                <?php
                if( (int)$job['complete'] ==1 ) {
                    echo "<div class='alert alert-success'>Job is complete!</div>";
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
                    <p>&nbsp;</p>
                    <button type="submit" name="submitHeader" class="btn btn-success">Save</button>
                </form>
            </div>
        </div>
    </div>
    <?php require 'inc/job_actions.php'; ?>
</div>