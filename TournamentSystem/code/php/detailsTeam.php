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

// Gather team info
$team_id = $_REQUEST['team_id'];
$tm = new team(array('team_id'=>$team_id));
$name=$tm->getValue('name');
$name_abbr=$tm->getValue('name_abbr');
$email=$tm->getValue('email');
$irc_channel=$tm->getValue('irc_channel');
$location_id=$tm->getValue('location_id');
$loc = new location(array('location_id'=>$location_id));
$loc_name = $loc->getValue('country_name').":".$loc->getValue('state_name');
$password=$tm->getValue('password');
$approved=$tm->getValue('approved');

echo "<table border=1 cellpadding=4 cellspacing=0>\n";
echo "<tr><td><b>Team</b></td><td>",$name," (",$name_abbr,")</td></tr>\n";
echo "<tr><td><b>Email</b></td><td><a href='mailto:",$email,"'>",$email,"</a></td></tr>\n";
echo "<tr><td><b>IRC</b></td><td>",$irc_channel,"</td></tr>\n";
echo "</table><br>";
// List players in this team
echo "<table border=1 cellpadding=4 cellspacing=0>\n";
echo "<th>Name</th><th>Location</th><th>GP</th><th>F/G</th><th>Frags</th><th>Record with</th>";
foreach ($tm->getPlayers($tid) as $player) 
{
	$loc = new location(array('location_id'=>$player->getValue('location_id')));
	$loc_name = $loc->getValue('country_name').":".$loc->getValue('state_name');
	echo "\t<tr>\n<td>";
	echo "<a href='?a=detailsPlayer&amp;tourney_id=",$tid,"&amp;team_id=",$team_id,"&amp;player_id=",$player->getValue('player_id'),"'>";
		$tlp = $tm->getTeamLeader($tid);
		if ($tlp != null) {
			if ($tlp->getValue('player_id') == $player->getValue('player_id')) {
				echo "<font color=red>",$player->getValue('name'),"</font></a></td>\n";
			} else {
				echo $player->getValue('name'),"</a></td>\n";
			}
		} else {
			echo $player->getValue('name'),"</a></td>\n";
		}
	echo "\t<td>",$loc_name,"</td>\n";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
}
echo "</tr></table>";
echo "<p><font color=red>Red = team leader</font></p>";

?>
