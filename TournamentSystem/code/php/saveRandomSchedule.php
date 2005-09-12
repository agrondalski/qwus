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

// Try to create a new schedule

$div->removeSchedule();

$nw = $_POST['num_weeks'];

//if ($nw == "") {
// default val of 8
//$nw = "8";
//}

$div->createSchedule($nw);
$msg = "New Schedule created!<br>";

echo $msg;
include 'manageSchedule.php';
?>
