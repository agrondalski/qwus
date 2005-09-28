<?php
session_start() ;
require 'includes.php';

try
{
  echo "<p><b>Signup a Team:</b></p>";
  echo "<form action='?a=signupTeamSave' method=post>";
  echo "<table border=1 cellpadding=2 cellspacing=0>";
  echo "<tr>";
  echo "<td>Team Name:</td><td>";
  echo "<input type='text' name='name' maxlength='50' value='' size='50'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>Team Abbreviation:<br>(must match team in qw)</td><td>";
  echo "<input type='text' name='name_abbr' maxlength='4' value='' size='6'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>Team Email:</td><td>";
  echo "<input type='text' name='email' value='' size='50'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td nowrap>IRC Network & Chan:</td><td>";
  echo "<input type='text' name='irc_channel' value='' size='50'></td>";
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

      echo "<option value='" . $l->getValue('location_id') . "'" . $sel . ">" . $l->getValue('country_name') ;
    }

  echo "</select></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td nowrap>";
  echo "Password:</td><td>";
  echo "<input type='password' name='password' value='' size='50'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td nowrap>";
  echo "Image Password:</td><td>";
  echo "<input type='password' name='image_password' value='' size='50'></td>";
  echo "</tr>";
  echo "<tr><td>&nbsp;</td><td><input type='submit' value='Submit' name='B1' class='button'>";
  echo "&nbsp;<input type='reset' value='Reset' name='B2' class='button'></td></tr></table>";
  echo "</p></font>";
  echo "</form>" ;

  $_SESSION['validate_pw'] = util::generateRandomStr(7);
  echo "<center><img src='php/signupPassword.php' width=300 height=100></center>" ;
}
catch (Exception $e) {}
?>

