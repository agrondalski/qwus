<?php

require 'includes.php';

$tid = $_POST['tourney_id'];
$division_id = $_POST['division_id'];
$team_id = $_POST['team_id'];

if ($tid == "") {
	$tid = $_REQUEST['tourney_id'];
}
if ($division_id == "") {
	$division_id = $_REQUEST['division_id'];
}
if ($team_id == "") {
	$team_id = $_REQUEST['team_id'];
}

$t  = new tourney(array('tourney_id'=>$tid));
$div = new division(array('division_id'=>$division_id));
$tm  = new team(array('team_id'=>$team_id));

$mode = $_REQUEST['mode'];

if ($mode=="delete") {

  if ($div->hasTeam($team_id) == true) {
	  try {
		  $div->removeTeam($team_id);
		  $msg = "<br>Team deleted from div!<br>";
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
	if ($div->hasTeam($team_id) == false) {
		try {
		  $div->addTeam($team_id);
		  $msg = "<br>Team added!<br>";
		}
		catch (Exception $e) {
		  $msg = "<br>Error adding!<br>";
		}
	} else {
		$msg = "<br>Error!<br>";
	} 
}
echo $msg;
include 'assignTeamsToDiv.php';
?>
