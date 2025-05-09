<?php
// Insert data to new file
$newDB = new PDO( 'sqlite:' . $newFile );
$categories = $newDB->prepare( "INSERT INTO `categories` (`id`,`name`) VALUES(:id,:cat)" );
foreach( $upgrade['categories'] as $cat ) {
    $categories->execute( [ ':id' => $cat['id'] , ':cat' => $cat['name'] ] );
}

$company = $newDB->prepare("
    INSERT INTO `company` (`id`,`name`,`address_line1`,`address_line2`,`town`,`postcode`,`telephone`,`website`,`email`,`currencysymbol`,`lastbackup`,`appversion`,`welcome`,`logo`,`ftp_host`,`ftp_username`,`ftp_password`,`ftp_port`,`ftp_dir`,`ftp_backup`)
    VALUES(:id,:company,:address_line1,:address_line2,:town,:postcode,:telephone,:website,:email,:currencysymbol,:lastbackup,:appversion,:welcome,:logo,:ftp_host,:ftp_username,:ftp_password,:ftp_port,:ftp_dir,:ftp_backup)
");
foreach( $upgrade['company'] as $com ) {
    if( isset( $com['welcome'] ) ) {
        $welcome = $com['welcome'];
    } else {
        $welcome = 1;
    }
    if( isset( $com['logo'] ) ) {
        $logo = $com['logo'];
    } else {
        $logo = 1;
    }
    if( isset( $com['ftp_host'] ) ) {
        $ftp_host = $com['ftp_host'];
        $ftp_username = $com['ftp_username'];
        $ftp_password = $com['ftp_password'];
        $ftp_port = $com['ftp_port'];
        $ftp_dir = $com['ftp_dir'];
        $ftp_backup = $com['ftp_backup'];

    } else {
        $ftp_host = NULL;
        $ftp_username = NULL;
        $ftp_password = NULL;
        $ftp_port = 21;
        $ftp_dir = NULL;
        $ftp_backup = 0;
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
        ':welcome' => $welcome,
        ':logo' => $logo,
        ':ftp_host' => $ftp_host,
        ':ftp_username' => $ftp_username,
        ':ftp_password' => $ftp_password,
        ':ftp_port' => $ftp_port,
        ':ftp_dir' => $ftp_dir,
        ':ftp_backup' => $ftp_backup

    ]);
} 

$customers = $newDB->prepare("
    INSERT INTO `customers` (`id`,`name`,`creationdate`,`notes`,`company_number`,`vat_number`,`invoice_terms`,`hold`,`website`,`supplier`)
    VALUES(:id,:customer,:creationdate,:notes,:company_number,:vat_number,:invoice_terms,:hold,:website,:supplier)
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
        ':website' => $cus['website'],
        ':supplier' => $cus['supplier']
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
    INSERT INTO `jobs` (`id`,`name`,`customer`,`address`,`contact`,`startdate`,`enddate`,`jobType`,`quoteAgreed`,`lost`,`complete`,`invoiced`,`invoice_number`,`price_lock`)
    VALUES(:id,:job,:customer,:address,:contact,:startdate,:enddate,:jobType,:quoteAgreed,:lost,:complete,:invoiced,:invoice_number,:price_lock)
");
foreach( $upgrade['jobs'] as $j ) {
    if( isset( $j['price_lock'] ) ) {
        $price_lock = $j['price_lock'];
    } else {
        $price_lock = 0;
    }
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
        ':invoiced' => $j['invoiced'],
        ':invoice_number' => $j['invoice_number'],
        ':price_lock' => $price_lock
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
    INSERT INTO `jobs_lines` (`id`,`job`,`linetype`,`stockEntry`,`stockEffect`,`price`,`cat`,`qty`,`itemName`,`parent`,`kit`,`cost`,`notes`,`dispatch`,`dispatch_date`,`return`,`return_date`,`mandatory`,`accType`,`service_startdate`,`service_enddate`,`supplier`)
    VALUES(:id,:job,:linetype,:stockEntry,:stockEffect,:price,:cat,:qty,:itemName,:parent,:kit,:cost,:notes,:dispatch,:dispatch_date,:return,:return_date,:mandatory,:accType,:startdate,:enddate,:supplier)
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
    if( isset( $line['service_startdate'] ) ) {
        $service_startdate = $line['service_startdate'];
        $service_enddate = $line['service_enddate'];
    } else {
        $service_startdate = NULL;
        $service_enddate = NULL;
    }
    if( isset( $line['supplier'] ) ) {
        $supplier = $line['supplier'];
    } else {
        $supplier = 0;
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
        ':accType' => $accType,
        ':startdate' => $service_startdate,
        ':enddate' => $service_enddate,
        ':supplier' => $supplier
    ]);
}

$kit = $newDB->prepare("
    INSERT INTO `kit` (`id`,`name`,`purchasevalue`,`sloc`,`price`,`height`,`width`,`length`,`weight`,`notes`,`active`,`toplevel`,`cat`,`img`)
    VALUES(:id,:kit,:purchasevalue,:sloc,:price,:height,:width,:length,:weight,:notes,:active,:toplevel,:cat,:img)
");
foreach( $upgrade['kit'] as $k ) {
    if( isset( $k['img'] ) ) {
        $img = $k['img'];
    } else {
        $img = NULL;
    }
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
        ':cat' => $k['cat'],
        ':img' => $img
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
$licence = $newDB->prepare("
    INSERT INTO `licence` (`licencekey`) VALUES(:licKey)
");
foreach( $upgrade['licence'] as $lic ) {
    $licence->execute( [ ':licKey' => $lic['licencekey'] ] );
}

$repairs = $newDB->prepare("
    INSERT INTO `kit_repairs` (`id`,`kit`,`startdate`,`enddate`,`repairtype`,`notes`,`complete`,`stockeffect`,`description`,`cost`)
    VALUES(:id,:kit,:startdate,:enddate,:repairtype,:notes,:complete,:stockeffect,:description,:cost)
");
foreach( $upgrade['repairs'] as $repair ) {
    $repairs->execute([
        ':id' => $repair['id'],
        ':kit' => $repair['kit'],
        ':startdate' => $repair['startdate'],
        ':enddate' => $repair['enddate'],
        ':repairtype' => $repair['repairtype'],
        ':notes' => $repair['notes'],
        ':complete' => $repair['complete'],
        ':stockeffect' => $repair['stockeffect'],
        ':description'=> $repair['description'],
        ':cost' => $repair['cost']
    ]);
}
?>