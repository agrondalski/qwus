
<?php

require 'includes.php';
require 'userLinks.php';
echo "<br>";

$tid = $_REQUEST['tourney_id'];
$t = new tourney(array('tourney_id'=>$tid));

$match_id = $_REQUEST['match_id'];
$m = new match(array('match_id'=>$match_id));

if (!$m->getValue('approved'))
{
  util::throwException('Match has not been approved yet') ;
}

$game_id = $_REQUEST['game_id'];
$g = new game(array('game_id'=>$game_id));

$map = $g->getMap() ;
$files = $g->getFiles() ;

$gameout .= "<a href='?a=detailsMatch&amp;tourney_id=" . $t->getValue('tourney_id') . "&amp;match_id=" . $m->getValue('match_id'). "'>Match Details</a><p>";

if (array_key_exists(util::TEAM_SCORE_GRAPH_LARGE, $files))
{
  $file = $files[util::TEAM_SCORE_GRAPH_LARGE]->getValue('url') ;
}
$gameout .= "<img src='" . $file . "'>";

if (array_key_exists(util::PLAYER_SCORE_GRAPH, $files))
{
  $file = $files[util::PLAYER_SCORE_GRAPH]->getValue('url') ;
}
$gameout .= "<img src='" . $file . "'>";

$teams = $m->getTeams() ;

foreach($teams as $t)
{
  $players = $g->getTeamPlayers($t->getValue('team_id')) ;

  $gameout .= "<br><h2><a href='?a=detailsTeam&amp;tourney_id=" . $tid . "&amp;team_id=" . $t->getValue('team_id') . "'>" . $t->getValue('name') . "</a></h2>";

  foreach($players as $p)
    {
      $gameout .= "<h3><a href='?a=detailsPlayer&amp;tourney_id=" . $tid . "&amp;team_id=" . $t->getValue('team_id') . "&amp;player_id=" . $p->getValue('player_id') . "'>" ;
      $gameout .= $p->getValue('name'). "</a></h3>";

      $piechart = $p->getPieChartIdx($game_id) ;

      $file = null ;
      if (array_key_exists($piechart, $files))
	{
	  $file = $files[$piechart]->getValue('url') ;
	}

      if (!util::isNull($file))
	{
	  $gameout .= "<img src='" . $file . "'><p>";      
	}
      else
	{
	  $gameout .= "No Chart Available<p>" ;
	}
    }
}

echo $gameout ;
?>