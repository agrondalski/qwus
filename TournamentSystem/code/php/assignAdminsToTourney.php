<?php

require 'includes.php';
require_once 'login.php';

try
{
  $tid = $_REQUEST['tourney_id'];

  $t = new tourney(array('tourney_id'=>$tid));
  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  if (!$p->isSuperAdmin() && $p->isTourneyAdmin($t->getValue('tourney_id')))
    {
      util::throwException('not authorized') ;
    }

  echo "<br>";
  echo "<table border=1 cellpadding=2 cellspacing=0>\n";
  echo "<th>Name</th><th>Action</th>";

  foreach ($t->getTourneyAdmins() as $p)
    {
      echo "\t<tr>\n";
      echo "\t<td>",$p->getValue('name'),"</td>\n";
      echo "<td><a href='?a=saveTourneyAdmin&amp;tourney_id=$tid&amp;mode=delete&amp;player_id=",$p->getValue('player_id'),"'>Delete</a></td>";
      echo "\t</tr>\n";
    }

  echo "</table><br>\n";

  echo "<form action='?a=saveTourneyAdmin' method=post>";
  echo "<input type='hidden' name='tourney_id' value='$tid'>";
  echo "<select name='player_id'>";

  foreach (player::getAllPlayers() as $play)
    {
      echo "<option value='" . $play->getValue('player_id') . "'>" . $play->getValue('name');
    }

  echo "</select>&nbsp;&nbsp;";
  echo "<input type='submit' value='Add Admin' name='B1' class='button'>";
  echo "<br>";
}
catch (Exception $e) {}

?>
