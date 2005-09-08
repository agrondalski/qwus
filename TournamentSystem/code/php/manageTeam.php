<?php
require 'includes.php';

$tid = $_REQUEST['tourney_id'];
$mode = $_REQUEST['mode'];
include 'tourneyLinks.php';
if ($mode == "edit") {
	echo "<b><p>Modify a Team:</b></p>";
	$team_id = $_REQUEST['team_id'];
	$tm = new team(array('team_id'=>$team_id));

	$name=$tm->getValue('name');
	$email=$tm->getValue('email');
	$irc_channel=$tm->getValue('irc_channel');
	$location_id=$tm->getValue('location_id');
	$loc = new location(array('location_id'=>$location_id));
	$loc_name = $loc->getValue('country_name').":".$loc->getValue('state_name');
	$password=$tm->getValue('password');
	$approved=$tm->getValue('approved');
}
else {
	echo "<p><b>Create a Team:</b></p>";
	$name="";
	$email="";
	$irc_channel="";
	$location_id="";
	$loc_name = "";
	$password="";
	$approved="";
} 
echo "<form action='?a=saveTeam' method=post>";
echo "<input type='hidden' name='tourney_id' value='$tid'>";
if ($mode == "edit") {
echo "<input type='hidden' name='team_id' value='$team_id'>";
echo "<input type='hidden' name='mode' value='edit'>";
}
echo "<table border=1 cellpadding=2 cellspacing=0>";
echo "<tr>";
echo "<td>Name:</td><td>";
echo "<input type='text' name='name' maxlength='50' value='",$name,"' size='50'></td>";
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
$locs = location::getAllLocations();
foreach ($locs as $l) {
	$sel = "";
	if ($l->getValue('location_id') == $location_id) {
		$sel = "selected";
	}
	echo "<option value='",$l->getValue('location_id'),"'",$sel,">",$l->getValue('country_name'),":",$l->getValue('state_name');
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Password:</td><td>";
echo "<input type='text' name='password' value='",$password,"' size='50'></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Approved:</td><td>";
if ($approved == "1") {
	$check = "checked";
} else {
	$check = "";
}
echo "<input type='checkbox' name='approved' value='1' ",$check,"></td>";
echo "</tr>";
echo "<tr><td>&nbsp;</td><td><input type='submit' value='Submit' name='B1' class='button'>";
echo "&nbsp;<input type='reset' value='Reset' name='B2' class='button'></td></tr></table>";
echo "</p></font>";
?>
</form>
<?php
include 'listTeams.php';
?>

