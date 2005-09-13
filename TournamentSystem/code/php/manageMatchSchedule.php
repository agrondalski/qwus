<?php
require 'includes.php';

$tid = $_REQUEST['tourney_id'];
$division_id = $_REQUEST['division_id'];
$mode = $_REQUEST['mode'];
include 'tourneyLinks.php';
if ($mode == "edit") {
	echo "<b><p>Modify:</b></p>";
	$schedule_id = $_REQUEST['schedule_id'];

	// Create new 
	$ms = new match_schedule(array('schedule_id'=>$schedule_id));

	$name=$ms->getValue('name');
	$deadline=$ms->getValue('deadline');
}
else {
	echo "<p><b>Create:</b></p>";
	$name="";
	$deadline="";
} 
echo "<form action='?a=saveMatchSchedule' method=post>";
echo "<input type='hidden' name='tourney_id' value='$tid'>";
echo "<input type='hidden' name='division_id' value='$division_id'>";
if ($mode == "edit") {
	echo "<input type='hidden' name='schedule_id' value='$schedule_id'>";
	echo "<input type='hidden' name='mode' value='edit'>";
}
echo "<table border=1 cellpadding=2 cellspacing=0>";
echo "<tr>";
echo "<td>Week Name:</td><td>";
echo "<input type='text' name='name' maxlength='50' value='",$name,"' size='20'></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Deadline:</td><td>";
echo "<input type='text' name='deadline' value='",$deadline,"' size='20'></td>";
echo "</tr>";
echo "<tr><td>&nbsp;</td><td><input type='submit' value='Submit' name='B1' class='button'>";
echo "&nbsp;<input type='reset' value='Reset' name='B2' class='button'></td></tr></table>";
echo "</p></font>";
?>
</form>
<?php
include 'listMatchSchedule.php';
?>

