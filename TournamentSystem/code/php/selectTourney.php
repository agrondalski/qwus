<?php
require_once 'includes.php' ;
require_once 'login.php' ;

try
{
  if (!util::isNull($_SESSION['user_id']))
    {
      $p = new player(array('player_id'=>$_SESSION['user_id'])) ;
      $first_time = true ;
       
      foreach (tourney::getAllTourneys() as $t)
	{
	  if ($p->isSuperAdmin()) ;
	    {
	      if ($first_time)
		{
		  echo "<table border=1 cellpadding=2 cellspacing=0>\n";
		  echo "<th>Tourney Name</th>";

		  $first_time = false ;
		}

	      echo "\t<tr>\n";
	      echo "\t<td><a href='?a=tourneyHome&tourney_id=" . $t->getValue('tourney_id') . "'>" . $t->getValue('name') . "</a></td>\n";
	      echo "\t</tr>\n";
	    }
	}

      if (!$first_time)
	{
	  echo "</table>\n";
	}
    }

  elseif (!util::isNull($_SESSION['team_id']))
    {
      $tm = new team(array('team_id'=>$_SESSION['team_id'])) ;

      echo "<h2>Manage Team</h2>";
      echo "<table border=1 cellpadding=2 cellspacing=0>\n";
      echo "<tr><td><a href='?a=manageTeam&amp;mode=edit&amp;team_id=" . $_SESSION['team_id'] . "'> Manage Team</a></td></tr>" ;
      echo "</table>" ;

      $mode = $_REQUEST['mode'];
      if ($mode=='delete')
	{
	  $t = new tourney(array('tourney_id'=>$_REQUEST['tourney_id']));

	  if ($t->hasTeam($tm->getValue('team_id')))
	    {
	      try
		{
		  $t->removeTeam($tm->getValue('team_id')) ;
		  $msg = "<br>Your team has been removed from tourney!<br>";
		}
	      catch (Exception $e)
		{
		  $msg = "<br>Error removing your team!<br>";
		}
	    }
	  else
	    {
	      $msg = "<br>Error!<br>";
	    } 
	}
      
      // add new
      elseif ($mode=='add')
	{
	  $t = new tourney(array('tourney_id'=>$_REQUEST['tourney_id']));

	  if (!$t->hasTeam($tm->getValue('team_id')))
	    {
	      try
		{
		  $t->addTeam($tm->getValue('team_id')) ;
		  $msg = "<br>Your team has been added to the tourney!<br>";
		}
	      catch (Exception $e)
		{
		  $msg = "<br>Error adding your team!<br>";
		}
	    }
	  else
	    {
	      $msg = "<br>Error!<br>";
	    } 
	}

      echo "<h2>Tourney Home</h2>";
      $first_time = true ;
      foreach ($tm->getTourneys() as $t)
	{
	  if ($first_time)
	    {
	      echo "<table border=1 cellpadding=2 cellspacing=0>\n";
	      echo "<th>Tourney Name</th>";

	      $first_time = false ;
	    }

	  echo "\t<tr>\n";
	  echo "\t<td><a href='?a=tourneyHome&tourney_id=" . $t->getValue('tourney_id') . "'>" . $t->getValue('name') . "</a></td>\n";
	  echo "\t</tr>\n";
	}

      if (!$first_time)
	{
	  echo "</table>\n";
	}

      echo "<h2>Signup</h2>";
      $first_time = true ;
      foreach (tourney::getTourneysByStatus(tourney::STATUS_SIGNUPS) as $t)
	{
	  if ($first_time)
	    {
	      echo "<table border=1 cellpadding=2 cellspacing=0>\n";
	      echo "<th>Tourney Name</th><th>Tourney Type</th><th># of players</th><th>Action</th>";

	      $first_time = false ;
	    }

	  echo "\t<tr>\n";
	  echo "\t<td>" . $t->getValue('name') . "</td>\n";
	  echo "\t<td>" . $t->getValue('tourney_type') . "</td>\n";
	  echo "\t<td>" . $t->getValue('team_size') . "x" . $t->getValue('team_size') . "</td>\n";

	  if ($t->hasTeam($tm->getValue('team_id')))
	    {
	      echo "\t<td><a href='?a=selectTourney&tourney_id=" . $t->getValue('tourney_id') . "&amp;mode=delete'>Withdraw</a></td>\n";
	    }
	  else
	    {
	      echo "\t<td><a href='?a=selectTourney&tourney_id=" . $t->getValue('tourney_id') . "&amp;mode=add'>Signup</a></td>\n";
	    }

	  echo "\t</tr>\n";
	}

      if (!$first_time)
	{
	  echo "</table>\n";
	}

      print $msg ;
    }
}
catch (Exception $e) {}
?>
