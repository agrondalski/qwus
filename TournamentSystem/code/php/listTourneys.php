<?php
require_once 'includes.php' ;
require_once 'login.php' ;

$tourneys = tourney::getAllTourneys();

try
{
  $p = new player(array('name'=>$_SESSION['username'])) ;
  $first_time = true ;

  foreach ($tourneys as $t)
    {
      if ($p->isSuperAdmin() || $p->isTourneyAdmin($t->getValue('tourney_id')))
	{
	  if ($first_time)
	    {
	      echo "<table border=1 cellpadding=2 cellspacing=0>\n";
	      echo "<th>tourney_id</th><th>Tourney Name</th>";

	      $first_time = false ;
	    }

	  echo "\t<tr>\n";
	  echo "\t<td>",$t->getValue('tourney_id'),"</td>\n";
	  echo "\t<td><a href='?a=tourneyHome&tourney_id=",$t->getValue('tourney_id'),"'>",$t->getValue('name'),"</a></td>\n";
	  echo "\t</tr>\n";
	}
    }

  if (!$first_time)
    {
      echo "</table>\n";
    }
}
catch(Exception $e){}

?>
