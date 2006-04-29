<?php

require 'includes.php';

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

try
{
  session_start() ;

  if (!util::isNull($_SESSION['user_id']))
    {
      $p = new player(array('player_id'=>$_SESSION['user_id'])) ;
    }
}
catch(Exception $e) {}

if (!$m->getValue('approved') && (util::isNull($p) || (!$p->isSuperAdmin() && !$p->isTourneyAdmin($t->getValue('tourney_id')))))
{
  echo "Match has not been approved by an admin yet - Game not viewable.";
  util::throwException('Game has not been approved yet') ;
}

$game_id = $_REQUEST['game_id'];
$g = new game(array('game_id'=>$game_id));

$map = $g->getMap() ;
$files = $g->getFiles() ;

$gameout .= "<a href='?a=detailsMatch&amp;tourney_id=" . $t->getValue('tourney_id') . "&amp;match_id=" . $m->getValue('match_id'). "'>Match Details</a><p>";

if (!$g->hasDetails())
{
  echo $gameout ;
  echo 'No details available<p>' ;
  util::throwException('No Details') ;
}

$showping = false;
// Get Ping/Score/Player table from stats
$teams = $m->getTeams();
foreach($teams as $t)
{  
  $currentStat = null;
  $tstats = $g->getStatsByTeam(array('stat_name', SORT_ASC, 'value', SORT_DESC), $t->getValue('team_id')) ;
  $players = $g->getTeamPlayers($t->getValue('team_id')) ;

  foreach ($tstats as $s) 
  {	
      $p = $players[$s->getValue('player_id')] ;
      $currentStat = $s->getValue('stat_name');

			if ($currentStat == "Ping") 
			{
				$showping = true;
				$ping[$p->getValue('name')] = $s->getValue('value');
			}
			if ($currentStat == "Score") 
			{
				$score[$p->getValue('name')] = $s->getValue('value');
			}	  
    }    
}

// Grab totals / averages by cycling through it again by team, then player
$totalgameplayers = 0;
$rank = 0;
$team = 0;
foreach($teams as $t)
{
	$team++;
	$playercount = 0;
	$pingtotal   = 0;
	$pinglow     = 999;
	$pingavg     = 0;
	$pinghigh    = 0;
	$scoretotal  = 0;
	$tstats = $g->getStatsByTeam(array('stat_name', SORT_ASC, 'value', SORT_DESC), $t->getValue('team_id')) ;
  $players = $g->getTeamPlayers($t->getValue('team_id')) ;
  $count = 0;
	foreach ($players as $p) 
  {	
  	$count++;
  	//$clr = "#CCCCCC";
		//$gameout .= "<tr>";		
		//$gameout .= "<td bgcolor='$clr'>".$ping[$p->getValue('name')]."</td><td bgcolor='$clr'>".$score[$p->getValue('name')]."</td><td bgcolor='$clr'>".$p->getValue('name')."</td><td bgcolor='$clr'>".$t->getValue('name')."</td>";		
		//$gameout .= "</tr>";
		if ($team == 1)
		{
			$t1pings[$count]  = $ping[$p->getValue('name')];
			$t1scores[$count] = $score[$p->getValue('name')];
			$t1names[$count]  = $p->getValue('name');
			$t1pid[$count]     = $p->getValue('player_id');
			$team1            = $t->getValue('name');
		} else 
		{
			$t2pings[$count]  = $ping[$p->getValue('name')];
			$t2scores[$count] = $score[$p->getValue('name')];
			$t2names[$count]  = $p->getValue('name');
			$t2pid[$count]     = $p->getValue('player_id');
			$team2            = $t->getValue('name');
		}
		// ping checks / averages / etc
		if ($ping[$p->getValue('name')] < $pinglow)
		{
			$pinglow = $ping[$p->getValue('name')];
		}
		if ($ping[$p->getValue('name')] > $pinghigh)
		{
			$pinghigh = $ping[$p->getValue('name')];
		}
		$pingtotal   += $ping[$p->getValue('name')];
		$scoretotal  += $score[$p->getValue('name')];
		$playercount += 1;
	}
	$pingavg = ($pingtotal) / ($playercount);
	$clr = "#C0C0C0";
	// Save the totals / average for each team
	if ($team == 1) 
	{
		$totalgameplayers += $playercount;
		$t1players = $playercount;
		$t1plow    = $pinglow;
		$t1ping    = $pingavg;
		$t1phigh   = $pinghigh;
		$t1score   = $scoretotal;
		$t1name    = $t->getValue('name_abbr');
		$t1id      = $t->getValue('team_id');
	} else 
	{
		$totalgameplayers += $playercount;
	  $t2players = $playercount;
		$t2plow    = $pinglow;
		$t2ping    = $pingavg;
		$t2phigh   = $pinghigh;
		$t2score   = $scoretotal;
		$t2name    = $t->getValue('name_abbr');
		$t2id      = $t->getValue('team_id');
	}	
}

///**********************************************
//  Group the Pings / Scores with the Team Stats
///**********************************************

$gameout .=  "<table border=0 cellpadding=0 cellspacing=3>";
$gameout .=  "<tr>";
$gameout .=  "<td>";

// Display team pings/scores
$clr = "#999999";
$gameout .= "<table cellpadding=3 cellspacing=0 border=1><tr>";
if ($showping == true)
{
	$gameout .= "<th bgcolor='$clr'>low&nbsp;/&nbsp;avg&nbsp;/&nbsp;high</th>";
}
$gameout .= "<th bgcolor='$clr'>team</th><th bgcolor='$clr'>total</th><th bgcolor='$clr'>players</th></tr>";
if ($t1score >= $t2score) 
{
	$clr = "#808080";
	$gameout .= "<tr>";
	if ($showping == true)
	{
		$gameout .= "<td bgcolor='$clr'>".$t1plow."&nbsp;/&nbsp;".$t1ping."&nbsp;/&nbsp;".$t1phigh."</td>";
	}
	$gameout .= "<td bgcolor='$clr'><b><a href='?a=detailsTeam&amp;tourney_id=".$tid."&amp;team_id=".$t1id."'>".$team1."</a></b></td><td bgcolor='$clr'>".$t1score."</td><td bgcolor='$clr'>".$t1players."</td>";		
	$gameout .= "</tr>";
	$clr = "#CCCCCC";
	$gameout .= "<tr>";
	if ($showping == true)
	{
		$gameout .= "<td bgcolor='$clr'>".$t2plow."&nbsp;/&nbsp;".$t2ping."&nbsp;/&nbsp;".$t2phigh."</td>";
	}
	$gameout .= "<td bgcolor='$clr'><b><a href='?a=detailsTeam&amp;tourney_id=".$tid."&amp;team_id=".$t2id."'>".$team2."</a></b></td><td bgcolor='$clr'>".$t2score."</td><td bgcolor='$clr'>".$t2players."</td>";		
	$gameout .= "</tr>";
} else 
{	
	$clr = "#808080";
	$gameout .= "<tr>";
	if ($showping == true)
	{
		$gameout .= "<td bgcolor='$clr'>".$t2plow."&nbsp;/&nbsp;".$t2ping."&nbsp;/&nbsp;".$t2phigh."</td>";
	}
	$gameout .= "<td bgcolor='$clr'><b><a href='?a=detailsTeam&amp;tourney_id=".$tid."&amp;team_id=".$t2id."'>".$team2."</a></b></td><td bgcolor='$clr'>".$t2score."</td><td bgcolor='$clr'>".$t2players."</td>";		
	$gameout .= "</tr>";
	$clr = "#CCCCCC";
	$gameout .= "<tr>";
	if ($showping == true)
	{
		$gameout .= "<td bgcolor='$clr'>".$t1plow."&nbsp;/&nbsp;".$t1ping."&nbsp;/&nbsp;".$t1phigh."</td>";
	}
	$gameout .= "<td bgcolor='$clr'><b><a href='?a=detailsTeam&amp;tourney_id=".$tid."&amp;team_id=".$t1id."'>".$team1."</a></b></td><td bgcolor='$clr'>".$t1score."</td><td bgcolor='$clr'>".$t1players."</td>";		
	$gameout .= "</tr>";
}
$gameout .= "</table><p>";
// END TABLE 1 (Totals)


//Display player pings/scores
$clr = "#999999";
$gameout .= "<table cellpadding=3 cellspacing=0 border=1><tr>";
if ($showping == true)
{
	$gameout .= "<th bgcolor='$clr'>Ping</th>";
}
$gameout .= "<th bgcolor='$clr'>Score</th><th bgcolor='$clr'>Player</th><th bgcolor='$clr'>Team</th></tr>";
if ($t1score >= $t2score) 
{
	$count = 0;
	foreach($t1names as $name) {
		$count++;
		$clr = "#808080";
		$gameout .= "<tr>";
		if ($showping == true)
		{
			$gameout .= "<td bgcolor='$clr'>".$t1pings[$count]."</td>";
		}
		$gameout .= "<td bgcolor='$clr'>".$t1scores[$count]."</td>";
		$gameout .= "<td bgcolor='$clr'><a href='?a=detailsPlayer&amp;tourney_id=" . $tid . "&amp;team_id=".$t1id."&amp;player_id=".$t1pid[$count]."'>".$t1names[$count]."</a></td><td bgcolor='$clr'>".$t1name."</td>";		
		$gameout .= "</tr>";
	}
	$count = 0;
	foreach($t2names as $name) {
		$count++;
		$clr = "#CCCCCC";
		$gameout .= "<tr>";
		if ($showping == true)
		{
			$gameout .= "<td bgcolor='$clr'>".$t2pings[$count]."</td>";
		}
		$gameout .= "<td bgcolor='$clr'>".$t2scores[$count]."</td>";
		$gameout .= "<td bgcolor='$clr'><a href='?a=detailsPlayer&amp;tourney_id=" . $tid . "&amp;team_id=".$t2id."&amp;player_id=".$t2pid[$count]."'>".$t2names[$count]."</a></td><td bgcolor='$clr'>".$t2name."</td>";		
		$gameout .= "</tr>";
	}
} else 
{	
	$count = 0;
	foreach($t2names as $name) {
		$count++;
		$clr = "#808080";
		$gameout .= "<tr>";
		if ($showping == true)
		{
			$gameout .= "<td bgcolor='$clr'>".$t2pings[$count]."</td>";
		}
		$gameout .= "<td bgcolor='$clr'>".$t2scores[$count]."</td>";
		$gameout .= "<td bgcolor='$clr'><a href='?a=detailsPlayer&amp;tourney_id=" . $tid . "&amp;team_id=".$t2id."&amp;player_id=".$t2pid[$count]."'>".$t2names[$count]."</a></td><td bgcolor='$clr'>".$t2name."</td>";		
		$gameout .= "</tr>";
	}
	$count = 0;
	foreach($t1names as $name) {	
		$count++;
		$clr = "#CCCCCC";
		$gameout .= "<tr>";
		if ($showping == true)
		{
			$gameout .= "<td bgcolor='$clr'>".$t1pings[$count]."</td>";
		}
		$gameout .= "<td bgcolor='$clr'>".$t1scores[$count]."</td>";
		$gameout .= "<td bgcolor='$clr'><a href='?a=detailsPlayer&amp;tourney_id=" . $tid . "&amp;team_id=".$t1id."&amp;player_id=".$t1pid[$count]."'>".$t1names[$count]."</a></td><td bgcolor='$clr'>".$t1name."</td>";		
		$gameout .= "</tr>";
	}
}
$gameout .= "</table><p>";
$gameout .=  "</td>";
$gameout .=  "<td>&nbsp;</td>";


// TEAM STATS
$clr='#CCCCCC';
$cl2='#C0C0C0';
$stats_team = $g->getTeamStats();

$showpowerups = true;
if ($totalgameplayers == 2)
{
	$showpowerups = false;
}
foreach ($stats_team as $tm) 
{
	$gameout .= "<td>" ;
	// Each 'Team Stats' is its own Cell, and also has its own table	  
  $gameout .= "<table border=1 cellpadding=3 cellspacing=0>";
  $gameout .= "<tr bgcolor='#999999'><th colspan=4><b><a href='?a=detailsTeam&amp;tourney_id=" . $tid . "&amp;team_id=" . $tm['team_id']. "'>" . $tm['name']."</a></b></th></tr>";
  $gameout .= "<tr bgcolor='$clr'><th colspan=4><b>Weapons</b></th></tr>";
  $gameout .= "<tr bgcolor='$clr'><th>LG%</th><th>RL D</th><th>SSG%</th><th>SG%</th></tr>";
  $gameout .= "<tr bgcolor='$cl2'>";
  $gameout .= "<td>".$tm[util::LG_ACCURACY]."</td>";
	$gameout .= "<td>".$tm[util::DIRECT_ROCKETS]."</td>";
	$gameout .= "<td>".$tm[util::SSG_ACCURACY]."</td>";
	$gameout .= "<td>".$tm[util::SG_ACCURACY]."</td>";
	$gameout .= "</tr>";
	if ($showpowerups == true)
	{
		$gameout .= "<tr bgcolor='$clr'><th colspan=4><b>Powerups</b></th></tr>";
		$gameout .= "<tr bgcolor='$clr'><th>Quads</th><th>Pents</th><th>Rings</th><th>&nbsp;</th></tr>";
		$gameout .= "<tr bgcolor='$cl2'>";
		$gameout .= "<td>".$tm[util::QUADS]."</td>"; 
		$gameout .= "<td>".$tm[util::PENTS]."</td>"; 
		$gameout .= "<td>".$tm[util::RINGS]."</td>"; 	
		$gameout .= "<td>&nbsp;</td>"; 	
		$gameout .= "</tr>";
	}
	$gameout .= "<tr bgcolor='$clr'><th colspan=4><b>Armors</b></th></tr>";
	$gameout .= "<tr bgcolor='$clr'><th>RA</th><th>YA</th><th>GA</th><th>&nbsp;</th></tr>";
  $gameout .= "<tr bgcolor='$cl2'>";
	$gameout .= "<td>".$tm[util::RED_ARMORS]."</td>";
	$gameout .= "<td>".$tm[util::YELLOW_ARMORS]."</td>"; 
	$gameout .= "<td>".$tm[util::GREEN_ARMORS]."</td>";
	$gameout .= "<td>&nbsp;</td>"; 		
	$gameout .= "</tr>";
	$gameout .= "<tr bgcolor='$clr'><th colspan=4><b>Damage &amp; Misc.</b></th></tr>";
	$gameout .= "<tr bgcolor='$clr'><th>DMG<br>Given</th><th>DMG<br>Taken</th><th>Mins<br>Played</th><th>With<br>Lead</th></tr>";
  $gameout .= "<tr bgcolor='$cl2'>";
	$gameout .= "<td>".$tm[util::DAMAGE_GIVEN]."</td>";
	$gameout .= "<td>".$tm[util::DAMAGE_TAKEN]."</td>";
	$gameout .= "<td>".$tm[util::MINUTESPLAYED]."</td>"; 
	$gameout .= "<td>".$tm[util::MINUTESWITHLEAD]."</td>"; 
	$gameout .= "</tr>";

	$gameout .= "</table>";
  $gameout .= "</td><td>&nbsp;</td>" ;
}
// END TEAM STATS
$gameout .=  "</tr>";
$gameout .=  "</table><p>";



// On to the graphs

if (array_key_exists(util::TEAM_SCORE_GRAPH_LARGE, $files))
{
  $file = $files[util::TEAM_SCORE_GRAPH_LARGE]->getValue('url') ;
  $gameout .= "<b>Team Scores:</b> (number is current lead)";
  $gameout .= "<img src='" . $file . "' alt=''>";
  $gameout .= "<br><br>";
}

if (array_key_exists(util::PLAYER_SCORE_GRAPH, $files))
{	
  $file = $files[util::PLAYER_SCORE_GRAPH]->getValue('url') ; 
  $gameout .= "<b>Player Scores:</b>";
  $gameout .= "<img src='" . $file . "' alt=''>";
  $gameout .= "<br><br><br>";
}

$gameout .= "<b>Player frags by weapon:</b>";
$gameout .= "<table border=0 cellpadding=0 cellspacing=8>";
$gameout .= "<tr>";
$teams = $m->getTeams() ;
foreach($teams as $t)
{
  $gameout .= "<td>";
  $players = $g->getTeamPlayers($t->getValue('team_id')) ;

  $gameout .= "<b><a href='?a=detailsTeam&amp;tourney_id=" . $tid . "&amp;team_id=" . $t->getValue('team_id') . "'>" . $t->getValue('name') . "</a></b><p>";

  foreach($players as $k=>$p)
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
	  $gameout .= "<img src='" . $file . "' alt=''><p>";
	}
      else
	{
	  $gameout .= "No Chart Available<p>" ;
	}
    }
    $gameout .= "</td><td>&nbsp;</td>";
}
$gameout .= "</tr></table>";

echo $gameout ;

echo "<br><b>Extra Stats:</b>";
echo "<table border=0 cellpadding=0 cellspacing=8>";
echo "<tr>";

foreach($teams as $t)
{
  $rank = 0;
  $currentStat = null;

  echo "<td>" ;
  // Each Team is its own Cell, and also has its own table
  
  echo "<table border=1 cellpadding=3 cellspacing=0>";

  $clr = ++$rank%2==1 ? $clr="#CCCCCC" : $clr="#C0C0C0" ;
  echo "<tr bgcolor='$clr'><th colspan=2><b><a href='?a=detailsTeam&amp;tourney_id=" . $tid . "&amp;team_id=" . $t->getValue('team_id') . "'>" . $t->getValue('name') . "</a></b></th></tr>";
  echo "<tr>" ;

  $tstats = $g->getStatsByTeam(array('stat_name', SORT_ASC, 'value', SORT_DESC), $t->getValue('team_id')) ;

  $players = $g->getTeamPlayers($t->getValue('team_id')) ;

  foreach ($tstats as $s) 
    {	
      $p = $players[$s->getValue('player_id')] ;
     
      if ($currentStat != $s->getValue('stat_name')) 
	{    
	  $currentStat = $s->getValue('stat_name');

	  $clr = ++$rank%2==1 ? $clr="#CCCCCC" : $clr="#C0C0C0" ;
	  echo "<tr bgcolor='$clr'><td colspan=3 align=center><b>" . $s->getValue('stat_name') . "</b></td></tr>" ;
	}
	  
      $clr = ++$rank%2==1 ? $clr="#CCCCCC" : $clr="#C0C0C0" ;
      echo "<tr bgcolor='$clr'>";
      echo "<td>",$p->getValue('name'),"</td>";
      echo "<td>",$s->getValue('value'),"</td>";
      echo "</tr>";	
    }

  echo "</table>";
  echo "</td><td>&nbsp;</td>" ;
}



// TEAM STATS
$clr='#CCCCCC';
$cl2='#C0C0C0';
$stats_team = $g->getTeamStats();

$showpowerups = true;
if ($totalgameplayers == 2)
{
	$showpowerups = false;
}
foreach ($stats_team as $tm) 
{
	echo "<td>" ;
	// Each 'Team Stats' is its own Cell, and also has its own table	  
  echo "<table border=1 cellpadding=3 cellspacing=0>";
  echo "<tr bgcolor='$clr'><th colspan=4><b><a href='?a=detailsTeam&amp;tourney_id=" . $tid . "&amp;team_id=" . $tm['team_id']. "'>" . $tm['name']."</a></b></th></tr>";
  echo "<tr bgcolor='$clr'><th colspan=4><b>Weapons</b></th></tr>";
  echo "<tr bgcolor='$clr'><th>LG%</th><th>RL D</th><th>SSG%</th><th>SG%</th></tr>";
  echo "<tr bgcolor='$cl2'>";
  echo "<td>".$tm[util::LG_ACCURACY]."</td>";
	echo "<td>".$tm[util::DIRECT_ROCKETS]."</td>";
	echo "<td>".$tm[util::SSG_ACCURACY]."</td>";
	echo "<td>".$tm[util::SG_ACCURACY]."</td>";
	echo "</tr>";
	if ($showpowerups == true)
	{
		echo "<tr bgcolor='$clr'><th colspan=4><b>Powerups</b></th></tr>";
		echo "<tr bgcolor='$clr'><th>Quads</th><th>Pents</th><th>Rings</th><th>&nbsp;</th></tr>";
		echo "<tr bgcolor='$cl2'>";
		echo "<td>".$tm[util::QUADS]."</td>"; 
		echo "<td>".$tm[util::PENTS]."</td>"; 
		echo "<td>".$tm[util::RINGS]."</td>"; 	
		echo "<td>&nbsp;</td>"; 	
		echo "</tr>";
	}
	echo "<tr bgcolor='$clr'><th colspan=4><b>Armors</b></th></tr>";
	echo "<tr bgcolor='$clr'><th>RA</th><th>YA</th><th>GA</th><th>&nbsp;</th></tr>";
  echo "<tr bgcolor='$cl2'>";
	echo "<td>".$tm[util::RED_ARMORS]."</td>";
	echo "<td>".$tm[util::YELLOW_ARMORS]."</td>"; 
	echo "<td>".$tm[util::GREEN_ARMORS]."</td>";
	echo "<td>&nbsp;</td>"; 		
	echo "</tr>";
	echo "<tr bgcolor='$clr'><th colspan=4><b>Damage &amp; Misc.</b></th></tr>";
	echo "<tr bgcolor='$clr'><th>DMG<br>Given</th><th>DMG<br>Taken</th><th>Mins<br>Played</th><th>With<br>Lead</th></tr>";
  echo "<tr bgcolor='$cl2'>";
	echo "<td>".$tm[util::DAMAGE_GIVEN]."</td>";
	echo "<td>".$tm[util::DAMAGE_TAKEN]."</td>";
	echo "<td>".$tm[util::MINUTESPLAYED]."</td>"; 
	echo "<td>".$tm[util::MINUTESWITHLEAD]."</td>"; 
	echo "</tr>";

	echo "</table>";
  echo "</td><td>&nbsp;</td>" ;
}
// END TEAM STATS

// End the stats table
echo "</tr></table>" ;

// Second table is the columns table

echo "<p>";
echo "<a href='?a=detailsMatch&amp;tourney_id=" . $tid . "&amp;match_id=" . $m->getValue('match_id'). "'>Match Details</a><p>";
?>
