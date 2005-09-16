<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];
$division_id = $_REQUEST['division_id'];
$match_id = $_REQUEST['match_id'];
$winning_team_id = $_REQUEST['winning_team_id'];
$filename = $_REQUEST['filename'];

$t = new tourney(array('tourney_id'=>$tid));

try 
{
	$div = new division(array('division_id'=>$division_id));
} 
catch (Exception $e) 
{
    $div = "";
}

try 
{
	$m = new match(array('match_id'=>$match_id));
} 
catch (Exception $e) 
{
    $m = "";
}

include 'tourneyLinks.php';
echo "<br>";

// *** PART 1
echo "<h2>Report a Match</h2>";
// Pick a division
echo "<form action='?a=reportMatch' method=post>";
echo "<table border=0 cellpadding=2 cellspacing=0>";
echo "<tr><td><b>Pick a division:</b></td>";
echo "<input type='hidden' name='tourney_id' value='$tid'>";
if ($div != "") 
{
	$dis = "disabled";
}
echo "<td><select name='division_id' $dis>";
$divlist = $t->getDivisions();
foreach ($divlist as $tmp) 
{
	$sel = "";
	if ($tmp->getValue('division_id') == $division_id) 
	{
		$sel = "selected";
	}
	echo "<option value='",$tmp->getValue('division_id'),"' ",$sel,">",$tmp->getValue('name');
}
echo "</select></td></tr>";
echo "<tr><td>&nbsp;</td><td><input type='submit' value='Okay' name='B1' class='button' $dis>";
echo "<br></td></tr>";
echo "</table></form>";

$dis = "";

// *** PART 2
if ($div != "") 
{
	if ($match_id != "") 
	{
		$dis = "disabled";
	}
	echo "<form action='?a=reportMatch' method=post>";
	echo "<table border=0 cellpadding=2 cellspacing=0>";
	echo "<tr><td><b>Pick a match:</b></td>";
	echo "<input type='hidden' name='tourney_id' value='$tid'>";
	echo "<input type='hidden' name='division_id' value='$division_id'>";
	echo "<td><select name='match_id' $dis>";
	$mlist = $div->getMatches();
	foreach ($mlist as $tmp) 
	{
		$t1 = new team(array('team_id'=>$tmp->getValue('team1_id')));
		$t2 = new team(array('team_id'=>$tmp->getValue('team2_id')));
		$s  = new match_schedule(array('schedule_id'=>$tmp->getValue('schedule_id')));
		$sel = "";
		if ($tmp->getValue('match_id') == $match_id) 
		{
			$sel = "selected";
		}
		echo "<option value='",$tmp->getValue('match_id'),"' ",$sel,">",$t1->getValue('name')," vs ",$t2->getValue('name')," (",$s->getValue('name'),")";
	}
	echo "</select></td></tr>";
	echo "<tr><td>&nbsp;</td><td><input type='submit' value='Okay' name='B1' class='button' $dis>";
	echo "<br></td></tr>";
	echo "</table></form>";
}
echo "<hr>";

$dis = "";

// *** PART 3
if ($match_id != "") 
{
	echo "<h2>Match Details</h2>";
	echo "<form action='?a=reportMatch' method=post>";
	echo "<table border=0 cellpadding=2 cellspacing=0>";
	echo "<tr><td><b>Who won?</b></td>";
	echo "<input type='hidden' name='tourney_id' value='$tid'>";
	echo "<input type='hidden' name='division_id' value='$division_id'>";
	echo "<input type='hidden' name='match_id' value='$match_id'>";
	echo "<td><select name='winning_team_id'>";
	// $m is the match object
	$t1 = new team(array('team_id'=>$m->getValue('team1_id')));
	$t2 = new team(array('team_id'=>$m->getValue('team2_id')));
	$s  = new match_schedule(array('schedule_id'=>$m->getValue('schedule_id')));
	echo "<option value='",$t1->getValue('team_id'),"'>",$t1->getValue('name'),"";
	echo "<option value='",$t2->getValue('team_id'),"'>",$t2->getValue('name'),"";
	echo "</select></td></tr>";
	echo "<tr><td><b>Match Approved?</b></td>";
	echo "<td><input type='checkbox' name='approved' value='1'></td></tr>";
	echo "<tr><td>&nbsp;</td><td><input type='submit' value='Okay' name='B1' class='button'>";
	echo "<br></td></tr>";
	echo "</table></form>";
}

// *** PART 4
if ($winning_team_id != "") 
{
	echo "<hr>";
	echo "<h2>Add Game Data</h2>";
	echo "<form action='?a=reportMatch' method=post>";
	echo "<table border=0 cellpadding=4 cellspacing=0>";
	echo "<tr><td><b>Add game MVD:</b></td>";
	echo "<input type='hidden' name='tourney_id' value='$tid'>";
	echo "<input type='hidden' name='division_id' value='$division_id'>";
	echo "<input type='hidden' name='match_id' value='$division_id'>";
	echo "<input type='hidden' name='winning_team_id' value='$winning_team_id'>";
	echo "<td><input type='file' name='filename'></td>";
	echo "<td><input type='submit' value='Submit' name='B1' class='button'></td></tr>";
	echo "</form>";
	echo "<tr><td colspan=3 align=center><b>OR</b></td></tr>";
	echo "<form action='?a=reportMatch' method=post>";
	echo "<input type='hidden' name='tourney_id' value='$tid'>";
	echo "<input type='hidden' name='division_id' value='$division_id'>";
	echo "<input type='hidden' name='match_id' value='$division_id'>";
	echo "<input type='hidden' name='winning_team_id' value='$winning_team_id'>";
	echo "<tr><td nowrap colspan=2><b>Manually add a game:</b></td>";
	echo "<td><input type='submit' value='Okay' name='B1' class='button'></td></tr>";
	echo "</table></form>";
}

// *** PART 5
if ($filename != "") 
{
	echo "<b>$filename</b> was uploaded!<br>";
}

//try
//{
//  $p = new player(array('name'=>$_SESSION['username'])) ;
//  if ($p->hasColumn())
//  if ($p->isSuperAdmin())
//  if ($p->isTourneyAdmin())
//}
//catch(Exception $e){}
?>

