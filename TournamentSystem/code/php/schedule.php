<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];
$t = new tourney(array('tourney_id'=>$tid));

include 'userLinks.php';
echo "<br>";

//$schedule_id = $_REQUEST['schedule_id'];

// Results section

echo "<h2>Schedule</h2>";

foreach ($t->getDivisions() as $div) 
{
	$cnt = 0;
	echo "<b>",$div->getValue('name'),"</b><br>";
	echo "<table border=1 cellpadding=4 cellspacing=0>\n";
	foreach ($div->getMatches() as $m)
	{
		$cnt += 1;
		if ($cnt % 2 == 1) 
		{
			$clr = "#CCCCCC";
		}
		else
		{
			$clr = "#C0C0C0";
		}
			
		echo "<tr bgcolor='$clr'>";
		$t1 = new team(array('team_id'=>$m->getValue('team1_id')));
		$t2 = new team(array('team_id'=>$m->getValue('team2_id')));
		$ms = new match_schedule(array('schedule_id'=>$m->getValue('schedule_id')));
		echo "<td>",$ms->getValue('name')," ",$ms->getValue('deadline'),"</td>";
		echo "<td>";
		echo $t1->getValue('name');
		echo "&nbsp;vs&nbsp;";
		echo $t2->getValue('name');
		echo "</td></tr>\n";
	}
	echo "</table><br>\n";
}
?>