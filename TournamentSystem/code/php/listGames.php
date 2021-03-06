<?php
require 'includes.php';
require_once 'login.php' ;

try
{
  $tid = $_REQUEST['tourney_id'];
  $t = new tourney(array('tourney_id'=>$tid));

  $division_id = $_REQUEST['division_id'];
  $div = new division(array('division_id'=>$division_id));

  $match_id = $_REQUEST['match_id'];
  $m = new match(array('match_id'=>$match_id));

  $t1 = new team(array('team_id'=>$m->getValue('team1_id')));
  $t2 = new team(array('team_id'=>$m->getValue('team2_id')));
  try
  {
    $p = new player(array('player_id'=>$_SESSION['user_id'])) ;
  }
  catch(Exception $e) {}
  try
  {
    $tm = new team(array('team_id'=>$_SESSION['team_id'])) ;
  }
  catch(Exception $e) {}
  if (util::isNull($tm) && !$p->isSuperAdmin() && !$p->isTourneyAdmin($t->getValue('tourney_id')))
    {
      util::throwException('not authorized') ;
    }
  echo "<br>";
  echo "<br><b>Current Games:</b><br>";
  echo "<table border=1 cellpadding=2 cellspacing=0>\n";
  echo "<th>Map</th><th>",$t1->getValue('name_abbr')," Score</th><th>",$t2->getValue('name_abbr')," Score</th>";
  echo "<th>Edit</th><th>Delete</th>";
  if (!util::isLoggedInAsTeam()) {
  	echo "<th>Recompute</th>";
  }
  echo "<th>Game Details</th>";

  foreach ($m->getGames() as $g)
    {
      $map = new map(array('map_id'=>$g->getValue('map_id')));
      echo "\t<tr>\n";
      echo "\t<td>",$map->getValue('map_abbr'),"</td>\n";
      echo "\t<td>",$g->getValue('team1_score'),"</td>\n";
      echo "\t<td>",$g->getValue('team2_score'),"</td>\n";
      echo "<td><a href='?a=manageGame&amp;tourney_id=$tid&amp;mode=edit&amp;division_id=$division_id&amp;match_id=$match_id&amp;game_id=",$g->getValue('game_id'),"'>";
      echo "Edit</a></td>";
      echo "<td><a href='?a=saveGame&amp;tourney_id=$tid&amp;mode=delete&amp;division_id=$division_id&amp;match_id=$match_id&amp;game_id=",$g->getValue('game_id'),"'>";
      echo "Delete</a></td>";
      if (!util::isLoggedInAsTeam()) {
      	echo "<td><a href='?a=recomputeGame&amp;tourney_id=$tid&amp;division_id=$division_id&amp;match_id=$match_id&amp;game_id=",$g->getValue('game_id'),"'>";
        echo "Recompute</a></td>";
      }
      echo "<td><a href='?a=detailsGame&amp;tourney_id=" . $t->getValue('tourney_id') . "&amp;division_id=$division_id&amp;match_id=" . $m->getValue('match_id'). "&amp;game_id=" . $g->getValue('game_id') . "'>Details</a><p></td>";
      echo "\t</tr>\n";
    }
  echo "</table>\n";
  echo "<p><a href='?a=manageGame&amp;tourney_id=$tid&amp;division_id=$division_id&amp;match_id=$match_id'>Add a Game</a>";
}
catch (Exception $e) {}
?>
