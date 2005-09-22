<?php

require_once 'login.php'; 

echo "<h2>Admin Home</h2>";
echo "<b>Actions</b><br>";
echo "<table border=1 cellpadding=2 cellspacing=0>";

if (util::isLoggedInAsPlayer())
{
  try
    {
      $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

      echo "<tr>";
      echo "<td><a href='?a=selectTourney'>Select Tourney</a></td>";
      echo "<td>&nbsp;</td>";
      echo "</tr>";

      if ($p->isSuperAdmin())
	{
	  echo "<tr>";
	  echo "<td><a href='?a=manageTourney'>Create Tourney</a></td>";
	  echo "<td><a href='?a=listTourneys'>Manage Tourneys</a></td>";
	  echo "</tr>";
	  echo "<tr>";
	  echo "<td><a href='?a=manageNews'>Create News</a></td>";
	  echo "<td><a href='?a=listNews'>Manage News</a></td>";
	  echo "</tr>";
	  echo "<tr>";
	  echo "<td><a href='?a=manageTeam'>Create Team</a></td>";
	  echo "<td><a href='?a=listTeams'>Manage Teams</a></td>";
	  echo "</tr>";
	  echo "<tr>";
	  echo "<td><a href='?a=managePlayer'>Create Player</a></td>";
	  echo "<td><a href='?a=listPlayers'>Manage Players</a></td>";
	  echo "</tr>";
	  echo "<tr>";
	  echo "<td><a href='?a=manageMap'>Create Map</a></td>";
	  echo "<td><a href='?a=listMaps'>Manage Maps</a></td>";
	  echo "</tr>";
	}

      if ($p->hasColumn())
	{
	  echo "<tr>";
	  echo "<td><a href='?a=manageColumn'>Create Column</a></td>";
	  echo "<td><a href='?a=listColumn'>Manage Columns</a></td>";
	  echo "</tr>";
	}
      
    }
  catch(Exception $e){}
}

elseif (util::isLoggedInAsTeam())
{
  try
    {
      $t = new team(array('team_id'=>$_SESSION['team_id'])) ;
      header("location: ?a=selectTourney") ;
    }
  catch(Exception $e) {}
}

echo "</table>";
?>
