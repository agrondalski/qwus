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

  $mode = $_REQUEST['mode'];

  if ($mode == "edit")
    {
      echo "<b><p>Modify a Map:</b></p>";

      $map_id = $_REQUEST['map_id'];
      $m = new map(array('map_id'=>$map_id)) ;

      $map_name = $m->getValue('map_name');
      $map_abbr = $m->getValue('map_abbr');
      $gt_id    = $m->getValue('game_type_id');
    }

  else
    {
      echo "<p><b>Create a Player:</b></p>";

      $map_name = '' ;
      $map_abbr = '' ;
      $gt_id    = '' ;
    }

  echo "<form action='?a=saveMap' method=post>";

  if ($mode == "edit")
    {
      echo "<input type='hidden' name='map_id' value='$map_id'>";
      echo "<input type='hidden' name='mode' value='edit'>";
    }

  echo "<table border=1 cellpadding=2 cellspacing=0>";
  echo "<tr>";
  echo "<td>Map Name:</td><td>";
  echo "<input type='text' name='map_name' maxlength='50' value='" . $map_name . "' size='50'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>Map Abbrevation:</td><td>";
  echo "<input type='text' name='map_abbr' maxlength='10' value='" . $map_abbr . "' size='10'></td>";
  echo "</tr>";

  echo "<td>Game Type:</td><td>";
  echo "<select name='game_type_id'>";
 
  foreach (game_type::getAllGameTypes() as $gt)
    {
      $sel = "";
      if ($gt->getValue('game_type_id') == $gt_id)
	{
	  $sel = "selected";
	}

      echo "<option value='" . $gt->getValue('game_type_id') . "'" . $sel . ">" . $gt->getValue('name') . $state_name ;
    }

  echo "</select></td>";
  echo "</tr>";

  echo "<tr><td>&nbsp;</td><td><input type='submit' value='Submit' name='B1' class='button'>";
  echo "&nbsp;<input type='reset' value='Reset' name='B2' class='button'></td></tr></table>";
  echo "</p></font>";
  echo "</form>" ;

  include 'listMaps.php';
}
catch (Exception $e) {}
?>
