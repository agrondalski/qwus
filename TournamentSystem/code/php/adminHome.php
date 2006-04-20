<?php

require_once 'login.php'; 

try
{
  if (util::isLoggedInAsPlayer())
    {
      $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

      echo "<h2>Admin Home</h2>";
      echo "<b>Actions</b><br>";
      echo "<table border=1 cellpadding=2 cellspacing=0>";
      echo "<tr><td><a href='?a=managePlayer&amp;mode=edit&amp;player_id=" . $_SESSION['user_id'] . "'> Manage " . $_SESSION['username'] . "</a></td><td>&nbsp;</td></tr>" ;

      echo "<tr>";
      echo "<td><a href='?a=selectTourney'>Select Tourney</a></td>";
      echo "<td>&nbsp;</td>" ;
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
	  echo "<tr>";
	  echo "<td><a href='?a=recomputeGame&all=1'>Recompute Matches</a></td>";
	  echo "<td>&nbsp;</td>" ;
	  echo "</tr>";
	}

      if ($p->hasColumn())
	{
	  echo "<tr>";
	  echo "<td><a href='?a=manageColumn'>Create Column</a></td>";
	  echo "<td><a href='?a=listColumn'>Manage Columns</a></td>";
	  echo "</tr>";
	}      

      echo "</table>" ;
    }

  elseif (util::isLoggedInAsTeam())
    {
      $t = new team(array('team_id'=>$_SESSION['team_id'])) ;

      echo "<h2>Admin Home</h2>";
      echo "<b>Actions</b><br>";
      echo "<table border=1 cellpadding=2 cellspacing=0>\n";
      echo "<tr><td><a href='?a=selectTourney'>Select Tourney</a></td><td>&nbsp;</td></tr>" ;
      echo "<tr><td><a href='?a=manageTeam&amp;mode=edit&amp;team_id=" . $_SESSION['team_id'] . "'> Manage Team</a></td>";
      echo "<td><a href='?a=managePlayer'>Create Player</a></td></tr>";
      echo "</tr></table>" ;
      echo "<p>Click <i>Select Tourney</i> to sign up for leagues/tourneys and Assign existing players to your league rosters.  If a player has been in a tourney at quakeworld.us ";
      echo "before, you do NOT need to re-add them.  Simply Assign them to your team roster (for the correct league).  If you need to create new players, use ";
      echo "the <i>Create Player</i> link. ";
    }
}
catch(Exception $e) {}

?>
