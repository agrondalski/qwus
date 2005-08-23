<?php

//listMaps.php

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
$query = 'SELECT m.* from maps m, tourney_maps tm WHERE m.map_id=tm.map_id AND tm.tourney_id=\''.$tid.'\'';
$result = mysql_query($query) or die('Query failed: ' . mysql_error());

// Printing results in HTML
echo "<table border=1 cellpadding=2 cellspacing=0>\n";
echo "<th>Map Abbr</th><th>Map Name</th>";
while ($line = mysql_fetch_array($result)) {
   echo "\t<tr>\n";
   echo "\t<td>",$line['map_abbr'],"</td>\n";
   echo "\t<td>",$line['map_name'],"</a></td>\n";
   echo "\t</tr>\n";
}
echo "</table>\n";

// Free resultset
mysql_free_result($result);

// Closing connection
mysql_close($link);
?>