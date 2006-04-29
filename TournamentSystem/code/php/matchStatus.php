<?php
require 'includes.php' ;
require_once 'login.php'; 

$tid = $_REQUEST['tourney_id'];

include 'userLinks.php';
echo "<br>";
echo "<h2>Match Status Page</h2>";

echo "<p>Click the 'team vs team' link to Approve a match, or report it.  Click on the games link to view the stats/details page.</p>";
$t = new tourney(array('tourney_id'=>$tid));

 if (util::isLoggedInAsPlayer())
	{
		$p = new player(array('player_id'=>$_SESSION['user_id'])) ;

		if (!$p->isSuperAdmin() && !$p->isTourneyAdmin($tid))
		{
			echo "<p>You are not authorized to view this page!";
			util::throwException('not authorized') ;
		}

		foreach ($t->getDivisions() as $div) 
		{
			$cnt = 0;
			echo "<b>",$div->getValue('name'),"</b><br>";
			echo "<table border=1 cellpadding=4 cellspacing=0>\n";
			echo "<tr bgcolor='#999999'><th>Week / Deadline</th><th>Report Match / Match Details</th><th>Games</th><th>Approved?</th></tr>";
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

						echo "<tr bgcolor='$clr'>";

						$teams = $m->getTeams() ;
						$t1 = $teams[0] ;
						$t2 = $teams[1] ;

						// Week - Deadline
						echo "<td>",$ms->getValue('name')," ",$ms->getValue('deadline'),"</td>";

						echo "<td><a href='?a=reportMatch&amp;tourney_id=" . $tid. "&amp;division_id=".$ms->getValue('division_id')."&amp;match_id=" . $m->getValue('match_id') . "'>";
						echo $t1->getValue('name');
						echo "&nbsp;vs&nbsp;";
						echo $t2->getValue('name')."</a>";
						echo "</td>\n";

						echo "<td nowrap>";
						echo "<a href='?a=detailsMatch&amp;tourney_id=".$tid."&amp;match_id=".$m->getValue('match_id')."'>";
						$gameCount = 0;
						try {
							foreach ($m->getGames() as $g)
								{	
									$gameCount++;
									$map = new map(array('map_id'=>$g->getValue('map_id')));
									echo $map->getValue('map_abbr')." (".$g->getValue('team1_score')."&nbsp;-&nbsp;".$g->getValue('team2_score').")<br>";						
							}
						} catch (Exception $e) {
							continue;
						}
						if ($gameCount > 0) {
							echo "</a></td>";
						} else {
							echo "No Games Reported</a></td>";
						}

						echo "<td>".$m->getValue('approved')."</td>";
						echo "</tr>";

					}
				}
			echo "</table><br>\n";
		}
} else {
		echo "<p>You are not authorized to view this page!";
		util::throwException('not authorized') ;
}
?>