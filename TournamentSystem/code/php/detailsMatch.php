<?php

require_once 'includes.php';

try
{
  session_start() ;

  if (!util::isNull($_SESSION['user_id']))
    {
      $p = new player(array('player_id'=>$_SESSION['user_id'])) ;
      require_once 'login.php';
    }
}
catch(Exception $e) {}

require 'userLinks.php';
echo "<br>";

$tid = $_REQUEST['tourney_id'];
$t = new tourney(array('tourney_id'=>$tid));

$match_id = $_REQUEST['match_id'];
$m = new match(array('match_id'=>$match_id));

if (!$m->getValue('approved') && (util::isNull($p) || (!$p->isSuperAdmin() && !$p->isTourneyAdmin($t->getValue('tourney_id')))))
{
  echo "Match has not been approved by an admin yet.";
  util::throwException('Match has not been approved yet') ;
}

echo "<table border=1 cellpadding=2 cellspacing=0>\n";
echo "<tr><th>Week</th><th>Result</th><th>Match Date</th></tr>";
$wid = $m->getValue('winning_team_id');

$t1 = new team(array('team_id'=>$m->getValue('team1_id')));
$t2 = new team(array('team_id'=>$m->getValue('team2_id')));
$ms = new match_schedule(array('schedule_id'=>$m->getValue('schedule_id')));

echo "<tr>";
echo "<td>",$ms->getValue('name'),"</td>";

echo "<td>";
if ($wid == $m->getValue('team1_id'))
{
  echo "<b>";
  echo $t1->getValue('name');
  echo "</b>";
}
else 
{
  echo $t1->getValue('name');
}

echo "&nbsp;vs&nbsp;";

if ($wid == $m->getValue('team2_id'))
{
  echo "<b>";
  echo $t2->getValue('name');
  echo "</b>";
}
else 
{
  echo $t2->getValue('name');
}
// Don't end row yet
$team1 = 0;
$team2 = 0;
$gameout = "";
foreach ($m->getGames() as $g)
{	
  $gameout .= "<table border=1 cellpadding=2 cellspacing=0 align=center width=100%>";
  if ($g->getValue('team1_score') > $g->getValue('team2_score'))
    {
      $team1 += 1;				
    }
  elseif ($g->getValue('team1_score') < $g->getValue('team2_score'))
    {
      $team2 += 1;				
    }	
  
  $files = $g->getFiles() ;

  $t1out = "<a href='?a=detailsTeam&amp;tourney_id=".$tid."&amp;team_id=".$t1->getValue('team_id')."'>".$t1->getValue('name')."</a>";
  $t2out = "<a href='?a=detailsTeam&amp;tourney_id=".$tid."&amp;team_id=".$t2->getValue('team_id')."'>".$t2->getValue('name')."</a>";
  $map = new map(array('map_id'=>$g->getValue('map_id')));

  $gameout .= "<tr><td align=center><b>".$map->getValue('map_name')."&nbsp;(".$map->getValue('map_abbr').")</b><br>";
  $gameout .= "<table border=0 cellspacing=4 cellpadding=0 width=75%>";
  $gameout .= "<tr><td align=center width=50%>".$t1out."</td>";
  $gameout .= "<td align=center>".$t2out."</td></tr>";
  $gameout .= "<tr><td align=center width=50%>".$g->getValue('team1_score')."</td>";
  $gameout .= "<td align=center>".$g->getValue('team2_score')."</td></tr>";
  $gameout .= "</table>";
	
  $file = null ;
  if (array_key_exists(util::SCREENSHOT, $files))
    {
      $file = $files[util::SCREENSHOT]->getValue('url') ;
      $gameout .= "<a href='$file'><img src='" . $file . "' width=400 height=300 alt=''></a><p>";
    }
  else
    {
      $gameout .= 'No Screenshot Available<p>' ;
    }
  
  if (array_key_exists(util::TEAM_SCORE_GRAPH_SMALL, $files))
    {
      $file = $files[util::TEAM_SCORE_GRAPH_SMALL]->getValue('url') ;
      $gameout .= "<img src='" . $file . "'><p>";
    }
  else
    {
      $gameout .= 'No Graph Available<p>' ;
    }

  if ($g->hasDetails())
    {
      $gameout .= "<a href='?a=detailsGame&amp;tourney_id=" . $t->getValue('tourney_id') . "&amp;match_id=" . $m->getValue('match_id'). "&amp;game_id=" . $g->getValue('game_id') . "'>Game Details</a><p>";
    }
  else
    {
      $gameout .= 'No Game Details Available<p>' ;
    }
  
  if (array_key_exists(util::MVD_DEMO, $files))
    {
      $file = $files[util::MVD_DEMO]->getValue('url') ;
      $gameout .= "<a href='". $file ."'>Demo</a><p>";
    }
  else
    {
      $gameout .= 'No demo available.' ;
    }

  $gameout .= "</td></tr></table>\n";
}

echo " (",$team1,"-",$team2,")";
echo "</td>";
echo "<td>",$m->getValue('match_date'),"</td></tr>\n";
echo "</table><br>\n";
echo $gameout;

echo "<br>" ;		

include 'listComments.php' ;
?>