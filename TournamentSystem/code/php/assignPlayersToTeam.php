<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];
$team_id = $_REQUEST['team_id'];

$t = new tourney(array('tourney_id'=>$tid));
try {
	$tm = new team(array('team_id'=>$team_id));
} catch (Exception $e) {
    $tm = "";
}

include 'tourneyLinks.php';
echo "<br>";
// Printing results in HTML
echo "<form action='?a=assignPlayersToTeam' method=post>";
echo "<table border=0 cellpadding=2 cellspacing=0>";
echo "<tr><td><b>Pick a team:</b></td>";
echo "<input type='hidden' name='tourney_id' value='$tid'>";
echo "<td><select name='team_id'>";
$teamlist = team::getAllTeams();
foreach ($teamlist as $tmp) {
	$sel = "";
	if ($tmp->getValue('team_id') == $team_id) {
		$sel = "selected";
	}
	echo "<option value='",$tmp->getValue('team_id'),"' ",$sel,">",$tmp->getValue('name');
}
echo "</select></td></tr>";
echo "<tr><td>&nbsp;</td><td><input type='submit' value='Okay' name='B1' class='button'>";
echo "<br></td></tr>";
echo "</table></form>";
if ($tm != "") {
	//echo "team is set!!!<br>";
	//echo "team_id=".$team_id."<br>";

	// List players in this team
	echo "<table border=1 cellpadding=2 cellspacing=0>\n";
	echo "<th>Name</th><th>Super<br>Admin?</th><th>Tourney<br>Admin?</th><th>Location</th><th>Actions</th>";
	foreach ($tm->getPlayers($tid) as $player) {
		$loc = new location(array('location_id'=>$player->getValue('location_id')));
		$loc_name = $loc->getValue('country_name').":".$loc->getValue('state_name');
		echo "\t<tr>\n";
		echo "\t<td>",$player->getValue('name'),"</td>\n";
		echo "\t<td>",$player->getValue('superAdmin'),"</td>\n";
		$ta = $player->isTourneyAdmin($tid);
		if ($ta == true) {
			$ta = "1";
		} else {
			$ta = "0";
		}
		echo "\t<td>",$ta,"</td>\n";
		echo "\t<td>",$loc_name,"</td>\n";
		echo "<td><a href='?a=saveTeamPlayer&amp;tourney_id=$tid&amp;mode=delete&amp;team_id=",$team_id,"&amp;player_id=",$player->getValue('player_id'),"'>
Delete</a></td>";
	}
	echo "</tr></table>";
	
// Show players
echo "<form action='?a=saveTeamPlayer' method=post>";
echo "<table border=0 cellpadding=2 cellspacing=0>";
echo "<tr><td><b>Pick a player:</b></td>";
echo "<input type='hidden' name='tourney_id' value='$tid'>";
echo "<input type='hidden' name='team_id' value='$team_id'>";
echo "<td><select name='player_id'>";
$plist = player::getAllPlayers();
foreach ($plist as $tmp) {
	echo "<option value='",$tmp->getValue('player_id'),"'>",$tmp->getValue('name');
}
echo "</select></td></tr>";
echo "<tr><td>Team Leader?&nbsp;</td><td><input type='checkbox' name='isteamleader'></td></tr>";
echo "<tr><td>&nbsp;</td><td><input type='submit' value='Add' name='B1' class='button'>";
echo "<br></td></tr></table></form>";
	

} else {
	//echo "pick a team!";
}

?>
