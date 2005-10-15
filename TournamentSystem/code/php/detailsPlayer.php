<?php
require_once 'includes.php';
include 'userLinks.php';
echo "<br>";

$tid = $_REQUEST['tourney_id'];
$team_id = $_REQUEST['team_id'];
$player_id = $_REQUEST['player_id'];

$t = new tourney(array('tourney_id'=>$tid));
try
{
  $tm = new team(array('team_id'=>$team_id));
  $p  = new player(array('player_id'=>$player_id));
}
catch (Exception $e)
{
    $tm = "";
    $p = "";
}

$team_id = $_REQUEST['team_id'];
$player = $p;

$loc = new location(array('location_id'=>$player->getValue('location_id')));
$loc_name = $loc->getValue('country_name') ;

echo "Team: <a href='?a=detailsTeam&amp;tourney_id=",$tid,"&amp;team_id=",$tm->getValue('team_id'),"'>";
echo $tm->getValue('name'),"</a><p></p>";

echo "<table border=1 cellpadding=4 cellspacing=0>\n";
echo "<tr bgcolor='#999999'>";
echo "<th>Name</th><th>Location</th><th>GP</th><th>F/G</th><th>Eff</th><th>Frags</th><th>Record with</th><th>+/-</th>";

// List player info
echo "<tr bgcolor='#CCCCCC'><td>";
echo "<a href='?a=detailsPlayer&amp;tourney_id=",$tid,"&amp;team_id=",$team_id,"&amp;player_id=",$player->getValue('player_id'),"'>";
$tlp = $tm->getTeamLeader($tid);

if ($tlp != null)
{
  if ($tlp->getValue('player_id') == $player->getValue('player_id'))
    {
      echo "<b>",$player->getValue('name'),"</b></a></td>\n";
    }
  else
    {
      echo $player->getValue('name'),"</a></td>\n";
    }
}
else
{
  echo $player->getValue('name'),"</a></td>\n";
}
$info = $player->getTourneyStats($tid);

echo "\t<td>",$loc_name,"</td>\n";
echo "<td>",$info['games_played'],"</td>";
echo "<td>",$info['frags_per_game'],"</td>";
echo "<td>",$info[util::EFFICIENCY],"</td>";
echo "<td>",$info['total_frags'],"</td>";
echo "<td>",$info['games_won'],"-",$info['games_lost'],"</td>";
echo "<td nowrap>",$info['frag_diff'],"</td>";
echo "</tr></table>";

echo "<br><b>Games Played</b><br>";
//echo "<table>" ;

foreach($player->getGamesPlayed($tid) as $g)
{
  $m = $g->getMatch() ;
  $teams = $m->getTeams() ;
  $t1 = $teams[0] ;
  $t2 = $teams[1] ;

  if ($tm->getValue('team_id')==$t1->getValue('team_id'))
    {
      $tm_score = $g->getValue('team1_score') ;

      $o_team = $t2 ;
      $o_score = $g->getValue('team2_score') ;
    }
  else
    {
      $tm_score = $g->getValue('team2_score') ;

      $o_team = $t1 ;
      $o_score = $g->getValue('team1_score') ;
    }

  $map = $g->getMap() ;
  $stats = $player->getGameStats($g->getValue('game_id')) ;

  $status = util::choose($tm_score>$o_score, ' in victory vs ', ' in loss vs ') ;

  echo "<a href='?a=detailsGame&amp;tourney_id=" . $t->getValue('tourney_id') . "&amp;match_id=" . $m->getValue('match_id'). "&amp;game_id=" . $g->getValue('game_id') . "'>" ;

  echo $stats['total_frags'] . ' frags ' . $status . $o_team->getValue('name') . ' on ' . $map->getValue('map_abbr') . ' (' . $tm_score . '-' . $o_score . ")</a><br>" ;
}

//echo '</table>' ;
?>
