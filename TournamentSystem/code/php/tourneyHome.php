<html>
<head>
<script language="JavaScript">
</script>
<title>NAQL</title>
<style type="text/css">
<!--
a:link { text-decoration: none }
a:active { text-decoration: none }
a:visited { text-decoration: none }
a:hover {text-decoration: underline; color: #ff0000}-->
</style>
<base target="_self">
</head>
<?php
$tid = $_REQUEST['tourney_id'];

echo "<body bgcolor='#000000' text='#CCFFFF' link='#66FF99' vlink='#66FF99' alink='#00FF00'>";
echo "<h2>Admin Home</h2>";
echo "<table border=1 cellpadding=2 cellspacing=0>";
echo "<tr>";
echo "<td><font size='2'>-<br></td>";
echo "<td><font size='2'><a href='?a=listTourneys&tourney_id=$tid'>List tourneys</a><br></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font size='2'><a href='?a=addDivision&tourney_id=$tid'>Create a division</a><br></td>";
echo "<td><font size='2'><a href='?a=listDivisions&tourney_id=$tid'>List divisions</a><br></td>";
echo "</tr>";
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
echo "</html>";
?>
