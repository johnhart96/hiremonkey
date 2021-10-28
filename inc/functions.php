<?php
function modalButton( $id , $text ) {
    echo '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#' . $id . '">';
    echo $text;
    echo '</button>';
  }
  function modalButton_green( $id , $text ) {
    echo '<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#' . $id . '">';
    echo $text;
    echo '</button>';
  }
  function modalButton_red( $id , $text ) {
    echo '<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#' . $id . '">';
    echo $text;
    echo '</button>';
  }
  function modal( $id , $title , $content , $buttons ) {
    $buttons = explode( " " , $buttons );
    echo "<form method='post'>";
    echo "<div class='modal fade' id=" . $id . " tabindex='-1' role='dialog' aria-labelledby='" . $title . "' aria-hidden='true'>";
    echo "<div class='modal-dialog'>";
    echo "<div class='modal-content'>";
    echo "<div class='modal-header'>";
    echo "<h5 class='modal-title' id='exampleModalLabel'>" . $title . "</h5>";
    echo "<button type='button' class='close' data-bs-dismiss='modal' aria-label='Close'>";
    echo "<span aria-hidden='true'>&times;</span>";
    echo "</button>";
    echo "</div>";
    echo "<div class='modal-body'>";
    echo $content;
    echo "</div>";
    echo "<div class='modal-footer'>";
    $buttonCount = count( $buttons , COUNT_RECURSIVE );
    echo "<button type='submit' class='btn btn-primary'>" . $buttons['0'] . "</button>";
    if( $buttonCount > 1 ) {
       echo "&nbsp;";
         echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>" . $buttons['1'] . "</button>";
    }
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</form>";
  }
function trial() {
  global $db;
  $checkForLicence = $db->query( "SELECT * FROM `licence` LIMIT 1" );
  $fetch = $checkForLicence->fetch( PDO::FETCH_ASSOC );
  if( isset( $fetch['licencekey'] ) ) {
    if( $fetch['licencekey'] == "trial" ) {
      return TRUE;
    } else {
      return FALSE;
    }
  } else {
    return TRUE;
  }
}
function licence( $info ) {
  global $db;
  $checkForLicence = $db->query( "SELECT * FROM `licence` ORDER BY `id` ASC LIMIT 1" );
  $fetch = $checkForLicence->fetch( PDO::FETCH_ASSOC );
  if( isset( $fetch['licencekey'] ) ) {
    return $fetch[$info];
  } else {
    return "trial";
  }
}
function entry_count( $object ) {
  global $db;
  switch( $object ) {
    case "customers":
      $getCustomers = $db->query( "SELECT * FROM `customers`" );
      $fetch = $getCustomers->fetch( PDO::FETCH_ASSOC );
      if( isset( $fetch['id'] ) ) {
        $count = 1;
        while( $fetch = $getCustomers->fetch( PDO::FETCH_ASSOC ) ) {
          $count ++;
        }
      } else {
        $count = 0;
      }
      break;
    case "kit":
      $getKit = $db->query( "SELECT * FROM `kit`" );
      $fetch = $getKit->fetch( PDO::FETCH_ASSOC );
      if( isset( $fetch['id'] ) ) {
        $count = 1;
        while( $fetch = $getKit->fetch( PDO::FETCH_ASSOC ) ) {
          $count ++;
        }
      } else {
        $count = 0;
      }
      break;
    case "jobs":
      $getJobs = $db->query( "SELECT * FROM `jobs`" );
      $fetch = $getJobs->fetch( PDO::FETCH_ASSOC );
      if( isset( $fetch['id'] ) ) {
        $count = 1;
        while( $fetch = $getJobs->fetch( PDO::FETCH_ASSOC ) ) {
          $count ++;
        }
      } else {
        $count = 0;
      }
      break;
    case "cats":
      $getCats = $db->query( "SELECT * FROM `categories`" );
      $fetch = $getCats->fetch( PDO::FETCH_ASSOC );
      if( isset( $fetch['id'] ) ) {
        $count = 1;
        while( $fetch = $getCats->fetch( PDO::FETCH_ASSOC ) ) {
          $count ++;
        }
      } else {
        $count = 0;
      }
      break;
      case "sloc":
        $getSloc = $db->query( "SELECT * FROM `sloc`" );
        $fetch = $getSloc->fetch( PDO::FETCH_ASSOC );
        if( isset( $fetch['id'] ) ) {
          $count = 1;
          while( $fetch = $getSloc->fetch( PDO::FETCH_ASSOC ) ) {
            $count ++;
          }
        } else {
          $count = 0;
        }
        break;
  }
  return $count;
}
function go( $l ) {
  echo "<script>window.location='$l'</script>"; 
}
function company( $info ) {
  global $db;
  $getCompany = $db->query( "SELECT * FROM `company` ORDER BY `id` ASC LIMIT 1" );
  $fetch = $getCompany->fetch( PDO::FETCH_ASSOC );
  return $fetch[$info];
}
function sloc( $id ) {
  global $db;
  $getSloc = $db->prepare( "SELECT `name` FROM `sloc` WHERE `id` =:id" );
  $getSloc->execute( [ ':id' => $id ] );
  $fetch = $getSloc->fetch( PDO::FETCH_ASSOC );
  if( empty( $id ) ) {
    return "<em>None</em>";
  } else {
    return $fetch['name'];
  }
}
function price( $price ) {
  return number_format( $price , 2 );
}
function totalStockCount( $id ) {
  global $db;
  $getAllKit = $db->prepare( "SELECT * FROM `kit_stock` WHERE `kit` =:kitID" );
  $getAllKit->execute( [ ':kitID' => $id ] );
  $net = 0;
  while( $row = $getAllKit->fetch( PDO::FETCH_ASSOC ) ) {
    if( (int)$row['serialized'] == 1 ) {
      $net ++;
    } else {
      $net = $net + (int)$row['stock_count'];
    }
  }
  return $net;
}
function customer( $id ) {
  global $db;
  $getCustomer = $db->prepare( "SELECT `name` FROM `customers` WHERE `id` =:id LIMIT 1" );
  $getCustomer->execute( [ ':id' => $id ] );
  $fetch = $getCustomer->fetch( PDO::FETCH_ASSOC );
  if( empty( $id ) ) {
    return "<em>None</em>";
  } else {
    return $fetch['name'];
  }
}
function cat( $id ) {
  global $db;
  $getCat = $db->prepare( "SELECT `name` FROM `categories` WHERE `id` =:id LIMIT 1" );
  $getCat->execute( [ ':id' => $id ] );
  $fetch = $getCat->fetch( PDO::FETCH_ASSOC );
  if( empty( $id ) ) {
    return "<em>None</em>";
  } else {
    return $fetch['name'];
  }
}
function avlb( $product , $date1 , $date2 ) {
  global $db;
  global $id;
  echo "<script>console.log('Starting stock check');</script>";

  // Get stock held
  $stock_held = 0;
  $getStock = $db->prepare( "SELECT * FROM `kit_stock` WHERE `kit` =:kitID" );
  $getStock->execute( [ ':kitID' => $product ] );
  while( $kit_stock = $getStock->fetch( PDO::FETCH_ASSOC ) ) {
    if( $kit_stock['serialized'] == 1 ) {
      $stock_held ++;
    } else {
      $stock_held = $stock_held + (int)$kit_stock['stock_count'];
    }
  }
  $balence = $stock_held;
  echo "<script>console.log('" . $balence . " are held.');</script>";

  // Get jobs between these dates
  $getJobs = $db->prepare( "SELECT * FROM `jobs` WHERE `startdate` BETWEEN :date1 AND :date2" );
  $getJobs->execute( [ ':date1' => $date1 , ':date2' => $date2 ] );
  while( $job = $getJobs->fetch( PDO::FETCH_ASSOC )  ) {
    // Check to see if this item is on the job
    if( (int)$job['complete'] == 0 ) {
      $jobID = $job['id'];
      echo "<script>console.log('Found active job `$jobID`');</script>";
      $jobID = (int)$job['id'];
      $checkForStockOnJob = $db->prepare( "SELECT * FROM `jobs_lines` WHERE `kit` =:kitID AND `job` =:jobID" );
      $checkForStockOnJob->execute( [ ':kitID' => $product , ':jobID' => $jobID ] );
      while( $line = $checkForStockOnJob->fetch( PDO::FETCH_ASSOC ) ) {
        if( $line['linetype'] == "hire" ) {
          $d = $line['stockEffect'];
          echo "<script>console.log('$d');</script>";
          $balence = $balence + (int)$line['stockEffect'];
        }
      }
    }
  }
  echo "<script>console.log('" . $balence . " are avlb.');</script>";
  return $balence;
}
function discount_to_percent( $decimel ) {
  if( $decimel == 1 or $decimel == 1.0 ) {
    return 0;
  } else if( $decimel == 0 ) {
    return 100;
  } else {
    $percent = str_replace( "0." , "" , $decimel );
    if( strlen( $percent !== 2 ) ) {
      $percent .= "0";
    }
    return 100 - (int)$percent;
  }
}
function discount_to_decimel( $percent ) {
  if( $percent == 100 ) {
    return 0;
  } else if( $percent == 0 ) {
    return 1;
  } else {
    $decimel = "0." . $percent;
    $decimel = (double)$decimel;
    return 1.0 - $decimel;
  }
}
?>