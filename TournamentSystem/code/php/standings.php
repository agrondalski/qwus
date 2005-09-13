<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];

$t = new tourney(array('tourney_id'=>$tid));

include 'tourneyLinks.php';
echo "<br>";
echo "<h2>Standings</h2>";
echo "<p></p><hr><p></p>";


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
