<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];

$t = new tourney(array('tourney_id'=>$tid));

include 'tourneyLinks.php';
echo "<br>";
echo "<h2>Standings</h2>";

foreach ($t->getDivisions() as $div) 
{
	echo "<b>",$div->getValue('name'),"</b><br>";
	echo "<table border=1 cellpadding=2 cellspacing=0>\n";
	echo "<tr>";
	echo "<th>#</th>";
	echo "<th>Team</th>";
	echo "<th>W (2-0 2-1)</th>";
	echo "<th>L (1-2 0-2)</th>";
	echo "<th>GW</th>";
	echo "<th>GL</th>";
	echo "<th>Pts</th>";
	echo "<th>FF</th>";
	echo "<th>FA</th>";		
	echo "<th>Strk</th>";	
	echo "</tr>\n";
	$rank = 0;
	foreach ($div->getTeams() as $tm)
	{
		$rank += 1;
		$info = $tm->getDivisionInfo($div->getValue('division_id'));
		$m20 = $info['match_2-0'];
		$m21 = $info['match_2-1'];
		$m12 = $info['match_1-2'];
		$m02 = $info['match_0-2'];
		if ($m20 == null) 
		{
			$m20 = "0";
		}
		if ($m21 == null) 
		{
			$m21 = "0";
		}
		if ($m12 == null) 
		{
			$m12 = "0";
		}
		if ($m02 == null) 
		{
			$m02 = "0";
		}
		
		echo "<tr>";
		echo "<td>",$rank,"</td>";
		echo "<td><a href='?a=detailsTeam&amp;tourney_id=",$tid,"&amp;team_id=",$tm->getValue('team_id'),"'>";
		echo $tm->getValue('name'),"</a></td>";
		echo "<td>",$info['wins']," (",$m20," ",$m21,")</td>"; 
		echo "<td>",$info['losses']," (",$m12," ",$m02,")</td>"; 
		echo "<td>",$info['maps_won'],"&nbsp;</td>"; 
		echo "<td>",$info['maps_lost'],"&nbsp;</td>"; 
		echo "<td>",$info['points'],"&nbsp;</td>"; 
		//echo "<td>",$info['max_score'],"</td>"; 
		//echo "<td>",$info['min_score'],"</td>"; 
		//echo "<td>",$info['avg_score'],"</td>"; 
		echo "<td>",$info['frags_for'],"&nbsp;</td>"; 
		echo "<td>",$info['frags_against'],"&nbsp;</td>"; 
		if ($info['winning_streak'] != null) 
		{
			echo "<td>",$info['winning_streak'],"W</td>";  
		} 
		elseif ($info['losing_streak'] != null)
		{
			echo "<td>",$info['losing_streak'],"L</td>"; 
		} 
		else 
		{
			echo "<td>&nbsp;</td>";
		}

		echo "<tr>\n";	
	}
	echo "</table><br>\n";
	
}


// Results section

echo "<h2>Results</h2>";

foreach ($t->getDivisions() as $div) 
{
	echo "<b>",$div->getValue('name'),"</b><br>";
	echo "<table border=1 cellpadding=2 cellspacing=0>\n";
	foreach ($div->getMatches() as $m)
	{
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
		echo "</td></tr>\n";
		foreach ($m->getGames() as $g)
		{			
			$map = new map(array('map_id'=>$g->getValue('map_id')));
			echo "<tr>";
			echo "<td>",$map->getValue('map_abbr'),"</td>";
			echo "<td>",$g->getValue('team1_score')," - ";
			echo $g->getValue('team2_score'),"</td>";
			echo "</tr>\n";
		}
	}
	echo "</table><br>\n";
}
?>
