<?php
$tid = $_REQUEST['tourney_id'];
$t = new tourney(array('tourney_id'=>$tid));

echo "<body bgcolor='#000000' text='#CCFFFF' link='#66FF99' vlink='#66FF99' alink='#00FF00'>";
echo "<h2>Admin Home</h2>";
echo "<b>Tourney specific Actions</b><br>";
echo "<table border=1 cellpadding=2 cellspacing=0>";
echo "<tr>";
echo "<td><font size='2'><a href='?a=listTourneys&tourney_id=$tid'>Select a Tourney</a><br></td>";
echo "<td><font color=red>",$t->getValue('name'),"</font></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font size='2'><a href='?a=manageDivision&tourney_id=$tid'>Create a division</a><br></td>";
echo "<td><font size='2'><a href='?a=listDivisions&tourney_id=$tid'>List divisions</a><br></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font size='2'><a href='?a=assignTeamsToDivs&tourney_id=$tid'>Assign Teams to Divs</a><br>";
echo "<td>&nbsp;</td>";
echo "</tr>";
echo "<tr>";
echo "<td><font size='2'><a href='?a=assignPlayersToTeams&tourney_id=$tid'>Assign Players to Teams</a><br>";
echo "<td>&nbsp;</td>";
echo "</tr>";
echo "<tr>";
echo "<td><font size='2'><a href='?a=assignMapsToTourney&tourney_id=$tid'>Assign Maps to Tourney</a><br>";
echo "<td>&nbsp;</td>";
echo "</tr>";
echo "</table><br>";
echo "<br>";
echo "<b>Global Actions</b><br>";
echo "<table border=1 cellpadding=2 cellspacing=0>";
echo "<tr>";
echo "<td><font size='2'><a href='?a=addTeam&tourney_id=$tid'>Create a team</a><br>";
echo "<td><font size='2'><a href='?a=listTeams&tourney_id=$tid'>List teams</a><br></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font size='2'><a href='?a=addPlayer&tourney_id=$tid'>Create a player</a><br>";
echo "<td><font size='2'><a href='?a=listPlayers&tourney_id=$tid'>List players</a><br></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font size='2'><a href='?a=addMap&tourney_id=$tid'>Create a map</a><br>";
echo "<td><font size='2'><a href='?a=listMaps&tourney_id=$tid'>List maps</a><br></td>";
echo "</tr>";
echo "</table>";
?>
