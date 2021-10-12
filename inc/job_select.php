<?php
// Set the current header
$getCurrent = $db->prepare( "SELECT * FROM `jobs` WHERE `id` =:jobID lIMIT 1" );
$getCurrent->execute( [ ':jobID' => $id ] );
$job = $getCurrent->fetch( PDO::FETCH_ASSOC );
?>