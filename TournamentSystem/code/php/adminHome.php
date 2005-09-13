<?php

require_once 'login.php'; 

echo "<h2>Admin Home</h2>";
echo "<b>Actions</b><br>";
echo "<table border=1 cellpadding=2 cellspacing=0>";
echo "<tr>";
echo "<td><a href='?a=listTourneys'>Select Tourney</a></td>";
echo "<td>&nbsp;</td>";
echo "</tr>";

try
{
  $p = new player(array('name'=>$_SESSION['username'])) ;
  if ($p->hasColumn())
    {
      echo "<tr>";
      echo "<td><a href='?a=manageColumn'>Create Column</a></td>";
      echo "<td><a href='?a=listColumn'>Manage Column</a></td>";
      echo "</tr>";
    }

  if ($p->isSuperAdmin())
    {
      echo "<tr>";
      echo "<td><a href='?a=manageNews'>Create News</a></td>";
      echo "<td><a href='?a=listNews'>Manage News</a></td>";
      echo "</tr>";
      echo "<tr>";
      echo "<td><a href='?a=manageTeam'>Create a Team</a></td>";
      echo "<td><a href='?a=listTeams'>Manage Teams</a></td>";
      echo "</tr>";
      echo "<tr>";
      echo "<td><a href='?a=managePlayer'>Create a Player</a></td>";
      echo "<td><a href='?a=listPlayers'>Manage Players</a></td>";
      echo "</tr>";
      echo "<tr>";
      echo "<td>&nbsp;</td>";
      echo "<td><a href='?a=listMaps'>List Maps</a></td>";
      echo "</tr>";
    }
}
catch(Exception $e){}

echo "</table>";
?>
