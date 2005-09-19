
<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];

$t = new tourney(array('tourney_id'=>$tid));

include 'userLinks.php';
echo "<br>";

// Results section

echo "<h2>Results</h2>";
foreach ($t->getDivisions() as $div) 
{
	echo "<b>",$div->getValue('name'),"</b><br>";
	echo "<table border=1 cellpadding=2 cellspacing=0>\n";
	echo "<tr><th>Week</th><th>Result</th><th>Match Date</th></tr>";
	foreach ($div->getMatches() as $m)
	{
		$wid = $m->getValue('winning_team_id');

		$t1 = new team(array('team_id'=>$m->getValue('team1_id')));
		$t2 = new team(array('team_id'=>$m->getValue('team2_id')));
		$ms = new match_schedule(array('schedule_id'=>$m->getValue('schedule_id')));
		echo "<tr>";
		echo "<td>",$ms->getValue('name'),"</td>";
		echo "<td><a href='?a=detailsMatch&amp;tourney_id=",$tid,"&amp;match_id=",$m->getValue('match_id'),"'>";
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
			//$map = new map(array('map_id'=>$g->getValue('map_id')));
			//echo "<tr>";
			//echo "<td>",$map->getValue('map_abbr'),"</td>";
			//echo "<td>",$g->getValue('team1_score')," - ";
			//echo $g->getValue('team2_score'),"</td>";
			//echo "</tr>\n";
		}
		echo " (",$team1,"-",$team2,")</a>";
		echo "</td>";
		echo "<td>",$m->getValue('match_date'),"</td></tr>\n";
	}
	echo "</table><br>\n";
}
?>