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
	echo "<th>Matchup<th>Deadline Date</th><th>Week</th><th>Actions</th>";
	foreach ($div->getMatches() as $m) {
	$t1 = new team(array('team_id'=>$m->getValue('team1_id')));
	$t2= new team(array('team_id'=>$m->getValue('team2_id')));
		echo "\t<tr>\n";
		echo "\t<td>",$t1->getValue('name')," vs. ";
		echo "",$t2->getValue('name'),"</td>\n";
		echo "\t<td>",$m->getValue('deadline'),"</td>\n";
		echo "\t<td>",$m->getValue('week_name'),"</td>\n";
		echo "<td><a href='?a=saveSchedule&amp;tourney_id=$tid&amp;mode=delete&amp;division_id=",$division_id,"&amp;match_id=",$m->getValue('match_id'),"'>";
		echo "Delete</a></td>";
	}
	echo "</tr></table>";
	
// Show teams
echo "<form action='?a=saveSchedule' method=post>";
echo "<input type='hidden' name='tourney_id' value='$tid'>";
echo "<input type='hidden' name='division_id' value='$division_id'>";
echo "<table border=1 cellpadding=2 cellspacing=0>";
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
echo "<tr>";
echo "<td>Deadline :</td><td>";
echo "<input type='text' name='deadline' value='",date("Y-m-d"),"' size='20'></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Week Name:</td><td>";
echo "<input type='text' name='week_name' value='' size='50'></td>";
echo "</tr>";
echo "<tr><td>&nbsp;</td><td><input type='submit' value='Add' name='B1' class='button'>";
echo "<br></td></tr></table></form>";
}


?>
