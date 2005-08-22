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
echo "<p>&nbsp;</p>";
echo "<p><font size='2'><a href='addDivision.php?tourney_id=$tid'>Create a division</a><a href='addTeam.php?tourney_id=$tid'><br>";
echo "Add a team</a><a href='addPlayer.php?tourney_id=$tid'><br>";
echo "Add a player</a><a href='tourneyAdmins.php?tourney_id=$tid'><br>";
echo "Assign admin privelages</a><a href='addMap.php?tourney_id=$tid'><br>";
echo "Add a map</a></font></p>";
echo "</html>";
?>
