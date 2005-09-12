<?php

require 'includes.php';

//tourney_id,division_id,team1_id,team2_id,deadline,week_name

$tid = $_POST['tourney_id'];
$division_id = $_POST['division_id'];

if ($tid == "") {
	$tid = $_REQUEST['tourney_id'];
}
if ($division_id == "") {
	$division_id = $_REQUEST['division_id'];
}


$t  = new tourney(array('tourney_id'=>$tid));
$div = new division(array('division_id'=>$division_id));

$mode = $_REQUEST['mode'];

if ($mode=="delete") {
	$match_id = $_REQUEST['match_id'];
	$m = new match(array('match_id'=>$match_id));

	try {
	  $m->delete();
	  $msg = "<br>Match deleted!<br>";
	}
	catch (Exception $e) {
	  $msg = "<br>Error deleting!<br>";
	}
  
}
else {
// add new
	if (($_POST['team1_id'] == $_POST['team2_id']) or ($_POST['team1_id']=="") or ($_POST['team2_id']=="") or ($_POST['week_name'] == "") or ($_POST['deadline'] == "")) {
		$msg = "<br>Error adding match!<br>";	
	} else {
		$match_id = $_REQUEST['match_id'];
		$m = new match(array('match_id'=>$match_id,
							 'division_id'=>$division_id,
							 'team1_id'=>$_POST['team1_id'],
							 'team2_id'=>$_POST['team2_id'],
							 'deadline'=>$_POST['deadline'],
							 'week_name'=>$_POST['week_name']));
		$msg = "<br>Match added!<br>";
	}

}
echo $msg;
include 'manageSchedule.php';
?>
