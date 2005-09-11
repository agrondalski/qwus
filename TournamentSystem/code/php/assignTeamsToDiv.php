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
echo "<form action='?a=assignTeamsToDiv' method=post>";
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
	echo "<th>Name</th><th>Email</th><th>IRC Chan</th><th>Location</th><th>Approved?</th><th>Actions</th>";
	foreach ($div->getTeams() as $tm) {
		$loc = new location(array('location_id'=>$tm->getValue('location_id')));
		$loc_name = $loc->getValue('country_name').":".$loc->getValue('state_name');
		echo "\t<tr>\n";
		echo "\t<td>",$tm->getValue('name'),"</td>\n";
		echo "\t<td>",$tm->getValue('email'),"</td>\n";
		echo "\t<td>",$tm->getValue('irc_channel'),"</td>\n";
		echo "\t<td>",$loc_name,"</td>\n";
		echo "\t<td>",$tm->getValue('approved'),"</td>\n";
		echo "<td><a href='?a=saveDivTeam&amp;tourney_id=$tid&amp;mode=delete&amp;division_id=",$division_id,"&amp;team_id=",$tm->getValue('team_id'),"'>";
		echo "Delete</a></td>";
	}
	echo "</tr></table>";
	
// Show teams
echo "<form action='?a=saveDivTeam' method=post>";
echo "<table border=0 cellpadding=2 cellspacing=0>";
echo "<tr><td><b>Pick a Team:</b></td>";
echo "<input type='hidden' name='tourney_id' value='$tid'>";
echo "<input type='hidden' name='division_id' value='$division_id'>";
echo "<td><select name='team_id'>";
$tlist = team::getAllTeams();
foreach ($tlist as $tmp) {
	echo "<option value='",$tmp->getValue('team_id'),"'>",$tmp->getValue('name');
}
echo "</select></td></tr>";
echo "<tr><td>&nbsp;</td><td><input type='submit' value='Add' name='B1' class='button'>";
echo "<br></td></tr></table></form>";
}

?>
