<?php
require 'includes.php';

$tid = $_REQUEST['tourney_id'];
$mode = $_REQUEST['mode'];
include 'tourneyLinks.php';
if ($mode == "edit") {
	echo "<b><p>Modify a Player:</b></p>";
	$player_id = $_REQUEST['player_id'];
	$p = new player(array('player_id'=>$player_id));

	$name=$p->getValue('name');
	$superadmin=$p->getValue('superAdmin');
	$location_id=$p->getValue('location_id');
	$loc = new location(array('location_id'=>$location_id));
	$loc_name = $loc->getValue('country_name').":".$loc->getValue('state_name');
	$password=$p->getValue('password');
	$hascolumn=$p->getValue('hasColumn');
}
else {
	echo "<p><b>Create a Player:</b></p>";
	$name="";
	$superadmin="";
	$location_id="";
	$loc_name = "";
	$password="";
	$hascolumn="";
} 
echo "<form action='?a=savePlayer' method=post>";
echo "<input type='hidden' name='tourney_id' value='$tid'>";
if ($mode == "edit") {
echo "<input type='hidden' name='player_id' value='$player_id'>";
echo "<input type='hidden' name='mode' value='edit'>";
}
echo "<table border=1 cellpadding=2 cellspacing=0>";
echo "<tr>";
echo "<td>Name:</td><td>";
echo "<input type='text' name='name' maxlength='50' value='",$name,"' size='50'></td>";
echo "</tr>";
echo "<tr>";
echo "<td>S.A.:</td><td>";
if ($superadmin == "1") {
	$check = "checked";
} else {
	$check = "";
}
echo "<input type='checkbox' name='superadmin' value='1' ",$check,"></td>";
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
echo "<td nowrap>";
if ($mode == "edit") {
	echo "New ";
}
echo "Password:</td><td>";
echo "<input type='text' name='password' value='' size='50'></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Has column?:</td><td>";
if ($hascolumn == "1") {
	$check = "checked";
} else {
	$check = "";
}
echo "<input type='checkbox' name='hascolumn' value='1' ",$check,"></td>";
echo "</tr>";
echo "<tr><td>&nbsp;</td><td><input type='submit' value='Submit' name='B1' class='button'>";
echo "&nbsp;<input type='reset' value='Reset' name='B2' class='button'></td></tr></table>";
echo "</p></font>";
?>
</form>
<?php
include 'listPlayers.php';
?>

