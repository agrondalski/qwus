<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];

include 'userLinks.php';
echo "<br>";
echo "<h2>Standings</h2>";

$t = new tourney(array('tourney_id'=>$tid));

foreach ($t->getDivisions() as $div) 
{
  echo "<b>",$div->getValue('name'),"</b><br>";
  echo "<table border=1 cellpadding=2 cellspacing=0>\n";
  echo "<tr bgcolor='#999999'>";
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

  foreach ($div->getSortedTeamStats(array(util::POINTS, SORT_DESC, util::GAMES_WON, SORT_DESC, util::GAMES_LOST, SORT_ASC, util::SCORE_DIFF, SORT_DESC, 'name', SORT_ASC)) as $tm)
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
      
      if ($rank % 2 == 1) 
	{
	  $clr = "#CCCCCC";
	}
      else
	{
	  $clr = "#C0C0C0";
	}
		
      echo "<tr bgcolor='$clr'>";
      echo "<td nowrap>",$rank,"</td>";
      echo "<td nowrap><a href='?a=detailsTeam&amp;tourney_id=",$tid,"&amp;team_id=",$tm['team_id'],"'>";
      echo $tm['name'],"</a></td>";
      echo "<td nowrap>",$tm[util::MATCHES_WON]," (",$m20," ",$m21,")</td>"; 
      echo "<td nowrap>",$tm[util::MATCHES_LOST]," (",$m12," ",$m02,")</td>"; 
      echo "<td nowrap>",$tm[util::GAMES_WON],"&nbsp;</td>"; 
      echo "<td nowrap>",$tm[util::GAMES_LOST],"&nbsp;</td>"; 
      echo "<td nowrap>",$tm[util::POINTS],"&nbsp;</td>"; 
      echo "<td nowrap>",$tm[util::TOTAL_SCORE],"&nbsp;</td>"; 
      echo "<td nowrap>",$tm[util::TOTAL_SCORE_OPP],"&nbsp;</td>"; 

      echo "<td nowrap>",$tm[util::SCORE_DIFF],"&nbsp;</td>"; 

      if ($tm[util::WINNING_STREAK] != null) 
	{
	  echo "<td nowrap>",$tm[util::WINNING_STREAK],"W</td>";  
	} 
      elseif ($tm[util::LOSING_STREAK] != null)
	{
	  echo "<td nowrap>",$tm[util::LOSING_STREAK],"L</td>"; 
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
