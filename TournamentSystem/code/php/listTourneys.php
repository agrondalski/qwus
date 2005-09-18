<?php

require 'includes.php';
require_once 'login.php' ;

try
{
  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  if (!$p->isSuperAdmin())
    {
      util::throwException('not authorized') ;
    }

  // Printing results in HTML
  echo "<table border=1 cellpadding=2 cellspacing=0>\n";
  echo "<th>Name</th><th>Game Type</th><th>Tournament Type</th><th>Signup Start</th><th>Signup End</th><th>Teamsize</th><th>Timelimit</th><th colspan=2>Actions</th>";

  foreach (tourney::getAllTourneys() as $tour)
    {
      $gt = new game_type(array('game_type_id'=>$tour->getValue('game_type_id'))) ;

      echo "\t<tr>\n";
      echo "\t<td>" . $tour->getValue('name'). "</td>\n";
      echo "\t<td>" . $gt->getValue('name') . "</td>\n";
      echo "\t<td>" . $tour->getValue('tourney_type') . "</td>\n";
      echo "\t<td>" . $tour->getValue('signup_start') . "</td>\n";
      echo "\t<td>" . $tour->getValue('signup_end') . "</td>\n";
      echo "\t<td>" . $tour->getValue('team_size') . "</td>\n";
      echo "\t<td>" . $tour->getValue('timelimit') . "</td>\n";
      echo "<td><a href='?a=manageTourney&amp;mode=edit&amp;tourney_id=" . $tour->getValue('tourney_id') . "'>Edit</a></td>";
      echo "<td><a href='?a=saveTourney&amp;mode=delete&amp;tourney_id=" . $tour->getValue('tourney_id') . "'>Delete</a></td>";	
      echo "\t</tr>\n";
    }

  echo "</table>\n";
  echo "<p><a href='?a=manageTourney'>Create Tournament</a>";
}
catch (Exception $e) {print $e;}
?>
