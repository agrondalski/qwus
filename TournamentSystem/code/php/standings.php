<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];

$t = new tourney(array('tourney_id'=>$tid));

include 'userLinks.php';
echo "<br>";
echo "<h2>Standings</h2>";

foreach ($t->getDivisions() as $div) 
{
	echo "<b>",$div->getValue('name'),"</b><br>";
	echo "<table border=1 cellpadding=2 cellspacing=0>\n";
	echo "<tr>";
	echo "<th>#</th>";
	echo "<th>Team</th>";
	echo "<th nowrap>W (2-0 2-1)</th>";
	echo "<th nowrap>L (1-2 0-2)</th>";
	echo "<th>GW</th>";
	echo "<th>GL</th>";
	echo "<th>Pts</th>";
	echo "<th>FF</th>";
	echo "<th>FA</th>";		
	echo "<th>FD</th>";	
	echo "<th>Strk</th>";	
	echo "</tr>\n";
	$rank = 0;
	//foreach ($div->getTeams() as $tm)
	foreach ($div->getSortedTeamInfo(array('points', SORT_DESC, 'maps_lost', SORT_ASC, 'frags_for', SORT_DESC)) as $tm)
	{
		$rank += 1;		
		$m20 = $tm['match_2-0'];
		$m21 = $tm['match_2-1'];
		$m12 = $tm['match_1-2'];
		$m02 = $tm['match_0-2'];
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
		echo "<td nowrap>",$rank,"</td>";
		echo "<td nowrap><a href='?a=detailsTeam&amp;tourney_id=",$tid,"&amp;team_id=",$tm['team_id'],"'>";
		echo $tm['name'],"</a></td>";
		echo "<td nowrap>",$tm['wins']," (",$m20," ",$m21,")</td>"; 
		echo "<td nowrap>",$tm['losses']," (",$m12," ",$m02,")</td>"; 
		echo "<td nowrap>",$tm['maps_won'],"&nbsp;</td>"; 
		echo "<td nowrap>",$tm['maps_lost'],"&nbsp;</td>"; 
		echo "<td nowrap>",$tm['points'],"&nbsp;</td>"; 
		$frag_difference = ($tm['frags_for']-$tm['frags_against']);
		//echo "<td>",$info['max_score'],"</td>"; 
		//echo "<td>",$info['min_score'],"</td>"; 
		//echo "<td>",$info['avg_score'],"</td>"; 
		echo "<td nowrap>",$tm['frags_for'],"&nbsp;</td>"; 
		echo "<td nowrap>",$tm['frags_against'],"&nbsp;</td>"; 
		echo "<td nowrap>",$frag_difference,"&nbsp;</td>"; 
		if ($tm['winning_streak'] != null) 
		{
			echo "<td nowrap>",$tm['winning_streak'],"W</td>";  
		} 
		elseif ($tm['losing_streak'] != null)
		{
			echo "<td nowrap>",$tm['losing_streak'],"L</td>"; 
		} 
		else 
		{
			echo "<td>&nbsp;</td>";
		}

		echo "<tr>\n";	
	}
	echo "</table><br>\n";
	
}


?>
