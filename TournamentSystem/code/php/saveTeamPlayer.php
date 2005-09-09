<?php

require 'includes.php';

$tid = $_POST['tourney_id'];
$team_id = $_POST['team_id'];
$player_id = $_POST['player_id'];

if ($tid == "") {
	$tid = $_REQUEST['tourney_id'];
}
if ($team_id == "") {
	$team_id = $_REQUEST['team_id'];
}
if ($player_id == "") {
	$player_id = $_REQUEST['player_id'];
}

$t  = new tourney(array('tourney_id'=>$tid));
$tm = new team(array('team_id'=>$team_id));
$p  = new player(array('player_id'=>$player_id));

$mode = $_REQUEST['mode'];

if ($mode=="delete") {

  if ($tm->hasPlayer($tid,$player_id) == true) {
	  try {
		  $tm->removePlayer($tid,$player_id);
		  $msg = "<br>Player deleted from team!<br>";
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
	if ($tm->hasPlayer($tid, $player_id) == false) {
		try {
		  $tm->addPlayer($tid, $player_id, $_POST['isteamleader']);
		  $msg = "<br>Player added!<br>";
		}
		catch (Exception $e) {
		  $msg = "<br>Error adding!<br>";
		}
	} else {
		$msg = "<br>Error!<br>";
	} 
}
echo $msg;
include 'assignPlayersToTeam.php';
?>
