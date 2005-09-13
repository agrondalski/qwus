<?php

require 'includes.php';

$tid = $_POST['tourney_id'];
$division_id = $_POST['division_id'];

// Create the new division 

$mode = $_REQUEST['mode'];

if ($mode=="edit") {
	$schedule_id = $_POST['schedule_id'];
	$ms = new match_schedule(array('schedule_id'=>$schedule_id));

	$ms->update('name',$_POST['name']);
	$ms->update('deadline',$_POST['deadline']);
	$msg = "<br>Updated!<br>";
}
elseif ($mode=="delete") {
	$schedule_id = $_REQUEST['schedule_id'];
	$ms = new match_schedule(array('schedule_id'=>$schedule_id));
	try {
		$ms->delete();
		$msg = "<br>Deleted!<br>";
	}
	catch (Exception $e) {
		$msg = "<br>Error deleting!<br>";
	}
}
else {
	$ms = new match_schedule(array('division_id'=>$_POST['division_id'],
                              	   'name'=>$_POST['name'],
                              	   'deadline'=>$_POST['deadline']));

$msg = "<br>Created!<br>";
}
include 'listMatchSchedule.php';
echo $msg
?>
