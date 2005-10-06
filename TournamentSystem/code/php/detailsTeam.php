<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];
$team_id = $_REQUEST['team_id'];
$sort = $_REQUEST['sort'];
$t = new tourney(array('tourney_id'=>$tid));
try {
	$tm = new team(array('team_id'=>$team_id));
} catch (Exception $e) {
    $tm = "";
}

if ($sort == "")
{
	$sort = 'frags_per_game';
}

include 'userLinks.php';
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
$loc_name = $loc->getValue('country_name') ;
$password=$tm->getValue('password');
$approved=$tm->getValue('approved');

echo "<table border=1 cellpadding=4 cellspacing=0>\n";
echo "<tr><td><b>Team</b></td><td>",$name," (",$name_abbr,")</td></tr>\n";
echo "<tr><td><b>Email</b></td><td><a href='mailto:",$email,"'>",$email,"</a></td></tr>\n";
echo "<tr><td><b>IRC</b></td><td>",$irc_channel,"</td></tr>\n";
echo "</table><br>";
// List players in this team
echo "<table border=1 cellpadding=4 cellspacing=0>\n";
echo "<th><a href='?a=detailsTeam&amp;tourney_id=$tid&amp;team_id=$team_id&amp;sort=name'>Name</a></th>";
echo "<th>Location</th>";
echo "<th><a href='?a=detailsTeam&amp;tourney_id=$tid&amp;team_id=$team_id&amp;sort=games_played'>GP</a></th>";
echo "<th><a href='?a=detailsTeam&amp;tourney_id=$tid&amp;team_id=$team_id&amp;sort=frags_per_game'>F/G</a></th>";
echo "<th><a href='?a=detailsTeam&amp;tourney_id=$tid&amp;team_id=$team_id&amp;sort=total_frags'>Frags</a></th>";
echo "<th><a href='?a=detailsTeam&amp;tourney_id=$tid&amp;team_id=$team_id&amp;sort=matches_won'>Record with</a></th>";
echo "<th><a href='?a=detailsTeam&amp;tourney_id=$tid&amp;team_id=$team_id&amp;sort=frag_diff'>+/-</a></th>";
if ($sort == "name") 
{
	$sortOrder = SORT_ASC;
}
else 
{
	$sortOrder = SORT_DESC;
}

//foreach ($tm->getPlayers($tid) as $player) 
foreach ($tm->getSortedPlayerStats($tid, array($sort, $sortOrder, 'frags_per_game', SORT_DESC, 'name', SORT_ASC)) as $player)
{
  $loc = new location(array('location_id'=>$player['location_id']));
  $loc_name = $loc->getValue('country_name') ;
  echo "\t<tr>\n<td nowrap>";
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
  echo "<td nowrap>",$player['games_played'],"</td>";
  echo "<td nowrap>",$player['frags_per_game'],"</td>";
  echo "<td nowrap>",$player['total_frags'],"</td>";
  echo "<td nowrap>",$player['matches_won'],"-",$player['matches_lost'],"</td>";
  echo "<td nowrap>",$player['frag_diff'],"</td>";
}

echo "</tr></table>";
echo "<p>Bold = team leader</b></p>";

echo "<b>Recent Matches</b>" ;
echo "<table>" ;
foreach ($tm->getRecentMatches($tid, array('limit'=>5)) as $m)
{
  $wid = $m->getValue('winning_team_id');

  $t1 = new team(array('team_id'=>$m->getValue('team1_id')));
  $t2 = new team(array('team_id'=>$m->getValue('team2_id')));

  echo "<td><a href='?a=detailsMatch&amp;tourney_id=" . $tid. "&amp;match_id=" . $m->getValue('match_id') . "'>";

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

  echo "</td><tr>";
}
echo "</table>" ;

?>
