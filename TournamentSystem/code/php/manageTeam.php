<?php
require 'includes.php';
require_once 'login.php' ;

try
{
  $mode = $_REQUEST['mode'];

  if (util::isLoggedInAsPlayer())
    {
      $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

      if (!$p->isSuperAdmin())
	{
	  util::throwException('not authorized') ;
	}
    }

  elseif (util::isLoggedInAsTeam())
    {
      $tm = new team(array('team_id'=>$_SESSION['team_id'])) ;

      if ($_SESSION[team_id] != $_REQUEST['team_id'] || $mode!='edit')
	{
	  util::throwException('not authorized') ;
	}
    }

  if ($mode == "edit")
    {
      echo "<b><p>Modify a Team:</b></p>";

      $team_id = $_REQUEST['team_id'];
      $tm = new team(array('team_id'=>$team_id));
      
      $name=$tm->getValue('name');
      $name_abbr=$tm->getValue('name_abbr');
      $email=$tm->getValue('email');
      $irc_channel=$tm->getValue('irc_channel');
      $location_id=$tm->getValue('location_id');
      $loc = new location(array('location_id'=>$location_id));
      $password=$tm->getValue('password');
      $approved=$tm->getValue('approved');
    }

  else
    {
      echo "<p><b>Create a Team:</b></p>";

      $name="";
      $name_abbr="";
      $email="";
      $irc_channel="";
      $location_id="";
      $password="";
      $approved="";
    } 

  echo "<form action='?a=saveTeam' method=post>";

  if ($mode == "edit")
    {
      echo "<input type='hidden' name='team_id' value='$team_id'>";
      echo "<input type='hidden' name='mode' value='edit'>";
    }

  echo "<table border=1 cellpadding=2 cellspacing=0>";
  echo "<tr>";
  echo "<td>Name:</td><td>";
  echo "<input type='text' name='name' maxlength='50' value='",$name,"' size='50'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>Abbr:</td><td>";
  echo "<input type='text' name='name_abbr' maxlength='4' value='",$name_abbr,"' size='6'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>Email:</td><td>";
  echo "<input type='text' name='email' value='",$email,"' size='50'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td nowrap>IRC Channel:</td><td>";
  echo "<input type='text' name='irc_channel' value='",$irc_channel,"' size='50'></td>";
  echo "</tr>";
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

      echo "<option value='" . $l->getValue('location_id') . "'" . $sel . ">" . $l->getValue('country_name')  ;
    }

  echo "</select></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td nowrap>";

  if ($mode == "edit")
    {
      echo "New ";
    }

  echo "Password:</td><td>";
  echo "<input type='password' name='password' value='' size='50'></td>";
  echo "</tr>";
  echo "<tr>";

  if (!util::isLoggedInAsTeam())
    {
      echo "<td>Approved:</td><td>";

      $check = util::choose(($approved == "1"), 'checked', '') ;

      echo "<input type='checkbox' name='approved' value='1' ",$check,"></td>";
      echo "</tr>";
    }

  echo "<tr><td>&nbsp;</td><td><input type='submit' value='Submit' name='B1' class='button'>";
  echo "&nbsp;<input type='reset' value='Reset' name='B2' class='button'></td></tr></table>";
  echo "</p></font>";

  echo "</form>" ;

  include 'listTeams.php';
}
catch (Exception $e) {}
?>

