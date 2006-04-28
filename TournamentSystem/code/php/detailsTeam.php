<?php

require_once 'includes.php';
include 'userLinks.php';
echo "<br>";

$tid = $_REQUEST['tourney_id'];
$team_id = $_REQUEST['team_id'];
$sort = $_REQUEST['sort'];
$t = new tourney(array('tourney_id'=>$tid));

try
{
  $tm = new team(array('team_id'=>$team_id));
}
catch (Exception $e)
{
  $tm = "";
}

if ($sort == "")
{
  $sort = util::SCORE_PER_GAME ;
}


// Gather team info
$team_id = $_REQUEST['team_id'];
$tm = new team(array('team_id'=>$team_id));
$name=$tm->getValue('name');
$name_abbr=$tm->getValue('name_abbr');
$email=$tm->getValue('email');
$irc_channel=$tm->getValue('irc_channel');
$location_id=$tm->getValue('location_id');
$loc = new location(array('location_id'=>$location_id));
$loc_name = $loc->getValue('country_name') ;
$password=$tm->getValue('password');
$approved=$tm->getValue('approved');

echo "<table border=1 cellpadding=4 cellspacing=0>\n";
echo "<tr bgcolor='#CCCCCC'><td><b>Team</b></td><td>",$name," (",$name_abbr,")</td></tr>\n";
echo "<tr bgcolor='#C0C0C0'><td><b>Email</b></td><td><a href='mailto:",$email,"'>",$email,"</a></td></tr>\n";
echo "<tr bgcolor='#CCCCCC'><td><b>IRC</b></td><td>",$irc_channel,"</td></tr>\n";
echo "</table><br>";

// List players in this team
echo "<table border=1 cellpadding=4 cellspacing=0>\n";
echo "<tr bgcolor='#999999'>";
echo "<th><a href='?a=detailsTeam&amp;tourney_id=$tid&amp;team_id=$team_id&amp;sort=name'>Name</a></th>";
echo "<th>Location</th>";
echo "<th><a href='?a=detailsTeam&amp;tourney_id=$tid&amp;team_id=$team_id&amp;sort=",util::GAMES_PLAYED,"'>GP</a></th>";
echo "<th><a href='?a=detailsTeam&amp;tourney_id=$tid&amp;team_id=$team_id&amp;sort=",util::TOTAL_SCORE,"'>Score</a></th>";
echo "<th><a href='?a=detailsTeam&amp;tourney_id=$tid&amp;team_id=$team_id&amp;sort=",util::SCORE_PER_GAME,"'>AS</a></th>";
echo "<th><a href='?a=detailsTeam&amp;tourney_id=$tid&amp;team_id=$team_id&amp;sort=",util::EFFICIENCY,"'>Eff</a></th>";
echo "<th><a href='?a=detailsTeam&amp;tourney_id=$tid&amp;team_id=$team_id&amp;sort=",util::GAMES_WON,"'>Record with</a></th>";
echo "<th><a href='?a=detailsTeam&amp;tourney_id=$tid&amp;team_id=$team_id&amp;sort=",util::SCORE_DIFF,"'>+/-</a></th>";
if ($sort == "name") 
{
  $sortOrder = SORT_ASC;
}
else 
{
  $sortOrder = SORT_DESC;
}

$count=0;
foreach ($tm->getSortedPlayerStats($tid, array($sort, $sortOrder, util::SCORE_PER_GAME, SORT_DESC, 'name', SORT_ASC)) as $player)
{
  if (++$count % 2 == 1) 
    {
      $clr = "#CCCCCC";
    }
  else
    {
      $clr = "#C0C0C0";
    }

  $loc = new location(array('location_id'=>$player['location_id']));
  $loc_name = $loc->getValue('country_name') ;
  echo "\t<tr bgcolor='$clr'>\n<td nowrap>";
  echo "<a href='?a=detailsPlayer&amp;tourney_id=",$tid,"&amp;team_id=",$team_id,"&amp;player_id=",$player['player_id'],"'>";
  $tlp = $tm->getTeamLeader($tid);

  if (!util::isNull($tlp))
    {
      if ($tlp->getValue('player_id') == $player['player_id']) {
	echo "<b>",$player['name'],"</b></a></td>\n";
      }
      else
	{
	  echo $player['name'],"</a></td>\n";
	}
    }
  else
    {
      echo $player['name'],"</a></td>\n";
    }

  //$info = $player->getTourneyStats($tid);
  echo "\t<td>",$loc_name,"</td>\n";
  echo "<td nowrap>",$player[util::GAMES_PLAYED],"</td>";
  echo "<td nowrap>",$player[util::TOTAL_SCORE],"</td>";
  echo "<td nowrap>",$player[util::SCORE_PER_GAME],"</td>";
  echo "<td nowrap>",$player[util::EFFICIENCY],"</td>";
  echo "<td nowrap>",$player[util::GAMES_WON],"-",$player[util::GAMES_LOST],"</td>";
  echo "<td nowrap>",$player[util::SCORE_DIFF],"</td>";
}

echo "</tr></table>";
echo "<p>Bold = team leader</b></p>";

echo "<b>Tourney Schedule</b><br>" ;
echo "<table border=1 cellpadding=2 cellspacing=0>\n";
echo "<tr bgcolor='#999999'><th>Week</th><th>Result</th><th>Match Date</th></tr>";
foreach ($tm->getMatches($tid) as $m)
{
  $wid = $m->getValue('winning_team_id');
  $cnt += 1;
  if ($cnt % 2 == 1) 
    {
      $clr = "#CCCCCC";
    }
  else
    {
      $clr = "#C0C0C0";
    }
  $t1 = new team(array('team_id'=>$m->getValue('team1_id')));
  $t2 = new team(array('team_id'=>$m->getValue('team2_id')));
  $ms = new match_schedule(array('schedule_id'=>$m->getValue('schedule_id')));
  echo "<tr bgcolor='$clr'>";
  echo "<td>",$ms->getValue('name'),"</td>";

  if ($m->getValue('approved'))
    {
      echo "<td><a href='?a=detailsMatch&amp;tourney_id=" . $tid. "&amp;match_id=" . $m->getValue('match_id') . "'>";
    }
  else
    {
      echo "<td>" ;
    }

  if ($wid == $m->getValue('team1_id') && $m->getValue('approved'))
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
  
  if ($wid == $m->getValue('team2_id') && $m->getValue('approved'))
    {
      echo "<b>";
      echo $t2->getValue('name');
      echo "</b>";
    }
  else 
    {
      echo $t2->getValue('name');
    }
  
  if ($m->getValue('approved'))
    {
      // Don't end row yet
      $team1 = 0;
      $team2 = 0;
      foreach ($m->getGames() as $g)
	{	
	  if ($g->getValue('team1_score') > $g->getValue('team2_score'))
	    {
	      $team1 += 1;				
	    }
	  elseif ($g->getValue('team1_score') < $g->getValue('team2_score'))
	    {
	      $team2 += 1;				
	    }
	}

      echo " (",$team1,"-",$team2,")</a>";
    }
  
  echo "</td>";
  echo "<td>",$m->getValue('match_date'),"</td></tr>\n";
}

echo "</table>" ;
echo "<br>" ;
?>
