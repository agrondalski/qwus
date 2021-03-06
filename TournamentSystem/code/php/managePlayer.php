<?php
require 'includes.php';
require_once 'login.php' ;

try
{
  try
  {
      $p = new player(array('player_id'=>$_SESSION['user_id']));
  }
  catch(Exception $e) {}

  $mode = $_REQUEST['mode'];

  if (!util::isLoggedInAsTeam() && !$p->isSuperAdmin() && ($_SESSION[user_id] != $_REQUEST['player_id'] || $mode!='edit'))
    {
      util::throwException('not authorized') ;
    }

  if ($mode == "edit")
    {
      echo "<b><p>Modify a Player:</b></p>";

      $player_id = $_REQUEST['player_id'];
      $play = new player(array('player_id'=>$player_id));

      $name=$play->getValue('name');
      $superadmin=$play->getValue('superAdmin');
      $location_id=$play->getValue('location_id');
      $loc = new location(array('location_id'=>$location_id));
      $password=$play->getValue('password');
      $hascolumn=$play->getValue('hasColumn');
    }

  else
    {
      echo "<p><b>Create a Player:</b></p>";

      $name="";
      $superadmin="";
      $location_id="";
      $password="";
      $hascolumn="";
    }

  echo "<form action='?a=savePlayer' method=post>";

  if ($mode == "edit")
    {
      echo "<input type='hidden' name='player_id' value='$player_id'>";
      echo "<input type='hidden' name='mode' value='edit'>";
    }

  echo "<table border=1 cellpadding=2 cellspacing=0>";
  echo "<tr>";
  echo "<td>Name:</td><td>";
  echo "<input type='text' name='name' maxlength='50' value='",$name,"' size='50'></td>";
  echo "</tr>";
  if (!util::isLoggedInAsTeam()) 
  {
	  echo "<tr>";
	  echo "<td>S.A.:</td><td>";

	  $check =  util::choose(($superadmin == "1"), "checked", "") ;
	  if (!$p->isSuperAdmin())
		{
		  $check .= ' disabled' ;
		}

	  echo "<input type='checkbox' name='superadmin' value='1' ",$check,"></td>";
	  echo "</tr>";
  }
  echo "<tr>";
  echo "<td>Location:</td><td>";
  echo "<select name='location_id'>";
 
  foreach (location::getAllLocations(array('country_name', SORT_ASC)) as $l)
    {
      $sel = "";
      if ($l->getValue('location_id') == $location_id)
	{
	  $sel = "selected";
	}

      echo "<option value='" . $l->getValue('location_id') . "'" . $sel . ">" . $l->getValue('country_name') ;
    }

  echo "</select></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td nowrap>";

  if ($mode == "edit")
    {
      echo "New ";
    }
  if (!util::isLoggedInAsTeam()) 
  {
	  echo "Password:</td><td>";
	  echo "<input type='password' name='password' value='' size='50'></td>";
	  echo "</tr>";
	  echo "<tr>";
	  echo "<td>Has column?:</td><td>";

	  $check = util::choose(($hascolumn == "1"), "checked", "") ;
	  if (!$p->isSuperAdmin())
		{
		  $check .= ' disabled' ;
		}

	  echo "<input type='checkbox' name='hascolumn' value='1' ",$check,"></td>";
	  echo "</tr>";
  }
  echo "<tr><td>&nbsp;</td><td><input type='submit' value='Submit' name='B1' class='button'>";
  echo "&nbsp;<input type='reset' value='Reset' name='B2' class='button'></td></tr></table>";
  echo "</p></font>";
  echo "</form>" ;
  if (!util::isLoggedInAsTeam()) 
  {
    include 'listPlayers.php';
  }
}
catch (Exception $e) {}
?>
