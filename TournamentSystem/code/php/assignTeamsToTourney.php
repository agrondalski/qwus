<?php

require 'includes.php';
require_once 'login.php';

try
{
  $tid = $_REQUEST['tourney_id'];

  $t = new tourney(array('tourney_id'=>$tid));
  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  if (!$p->isSuperAdmin() && !$p->isTourneyAdmin($t->getValue('tourney_id')))
    {
      util::throwException('not authorized') ;
    }

  echo "<br>";
  echo "<table border=1 cellpadding=2 cellspacing=0>\n";
  echo "<th>Name</th><th>Abbr</th><th>Email</th><th>IRC</th><th>Location</th><th>Action</th>";

  foreach ($t->getTeams() as $team)
    {
      $loc = new location(array('location_id'=>$team->getValue('location_id')));

      echo "\t<tr>\n";
      echo "\t<td>" . $team->getValue('name') . "</td>\n";
      echo "\t<td>" . $team->getValue('name_abbr') . "</td>\n";
      echo "\t<td>" . $team->getValue('email') . "</td>\n";
      echo "\t<td>" . $team->getValue('irc_channel') . "</td>\n";
      echo "\t<td>" . $loc->getValue('country_name') . "</td>\n";
      echo "<td><a href='?a=saveTourneyTeam&amp;tourney_id=$tid&amp;mode=delete&amp;team_id=" . $team->getValue('team_id') . "'>Delete</a></td>";
      echo "\t</tr>\n";
    }

  echo "</table>\n";
  echo "<br>" ;

  echo "<form action='?a=saveTourneyTeam' method=post>";
  echo "<input type='hidden' name='tourney_id' value='$tid'>";
  echo "<select name='team_id'>";

  foreach ($t->getUnsignedUpTeams(array('name', SORT_ASC, SORT_STRING)) as $team)
    {
      echo "<option value='" . $team->getValue('team_id') . "'>" . $team->getValue('name');
    }
  echo "</select>&nbsp;&nbsp;";
  echo "<input type='submit' value='Add Team' name='B1' class='button'>";
  echo "<br></form>";
}
catch (Exception $e) {}

?>
