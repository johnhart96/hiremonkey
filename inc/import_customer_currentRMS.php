<?php
$csv = file( $target_file );
$insertCustomer = $db->prepare( "INSERT INTO `customers` (`name`,`website`,`supplier`) VALUES(:custName,:website,:supplier)" );
$insertContact = $db->prepare( "INSERT INTO `customers_contacts`(`name`,`email`,`telephone`,`customer`) VALUES(:contactName,:email,:telephone,:customerID)" );
$insertAddress = $db->prepare( "INSERT INTO `customers_addresses`(`line1`,`line2`,`town`,`postcode`,`customer`) VALUES(:address_line1,:address_line2,:town,:postcode,:customerID)" );

foreach( $csv as $customer ) {
    $customer = explode( "," , $customer );
    $name = str_replace( '"' , "" , $customer[1] );
    $address_line1 = str_replace( '"' , "" , $customer[2] );
    $address_line2 = str_replace( '"' , "" , $customer[3] );
    $town = str_replace( '"' , "" , $customer[4] );
    $postcode = str_replace( '"' , '' , $customer[5] );
    $contact_name = NULL;
    $contact_telephone = str_replace( '"' , "" , $customer[22] );
    $contact_email = str_replace( '"' , "" , $customer[26] );
    $website = str_replace( '"' , "" , $customer[32] );
    $supplier = str_replace( '"' , "" , $customer[12]) ;


    // Exclude customers with no names
    if( ! empty( $name ) ) {
        $insertCustomer->execute( [ ':custName' => $name , ':website' => $website , ':supplier' => $supplier ] );
        $getLastInsert = $db->query( "SELECT `id` FROM `customers` ORDER BY `id` DESC LIMIT 1" );
        $lastCustomer = $getLastInsert->fetch( PDO::FETCH_ASSOC );  
        $id = $lastCustomer['id'];
        
        if( ! empty( $address_line1 ) ) {
            $insertAddress->execute([
                ':customerID' => $id,
                ':address_line1' => $address_line1,
                ':address_line2' => $address_line2,
                ':town' => $town,
                ':postcode' => $postcode
            ]);
        }
        if( ! empty( $contact_telephone ) ) {
            $insertContact->execute( [ ':customerID' => $id , ':contactName' => $contact_name , ':telephone' => $contact_telephone , ':email' => $contact_email ] );
        }
    }

}
$done = true;
?>