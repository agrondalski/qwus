<?php

require_once 'login.php'; 

$tid = $_REQUEST['tourney_id'];
$t = new tourney(array('tourney_id'=>$tid));

echo "<h2>Admin Home</h2>";
echo "<b>Tourney specific Actions</b><br>";
echo "<table border=1 cellpadding=2 cellspacing=0>";
echo "<tr>";
echo "<td><font size='2'><a href='?a=listTourneys&tourney_id=$tid'>Select a Tourney</a></td>";
echo "<td><font color=red>",$t->getValue('name'),"</font></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font size='2'><a href='?a=manageDivision&tourney_id=$tid'>Create new division</a></td>";
echo "<td><font size='2'><a href='?a=listDivisions&tourney_id=$tid'>Manage divisions</a></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font size='2'><a href='?a=manageNews&tourney_id=$tid'>Create news</a></td>";
echo "<td><font size='2'><a href='?a=listNews&tourney_id=$tid'>Manage news</a></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font size='2'><a href='?a=assignTeamsToDiv&tourney_id=$tid'>Assign Teams to a Div</a></td>";
echo "<td><font size='2'><a href='?a=manageSchedule&tourney_id=$tid'>Manage Schedule</a></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font size='2'><a href='?a=assignPlayersToTeam&tourney_id=$tid'>Assign Players to a Team</a></td>";
echo "<td><font size='2'><a href='?a=statistics&tourney_id=$tid'>do:Statistics</a></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font size='2'><a href='?a=assignMapsToTourney&tourney_id=$tid'>Assign Maps to Tourney</a></td>";
echo "<td><font size='2'><a href='?a=reportMatch&tourney_id=$tid'>do:Report Match</a></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font size='2'><a href='?a=standings&tourney_id=$tid'>do:Standings</a></td>";
echo "<td><font size='2'><a href='?a=schedule&tourney_id=$tid'>do:Schedule</a></td>";
echo "</tr>";
echo "</table><br>";
echo "<br>";
echo "<b>Global Actions</b><br>";
echo "<table border=1 cellpadding=2 cellspacing=0>";
echo "<tr>";
echo "<td><font size='2'><a href='?a=manageTeam&tourney_id=$tid'>Create a team</a></td>";
echo "<td><font size='2'><a href='?a=listTeams&tourney_id=$tid'>Manage teams</a></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font size='2'><a href='?a=managePlayer&tourney_id=$tid'>Create a player</a></td>";
echo "<td><font size='2'><a href='?a=listPlayers&tourney_id=$tid'>Manage players</a></td>";
echo "</tr>";
echo "<tr>";
echo "<td>&nbsp;</td>";
echo "<td><font size='2'><a href='?a=listMaps&tourney_id=$tid'>List maps</a></td>";
echo "</tr>";
echo "</table>";
?>
