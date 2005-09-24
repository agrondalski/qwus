<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];
$team_id = $_REQUEST['team_id'];
$player_id = $_REQUEST['player_id'];

$t = new tourney(array('tourney_id'=>$tid));
try {
	$tm = new team(array('team_id'=>$team_id));
	$p  = new player(array('player_id'=>$player_id));
} catch (Exception $e) {
    $tm = "";
    $p = "";
}

include 'userLinks.php';
echo "<br>";


$team_id = $_REQUEST['team_id'];
$player = $p;

$loc = new location(array('location_id'=>$player->getValue('location_id')));
$loc_name = $loc->getValue('country_name') ;

echo "Team: <a href='?a=detailsTeam&amp;tourney_id=",$tid,"&amp;team_id=",$tm->getValue('team_id'),"'>";
echo $tm->getValue('name'),"</a><p></p>";

echo "<table border=1 cellpadding=4 cellspacing=0>\n";
echo "<th>Name</th><th>Location</th><th>GP</th><th>F/G</th><th>Frags</th><th>Record with</th><th>+/-</th>";
// List player info
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
$info = $player->getTourneyStats($tid);

echo "\t<td>",$loc_name,"</td>\n";
echo "<td>",$info['games_played'],"</td>";
echo "<td>",$info['frags_per_game'],"</td>";
echo "<td>",$info['total_frags'],"</td>";
echo "<td>",$info['matches_won'],"-",$info['matches_lost'],"</td>";
echo "<td nowrap>",$info['frag_diff'],"</td>";
echo "</tr></table>";

echo "<br><b>Recent Games</b><br>";
//echo "<table>" ;

foreach($player->getRecentGameStats($tid, array('limit'=>5)) as $g)
{
  echo $g['frags'] . ' frags vs ' . $g['vs_team'] . ', (' . $g['map_abbr'] . ':' . $g['score'] . ')<br>' ;
  //echo '<tr><td>' . $g['frags'] . ' frags vs ' . $g['vs_team'] . ', (' . $g['map_abbr'] . ':' . $g['score'] . ')</td></tr>' ;
}

//echo '</table>' ;
?>
