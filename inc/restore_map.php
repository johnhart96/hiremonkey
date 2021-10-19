<?php
// Insert data to new file
$newDB = new PDO( 'sqlite:' . $newFile );
$categories = $newDB->prepare( "INSERT INTO `categories` (`id`,`name`) VALUES(:id,:cat)" );
foreach( $upgrade['categories'] as $cat ) {
    $categories->execute( [ ':id' => $cat['id'] , ':cat' => $cat['name'] ] );
}

$company = $newDB->prepare("
    INSERT INTO `company` (`id`,`name`,`address_line1`,`address_line2`,`town`,`postcode`,`telephone`,`website`,`email`,`currencysymbol`,`lastbackup`,`appversion`,`welcome`)
    VALUES(:id,:company,:address_line1,:address_line2,:town,:postcode,:telephone,:website,:email,:currencysymbol,:lastbackup,:appversion,:welcome)
");
foreach( $upgrade['company'] as $com ) {
    if( isset( $com['welcome'] ) ) {
        $welcome = $com['welcome'];
    } else {
        $welcome = 1;
    }
    $company->execute([
        ':id' => $com['id'],
        ':company' => $com['name'],
        ':address_line1' => $com['address_line1'],
        ':address_line2' => $com['address_line2'],
        ':town' => $com['town'],
        ':postcode' => $com['postcode'],
        ':telephone' => $com['telephone'],
        ':website' => $com['website'],
        ':email' => $com['email'],
        ':currencysymbol' => $com['currencysymbol'],
        ':lastbackup' => $com['lastbackup'],
        ':appversion' => FULLBUILD,
        ':welcome' => $welcome
    ]);
} 

$customers = $newDB->prepare("
    INSERT INTO `customers` (`id`,`name`,`creationdate`,`notes`,`company_number`,`vat_number`,`invoice_terms`,`hold`,`website`)
    VALUES(:id,:customer,:creationdate,:notes,:company_number,:vat_number,:invoice_terms,:hold,:website)
");
foreach( $upgrade['customers'] as $cus ) {
    $customers->execute([
        ':id' => $cus['id'],
        ':customer' => $cus['name'],
        ':creationdate' => $cus['creationdate'],
        ':notes' => $cus['notes'],
        ':company_number' => $cus['company_number'],
        ':vat_number' => $cus['vat_number'],
        ':invoice_terms' => $cus['invoice_terms'],
        ':hold' => $cus['hold'],
        ':website' => $cus['website']
    ]);
}

$customers_addresses = $newDB->prepare("
    INSERT INTO `customers_addresses` (`id`,`customer`,`line1`,`line2`,`town`,`postcode`)
    VALUES(:id,:customers_addresses,:line1,:line2,:town,:postcode)
");
foreach( $upgrade['customers_addresses'] as $address ) {
    $customers_addresses->execute([
        ':id' => $address['id'],
        ':customers_addresses' => $address['customer'],
        ':line1' => $address['line1'],
        ':line2' => $address['line2'],
        ':town' => $address['town'],
        ':postcode' => $address['postcode']
    ]);
}

$customers_contacts = $newDB->prepare("
    INSERT INTO `customers_contacts` (`id`,`customer`,`name`,`email`,`telephone`)
    VALUES(:id,:customers,:customers_contact,:email,:telephone)
");
foreach( $upgrade['customers_contacts'] as $contact ) {
    $customers_contacts->execute([
        ':id' => $contact['id'],
        ':customers' => $contact['customer'],
        ':customers_contact' => $contact['name'],
        ':email' => $contact['email'],
        ':telephone' => $contact['telephone']
    ]);
}

$jobs = $newDB->prepare("
    INSERT INTO `jobs` (`id`,`name`,`customer`,`address`,`contact`,`startdate`,`enddate`,`jobType`,`quoteAgreed`,`lost`,`complete`,`invoiced`)
    VALUES(:id,:job,:customer,:address,:contact,:startdate,:enddate,:jobType,:quoteAgreed,:lost,:complete,:invoiced)
");
foreach( $upgrade['jobs'] as $j ) {
    $jobs->execute([
        ':id' => $j['id'],
        ':job' => $j['name'],
        ':customer' => $j['customer'],
        ':address' => $j['address'],
        ':contact' => $j['contact'],
        ':startdate' => $j['startdate'],
        ':enddate' => $j['enddate'],
        ':jobType' => $j['jobType'],
        ':quoteAgreed' => $j['quoteAgreed'],
        ':lost' => $j['lost'],
        ':complete' => $j['complete'],
        ':invoiced' => $j['invoiced']
    ]);
}

$jobs_cat = $newDB->prepare("
    INSERT INTO `jobs_cat` (`id`,`job`,`cat`) VALUES(:id,:job,:cat)
");
foreach( $upgrade['jobs_cat'] as $cat ) {
    $jobs_cat->execute([
        ':id' => $cat['id'],
        ':job' => $cat['job'],
        ':cat' => $cat['cat']
    ]);
}

$jobs_lines = $newDB->prepare("
    INSERT INTO `jobs_lines` (`id`,`job`,`linetype`,`stockEntry`,`stockEffect`,`price`,`cat`,`qty`,`itemName`,`parent`,`kit`,`cost`,`notes`,`dispatch`,`dispatch_date`,`return`,`return_date`,`mandatory`,`accType`)
    VALUES(:id,:job,:linetype,:stockEntry,:stockEffect,:price,:cat,:qty,:itemName,:parent,:kit,:cost,:notes,:dispatch,:dispatch_date,:return,:return_date,:mandatory,:accType)
");
foreach( $upgrade['jobs_lines'] as $line ) {
    if( isset( $line['mandatory' ]) ) {
        $mandatory = $line['mandatory'];
    } else {
        $mandatory = 0;
    }
    if( isset( $line['accType'] ) ) {
        $accType = $line['accType'];
    } else {
        $accType == NULL;
    }
    $jobs_lines->execute([
       ':id' => $line['id'],
        ':job' => $line['job'],
        ':linetype' => $line['linetype'],
        ':stockEntry' => $line['stockEntry'],
        ':stockEffect' => $line['stockEffect'],
        ':price' => $line['price'],
        ':cat' => $line['cat'],
        ':qty' => $line['qty'],
        ':itemName' => $line['itemName'],
        ':parent' => $line['parent'],
        ':kit' => $line['kit'],
        ':cost' => $line['cost'],
        ':notes' => $line['notes'],
        ':dispatch' => $line['dispatch'],
        ':dispatch_date' => $line['dispatch_date'],
        ':return' => $line['return'],
        ':return_date' => $line['return_date'],
        ':mandatory' => $mandatory,
        ':accType' => $accType
    ]);
}

$kit = $newDB->prepare("
    INSERT INTO `kit` (`id`,`name`,`purchasevalue`,`sloc`,`price`,`height`,`width`,`length`,`weight`,`notes`,`active`,`toplevel`,`cat`)
    VALUES(:id,:kit,:purchasevalue,:sloc,:price,:height,:width,:length,:weight,:notes,:active,:toplevel,:cat)
");
foreach( $upgrade['kit'] as $k ) {
    $kit->execute([
        ':id' => $k['id'],
        ':kit' => $k['name'],
        ':purchasevalue' => $k['purchasevalue'],
        ':sloc' => $k['sloc'],
        ':price' => $k['price'],
        ':height' => $k['height'],
        ':width' => $k['width'],
        ':length' => $k['length'],
        ':weight' => $k['weight'],
        ':notes' => $k['notes'],
        ':active' => $k['active'],
        ':toplevel' => $k['toplevel'],
        ':cat' => $k['cat']
    ]);
}

$kit_accessories = $newDB->prepare("
    INSERT INTO `kit_accessories` (`id`,`accessory`,`type`,`price`,`kit`,`qty`,`mandatory`) VALUES(:id,:accessory,:type,:price,:kit,:qty,:mandatory)
");
foreach( $upgrade['kit_accessories'] as $accessory ) {
    if( isset( $accessory['mandatory'] ) ) {
        $mandatory = $accessory['mandatory'];
    } else {
        $mandatory = 0;
    }
    $kit_accessories->execute([
        ':id' => $accessory['id'],
        ':accessory' => $accessory['accessory'],
        ':type' => $accessory['type'],
        ':price' => $accessory['price'],
        ':kit' => $accessory['kit'],
        ':qty' => $accessory['qty'],
        ':mandatory' => $mandatory
    ]);
}

$kit_stock = $newDB->prepare("
    INSERT INTO `kit_stock` (`id`,`kit`,`stock_count`,`serialized`,`serialnumber`,`purchasedate`)
    VALUES(:id,:kit,:stock_count,:serialized,:serialnumber,:purchasedate)
");
foreach( $upgrade['kit_stock'] as $stock ) {
    $kit_stock->execute([
        ':id' => $stock['id'],
        ':kit' => $stock['kit'],
        ':stock_count' => $stock['stock_count'],
        ':serialized' => $stock['serialized'],
        ':serialnumber' => $stock['serialnumber'],
        ':purchasedate' => $stock['purchasedate']
    ]);
}

$sloc = $newDB->prepare("
    INSERT INTO `sloc` (`id`,`name`,`address_line1`,`address_line2`,`town`,`postcode`)
    VALUES(:id,:sloc,:address_line1,:address_line2,:town,:postcode)
");
foreach( $upgrade['sloc'] as $s ) {
    $sloc->execute([
        ':id' => $s['id'],
        ':sloc' => $s['name'],
        ':address_line1' => $s['address_line1'],
        ':address_line2' => $s['address_line2'],
        ':town' => $s['town'],
        ':postcode' => $s['postcode']
    ]);
}
?>