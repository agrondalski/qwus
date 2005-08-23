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
echo "<h2>Tourney Home</h2>";
echo "<p><font size='2'><a href='addDivision.php?tourney_id=$tid'>Create a division</a><br>";
echo "<a href='addTeam.php?tourney_id=$tid'>Add a team</a><br>";
echo "<a href='addPlayer.php?tourney_id=$tid'>Add a player</a><br>";
echo "<a href='addMap.php?tourney_id=$tid'>Add a map</a><br>";
echo "-----<br>";
echo "<a href='tourneyAdmins.php?tourney_id=$tid'>Assign admin privelages</a><br>";
echo "</font></html>";
?>
