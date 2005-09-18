<?php
require_once 'includes.php' ;
require_once 'login.php' ;

if ($_SESSION['user_id'])
{
  try
    {
      $p = new player(array('player_id'=>$_SESSION['user_id'])) ;
      $first_time = true ;
       
      foreach (tourney::getAllTourneys() as $t)
	{
	  if ($p->isSuperAdmin() || $p->isTourneyAdmin($t->getValue('tourney_id')))
	    {
	      if ($first_time)
		{
		  echo "<table border=1 cellpadding=2 cellspacing=0>\n";
		  echo "<th>Tourney Name</th>";

		  $first_time = false ;
		}

	      echo "\t<tr>\n";
	      echo "\t<td><a href='?a=tourneyHome&tourney_id=",$t->getValue('tourney_id'),"'>",$t->getValue('name'),"</a></td>\n";
	      echo "\t</tr>\n";
	    }
	}

      if (!$first_time)
	{
	  echo "</table>\n";
	}
    }
  catch (Exception $e){}
}

elseif ($_SESSION['team_id'])
{
  try
    {
      $t = new team(array('team_id'=>$_SESSION['team_id'])) ;
      $first_time = true ;

      foreach ($t->getTourneys() as $t)
	{
	  if ($first_time)
	    {
	      echo "<table border=1 cellpadding=2 cellspacing=0>\n";
	      echo "<th>Tourney Name</th>";

	      $first_time = false ;
	    }

	  echo "\t<tr>\n";
	  echo "\t<td><a href='?a=tourneyHome&tourney_id=",$t->getValue('tourney_id'),"'>",$t->getValue('name'),"</a></td>\n";
	  echo "\t</tr>\n";
	}

      if (!$first_time)
	{
	  echo "</table>\n";
	}
    }
  catch (Exception $e) {}
}
?>
