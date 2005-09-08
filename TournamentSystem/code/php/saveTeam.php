<?php

require 'includes.php';

$tid = $_POST['tourney_id'];

// Create the new division 

$mode = $_REQUEST['mode'];

if ($mode=="edit") {
	$team_id = $_POST['team_id'];
	$tm = new team(array('team_id'=>$team_id));
	$tm->update('name',$_POST['name']);
	$tm->update('email',$_POST['email']);
	$tm->update('irc_channel',$_POST['irc_channel']);
	$tm->update('location_id',$_POST['location_id']);
	$tm->update('password',$_POST['password']);
	if ($_POST['approved'] == "1") {
		$appr = "1";
	} else {
		$appr = "0";
	}
	$tm->update('approved',$appr);
 
  $msg = "<br>Team updated!<br>";
}
elseif ($mode=="delete") {

  $team_id = $_REQUEST['team_id'];
  $tm = new team(array('team_id'=>$team_id));
  try {
    $tm->delete();
    $msg = "<br>Team deleted!<br>";
  }
  catch (Exception $e) {
    $msg = "<br>Error deleting!<br>";
  }
}
else {
	if ($_POST['approved'] == "1") {
		$appr = "1";
	} else {
		$appr = "0";
	}
	$tm = new team(array('name'=>$_POST['name'],
					   'email'=>$_POST['email'],
					   'irc_channel'=>$_POST['irc_channel'],
					   'location_id'=>$_POST['location_id'],
					   'password'=>$_POST['password'],
					   'approved'=>$appr));
	
$msg = "<br>New team created!<br>";
}
include 'listTeams.php';
echo $msg
?>
