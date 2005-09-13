<?php

require_once 'login.php'; 

echo "<b>Actions</b><br>";
echo "<table border=1 cellpadding=2 cellspacing=0>";
echo "<tr>";
echo "<td><font size='2'><a href='?a=listTourneys'>Manage Tourney</a></td>";
echo "<td>&nbsp;</td>";
echo "</tr>";

try
{
  $p = new player(array('name'=>$_SESSION['username'])) ;
  if ($p->hasColumn())
    {
      echo "<tr>";
      echo "<td><font size='2'><a href='?a=manageColumn'>Create Column</a></td>";
      echo "<td><font size='2'><a href='?a=listColumn'>Manage Column</a></td>";
      echo "</tr>";
    }

  if ($p->isSuperAdmin())
    {
      echo "<tr>";
      echo "<td><font size='2'><a href='?a=manageNews'>Create News</a></td>";
      echo "<td><font size='2'><a href='?a=listNews'>Manage News</a></td>";
      echo "</tr>";
      echo "<tr>";
      echo "<td><font size='2'><a href='?a=manageTeam'>Create a Team</a></td>";
      echo "<td><font size='2'><a href='?a=listTeams'>Manage Teams</a></td>";
      echo "</tr>";
      echo "<tr>";
      echo "<td><font size='2'><a href='?a=managePlayer'>Create a Player</a></td>";
      echo "<td><font size='2'><a href='?a=listPlayers'>Manage Players</a></td>";
      echo "</tr>";
      echo "<tr>";
      echo "<td>&nbsp;</td>";
      echo "<td><font size='2'><a href='?a=listMaps'>List Maps</a></td>";
      echo "</tr>";
    }
}
catch(Exception $e){}

echo "</table>";
?>
