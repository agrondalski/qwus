<?php
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
  echo "<td>Team Abbreviation:<br>(lowercase)</td><td>";
  echo "<input type='text' name='name_abbr' maxlength='50' value='' size='50'></td>";
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

  foreach (location::getCountryLocations() as $l)
    {
      $sel = "";
      if ($l->getValue('location_id') == $location_id)
	{
	  $sel = "selected";
	}

      $state_name = $l->getValue('state_name') ;
      if (!util::isNull($state_name))
	{
	  $state_name = ':' . $state_name ;
	}

      echo "<option value='" . $l->getValue('location_id') . "'" . $sel . ">" . $l->getValue('country_name') . $state_name ;
    }

  echo "</select></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td nowrap>";
  echo "Password:</td><td>";
  echo "<input type='text' name='password' value='' size='50'></td>";
  echo "</tr>";
  echo "<tr><td>&nbsp;</td><td><input type='submit' value='Submit' name='B1' class='button'>";
  echo "&nbsp;<input type='reset' value='Reset' name='B2' class='button'></td></tr></table>";
  echo "</p></font>";
  echo "</form>" ;

}
catch (Exception $e) {}
?>

