<?php

require 'includes.php';

$tid = $_POST['tourney_id'];
if ($tid == "") {
	$tid = $_REQUEST['tourney_id'];
}
$t = new tourney(array('tourney_id'=>$tid));

// Create the new division 

$mode = $_REQUEST['mode'];

if ($mode=="delete") {

  if ($t->hasMap($_REQUEST['map_id']) == true) {
	  try {
		  $t->removeMap($_REQUEST['map_id']);
		  $msg = "<br>Map deleted from tourney!<br>";
		}
		catch (Exception $e) {
		  $msg = "<br>Error deleting!<br>";
	  }
  } else {
  	$msg = "<br>Error!<br>";
  } 
  
}
else {
// add new
	if ($t->hasMap($_POST['map_id']) == false) {
		try {
		  $t->addMap($_POST['map_id']);
		  $msg = "<br>Map added!<br>";
		}
		catch (Exception $e) {
		  $msg = "<br>Error adding!<br>";
		}
	} else {
		$msg = "<br>Error!<br>";
	} 
}
echo $msg;
include 'assignMapsToTourney.php';
//echo "<a href='?a=assignMapsToTourney&amp;tourney_id=$tid'>Add Another Map</a><br>";
?>
