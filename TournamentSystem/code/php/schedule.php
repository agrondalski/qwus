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
  
  foreach ($div->getMatchSchedule(array('name', SORT_ASC)) as $ms)
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

	  echo "<td>",$ms->getValue('name')," ",$ms->getValue('deadline'),"</td>";
	  echo "<td>";
	  echo $t1->getValue('name');
	  echo "&nbsp;vs&nbsp;";
	  echo $t2->getValue('name');
	  echo "</td></tr>\n";
	}
    }
  echo "</table><br>\n";
}
?>