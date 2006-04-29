<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];

include 'userLinks.php';
echo "<br>";
echo "<h2>Results</h2>";

$t = new tourney(array('tourney_id'=>$tid));

foreach ($t->getDivisions() as $div) 
{
	echo "<b>",$div->getValue('name'),"</b><br>";
	echo "<table border=1 cellpadding=2 cellspacing=0>\n";
	echo "<tr bgcolor='#999999'><th>Week</th><th>Result</th><th>Match Date</th></tr>";

	$matchCount = 0;
	foreach ($div->getMatchSchedule(array('deadline', SORT_ASC)) as $ms)
	{
		foreach($ms->getMatches() as $m)
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

			$teams = $m->getTeams() ;
			$t1 = $teams[0] ;
			$t2 = $teams[1] ;
			if ($m->getValue('approved'))
			{
				$matchCount++;
				echo "<tr bgcolor='$clr'>";
				echo "<td>",$ms->getValue('name'),"</td>";

			
				echo "<td><a href='?a=detailsMatch&amp;tourney_id=" . $tid. "&amp;match_id=" . $m->getValue('match_id') . "'>";
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
					echo "</td>";
					echo "<td>",$m->getValue('match_date'),"</td></tr>\n";
				}
				else
				{
				echo "<td>" . util::DEFAULT_DATE . "</td></tr>\n";

				}
			}
		}
	}
	echo "</table><br>\n";
	
	if ($matchCount == 0) {
		echo "<p>No matches have been played yet.</p>";
	}
}
?>