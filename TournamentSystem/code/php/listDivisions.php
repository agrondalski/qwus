<?php

//listDivisions.php

// set your infomation.
$dbhost='localhost';
$dbusername='skel';
$dbuserpass='Fr3nzY!';
$dbname='dew';

$tid = $_REQUEST['tourney_id'];

// Connecting, selecting database
$link = mysql_connect($dbhost, $dbusername, $dbuserpass)
   or die('Could not connect: ' . mysql_error());
mysql_select_db($dbname) or die('Could not select database');

// Performing SQL query
$query = 'SELECT d.name as dname, t.name as tname FROM division d, tourney t WHERE d.tourney_id=t.tourney_id AND t.tourney_id=\''.$tid.'\'';
$result = mysql_query($query) or die('Query failed: ' . mysql_error());

// Printing results in HTML
echo "<table border=1 cellpadding=2 cellspacing=0>\n";
echo "<th>Tourney</th><th>Division Name</th>";
while ($line = mysql_fetch_array($result)) {
   echo "\t<tr>\n";
   echo "\t<td>",$line['tname'],"</td>\n";
   echo "\t<td>",$line['dname'],"</a></td>\n";
   echo "\t</tr>\n";
}
echo "</table>\n";

// Free resultset
mysql_free_result($result);

// Closing connection
mysql_close($link);
?>