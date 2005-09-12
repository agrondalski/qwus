<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];
$division_id = $_REQUEST['division_id'];

$t = new tourney(array('tourney_id'=>$tid));
try {
	$div = new division(array('division_id'=>$division_id));
} catch (Exception $e) {
    $div = "";
}

include 'tourneyLinks.php';
echo "<br>";
// Printing results in HTML
echo "<form action='?a=manageSchedule' method=post>";
echo "<table border=0 cellpadding=2 cellspacing=0>";
echo "<tr><td><b>Pick a division:</b></td>";
echo "<input type='hidden' name='tourney_id' value='$tid'>";
echo "<td><select name='division_id'>";
$divlist = $t->getDivisions();
foreach ($divlist as $tmp) {
	$sel = "";
	if ($tmp->getValue('division_id') == $division_id) {
		$sel = "selected";
	}
	echo "<option value='",$tmp->getValue('division_id'),"' ",$sel,">",$tmp->getValue('name');
}
echo "</select></td></tr>";
echo "<tr><td>&nbsp;</td><td><input type='submit' value='Okay' name='B1' class='button'>";
echo "<br></td></tr>";
echo "</table></form>";
if ($div != "") {

	// List players in this team
	echo "<table border=1 cellpadding=2 cellspacing=0>\n";
	echo "<th>Matchup</th><th>Week</th><th>Deadline</th><th>Actions</th>";
	foreach ($div->getMatches() as $m) {
	$t1 = new team(array('team_id'=>$m->getValue('team1_id')));
	$t2 = new team(array('team_id'=>$m->getValue('team2_id')));
	$s  = new match_schedule(array('schedule_id'=>$m->getValue('schedule_id')));
		echo "\t<tr>\n";
		echo "\t<td>",$t1->getValue('name')," vs. ";
		echo "",$t2->getValue('name'),"</td>\n";
		//echo "<td>name</td><td>deadline</td>";
		echo "\t<td>",$s->getValue('name'),"</td>\n";
		echo "\t<td>",$s->getValue('deadline'),"</td>\n";
		echo "<td><a href='?a=saveSchedule&amp;tourney_id=$tid&amp;mode=delete&amp;division_id=",$division_id,"&amp;match_id=",$m->getValue('match_id'),"'>";
		echo "Delete</a></td>";
	}
	echo "</tr></table>";
	
	// Randomly Generate schedule button
echo "<form action='?a=saveRandomSchedule' method=post>";
echo "<input type='hidden' name='tourney_id' value='$tid'>";
echo "<input type='hidden' name='division_id' value='$division_id'>";
echo "<table border=1 cellpadding=2 cellspacing=0>";
echo "<tr>";
echo "<td>Weeks to play:</td>";
echo "<td><input type='text' name='num_weeks' value='' size='10'></td>";
echo "</tr>";
echo "<tr><td colspan=2><input type='submit' value='Generate Random Schedule' name='B1' class='button'></td>";
echo "</tr></table></form>";
	
// Show teams
echo "<form action='?a=saveSchedule' method=post>";
echo "<input type='hidden' name='tourney_id' value='$tid'>";
echo "<input type='hidden' name='division_id' value='$division_id'>";
echo "<table border=1 cellpadding=2 cellspacing=0>";
echo "<tr><td colspan=2><b>Schedule a Match:</b></td></tr>";
echo "<tr><td><b>Team 1:</b></td>";
echo "<td><select name='team1_id'>";
$tlist = $div->getTeams();
foreach ($tlist as $tmp) {
	echo "<option value='",$tmp->getValue('team_id'),"'>",$tmp->getValue('name');
}
echo "</select></td></tr>";
echo "<tr><td><b>Team 2:</b></td>";
echo "<td><select name='team2_id'>";
$tlist = $div->getTeams();
foreach ($tlist as $tmp) {
	echo "<option value='",$tmp->getValue('team_id'),"'>",$tmp->getValue('name');
}
echo "</select></td></tr>";
echo "<tr><td><b>Scheduled:</b></td>";
echo "<td><select name='schedule_id'>";
$slist = $div->getMatchSchedule();
foreach ($slist as $tmp) {
	echo "<option value='",$tmp->getValue('schedule_id'),"'>",$tmp->getValue('name'),":",$tmp->getValue('deadline');
}
echo "</select></td></tr>";
echo "<tr><td>&nbsp;</td><td><input type='submit' value='Add' name='B1' class='button'>";
echo "<br></td></tr></table></form>";
}


?>