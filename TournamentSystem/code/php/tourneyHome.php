<?php

require_once 'login.php'; 

$tid = $_REQUEST['tourney_id'];
$t = new tourney(array('tourney_id'=>$tid));

try
{
  $p = new player(array('name'=>$_SESSION['username'])) ;

  if (!$p->isSuperAdmin() && !$p->isTourneyAdmin($tid))
    {
      return ;
    }
}
catch(Exception $e)
{
  return ;
}

echo "<h2>Tourney Home</h2>";
echo "<b>Tourney specific Actions</b><br>";
echo "<table border=1 cellpadding=2 cellspacing=0>";
echo "<tr>";
echo "<td><a href='?a=listTourneys&tourney_id=$tid'>Select a Tourney</a></td>";
echo "<td><font color=red>",$t->getValue('name'),"</font></td>";
echo "</tr>";
echo "<tr>";
echo "<td><a href='?a=manageDivision&tourney_id=$tid'>Create Division</a></td>";
echo "<td><a href='?a=listDivisions&tourney_id=$tid'>Manage Divisions</a></td>";
echo "</tr>";
echo "<tr>";
echo "<td><a href='?a=manageNews&tourney_id=$tid'>Create News</a></td>";
echo "<td><a href='?a=listNews&tourney_id=$tid'>Manage News</a></td>";
echo "</tr>";
echo "<tr>";
echo "<td><a href='?a=assignTeamsToDiv&tourney_id=$tid'>Assign Teams to a Div</a></td>";
echo "<td><a href='?a=manageSchedule&tourney_id=$tid'>Manage Schedule</a></td>";
echo "</tr>";
echo "<tr>";
echo "<td><a href='?a=assignPlayersToTeam&tourney_id=$tid'>Assign Players to a Team</a></td>";
echo "<td><a href='?a=statistics&tourney_id=$tid'>do:Statistics</a></td>";
echo "</tr>";
echo "<tr>";
echo "<td><a href='?a=assignMapsToTourney&tourney_id=$tid'>Assign Maps to Tourney</a></td>";
echo "<td><a href='?a=reportMatch&tourney_id=$tid'>do:Report Match</a></td>";
echo "</tr>";
echo "<tr>";
echo "<td><a href='?a=standings&tourney_id=$tid'>do:Standings</a></td>";
echo "<td><a href='?a=schedule&tourney_id=$tid'>do:Schedule</a></td>";
echo "</tr>";
echo "</table><br>";
?>
