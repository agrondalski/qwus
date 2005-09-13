<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];
$division_id = $_REQUEST['division_id'];

//$t = new tourney(array('tourney_id'=>$tid));
$div = new division(array('division_id'=>$division_id));
include 'tourneyLinks.php';
echo "<br>";
// Printing results in HTML
echo "<table border=1 cellpadding=2 cellspacing=0>\n";
echo "<th>Name</th><th>Deadline</th><th colspan=2>Actions</th>";
foreach ($div->getMatchSchedule() as $ms) {
	echo "\t<tr>\n";
	echo "\t<td>",$ms->getValue('name'),"</td>\n";
	echo "\t<td>",$ms->getValue('deadline'),"</td>\n";
	echo "<td><a href='?a=manageMatchSchedule&amp;tourney_id=$tid&amp;mode=edit&amp;division_id=",$div->getValue('division_id'),"&amp;schedule_id=",$ms->getValue('schedule_id'),"'>";
	echo "Edit</a></td>";
	echo "<td><a href='?a=saveMatchSchedule&amp;tourney_id=$tid&amp;mode=delete&amp;division_id=",$div->getValue('division_id'),"&amp;schedule_id=",$ms->getValue('schedule_id'),"'>";
	echo "Delete</a></td>";
	echo "\t</tr>\n";
}
echo "</table>\n";
echo "<p>";
echo "<a href='?a=manageSchedule&amp;tourney_id=$tid&amp;division_id=",$div->getValue('division_id'),"'>";
echo "Back to Schedule</a>&nbsp;<b>&gt;&nbsp;</b>";
echo "<a href='?a=manageMatchSchedule&amp;tourney_id=$tid&amp;division_id=",$div->getValue('division_id'),"'>";
echo "Create New</a></p>";
?>
