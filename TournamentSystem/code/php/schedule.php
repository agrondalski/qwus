<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];

include 'userLinks.php';
echo "<br>";
echo "<h2>Schedule</h2>";

$t = new tourney(array('tourney_id'=>$tid));

foreach ($t->getDivisions() as $div) 
{
  $cnt = 0;
  echo "<b>",$div->getValue('name'),"</b><br>";
  echo "<table border=1 cellpadding=4 cellspacing=0>\n";
  
  foreach ($div->getMatchSchedule(array('deadline', SORT_ASC)) as $ms)
    {
      foreach($ms->getMatches() as $m)
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

				if ($m->getValue('approved')==1) {
					echo "<tr bgcolor='#999999'>";
				} else { 
					echo "<tr bgcolor='$clr'>";
				}
				
				$teams = $m->getTeams() ;
				$t1 = $teams[0] ;
				$t2 = $teams[1] ;

				// Week - Deadline
				echo "<td>",$ms->getValue('name')," ",$ms->getValue('deadline'),"</td>";

				// Team 1 vs Team 2	  	  
				if ($m->getValue('approved')==1)
				{
					echo "<td><a href='?a=detailsMatch&amp;tourney_id=" . $tid. "&amp;match_id=" . $m->getValue('match_id') . "'>";
					echo $t1->getValue('name');
					echo "&nbsp;vs&nbsp;";
					echo $t2->getValue('name');
					echo "</td></tr>\n";
				} else {
					echo "<td>";
					echo "<a href='?a=detailsTeam&amp;tourney_id=".$tid."&amp;team_id=".$t1->getValue('team_id')."'>".$t1->getValue('name')."</a>";
					//echo "&nbsp;<a href='?a=detailsMatch&amp;tourney_id=" . $tid. "&amp;match_id=".$m->getValue('match_id')."'>vs</a>&nbsp;";
					echo "&nbsp;<b>vs</b>&nbsp;";
					echo "<a href='?a=detailsTeam&amp;tourney_id=".$tid."&amp;team_id=".$t2->getValue('team_id')."'>".$t2->getValue('name')."</a>";
					echo "</td></tr>\n";
				}

			}
    }
  echo "</table><br>\n";
}
?>