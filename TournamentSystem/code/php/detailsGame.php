
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

$gameout .= "<b>Team Scores:</b> (number is current lead)";
$gameout .= "<img src='" . $file . "'>";
$gameout .= "<br><br>";

if (array_key_exists(util::PLAYER_SCORE_GRAPH, $files))
{	
  $file = $files[util::PLAYER_SCORE_GRAPH]->getValue('url') ; 
}
$gameout .= "<b>Player Scores:</b>";
$gameout .= "<img src='" . $file . "'>";
$gameout .= "<br><br><br>";


$gameout .= "<b>Player frags by weapon:</b>";
$gameout .= "<table border=0 cellpadding=0 cellspacing=8>";
$gameout .= "<tr>";
$teams = $m->getTeams() ;
foreach($teams as $t)
{
	$gameout .= "<td>";
  $players = $g->getTeamPlayers($t->getValue('team_id')) ;

  $gameout .= "<b><a href='?a=detailsTeam&amp;tourney_id=" . $tid . "&amp;team_id=" . $t->getValue('team_id') . "'>" . $t->getValue('name') . "</a></b><p>";

  foreach($players as $p)
    {
      $gameout .= "<a href='?a=detailsPlayer&amp;tourney_id=" . $tid . "&amp;team_id=" . $t->getValue('team_id') . "&amp;player_id=" . $p->getValue('player_id') . "'>" ;
      $gameout .= $p->getValue('name'). "</a><br>";

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
    $gameout .= "</td>";
}
$gameout .= "</tr></table>";

echo $gameout ;

echo "<br><b>Extra Stats:</b>";
//echo "<table border=0 cellpadding=0 cellspacing=0 align=center><tr><td>";
//$tbl = 0;
$rank = 0;
$first = true;
$currentStat = "";
foreach ($g->getStats(array('stat_name', SORT_ASC, 'value', SORT_DESC)) as $s) 
{	
  $rank++;
	$tm = new team(array('team_id'=>$s->getValue('team_id')));
	$p = new player(array('player_id'=>$s->getValue('player_id')));
	if ($first == true)
	{
		echo "<table border=1 cellpadding=2 cellspacing=0>";
		echo "<tr bgcolor='#999999'><th>Team</th><th>Player</th><th>",$s->getValue('stat_name'),"</th></tr>";
		$first = false;
	}
	else
	{
		if ($currentStat != $s->getValue('stat_name')) 
		{
			echo "</table><br>";
			//$tbl++;
			//if ($tbl == 2)
			//{
				//echo "</td></tr><tr><td>";
				//$tbl = 0;
			//}
			//else
			//{
				//echo "</td><td>";
			//}
			echo "<table border=1 cellpadding=2 cellspacing=0>";
			echo "<tr bgcolor='#999999'><th>Team</th><th>Player</th><th>",$s->getValue('stat_name'),"</th></tr>";
		}
	}
	$currentStat = $s->getValue('stat_name');

	if ($rank % 2 == 1) 
	{
		$clr = "#CCCCCC";
	}
	else
	{
		$clr = "#C0C0C0";
	}
	echo "<tr bgcolor='$clr'>";
	echo "<td>",$tm->getValue('name_abbr'),"</td>";
	echo "<td>",$p->getValue('name'),"</td>";
	echo "<td>",$s->getValue('value'),"</td>";
	echo "</tr>";			
}
echo "</table>";
//echo "</table>";
echo "<p>";
echo "<a href='?a=detailsMatch&amp;tourney_id=" . $tid . "&amp;match_id=" . $m->getValue('match_id'). "'>Match Details</a><p>";



?>