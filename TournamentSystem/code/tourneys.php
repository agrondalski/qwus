<html>
<head>
<script language="JavaScript">
</script>
<title>NAQL</title>
<style type="text/css">
<!--
a:link { text-decoration: none }
a:active { text-decoration: none }
a:visited { text-decoration: none }
a:hover {text-decoration: underline; color: #ff0000}-->
</style>
<base target="_self">
</head>
<body bgcolor="#000000" text="#CCFFFF" link="#66FF99" vlink="#66FF99" alink="#00FF00">
<?php

// set your infomation.
$dbhost='localhost';
$dbusername='skel';
$dbuserpass='Fr3nzY!';
$dbname='dew';

// Connecting, selecting database
$link = mysql_connect($dbhost, $dbusername, $dbuserpass)
   or die('Could not connect: ' . mysql_error());
//echo 'Connected successfully';
mysql_select_db($dbname) or die('Could not select database');
// Performing SQL query
$query = 'SELECT * FROM tourney';
$result = mysql_query($query) or die('Query failed: ' . mysql_error());
//$arr = mysql_fetch_array($result);
// Printing results in HTML
echo "<table border=1 cellpadding=2 cellspacing=0>\n";
echo "<th>tourney_id</th><th>Tourney Name</th>";
while ($line = mysql_fetch_array($result)) {
   echo "\t<tr>\n";
   echo "\t<td>",$line['tourney_id'],"</td>\n";
   echo "\t<td><a href='tourneyHome.php?tourney_id=",$line['tourney_id'],"'>",$line['name'],"</a></td>\n";
  // foreach ($line as $col_value) {
  //     echo "\t\t<td>$col_value</td>\n";
  // }
   echo "\t</tr>\n";
}
echo "</table>\n";

// Free resultset
mysql_free_result($result);

// Closing connection
mysql_close($link);
echo "<br><br>:o</html>";

?>

