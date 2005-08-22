<html>
<head>
<script language="JavaScript">
</script>
<title>NAQL</title>
<style type="text/css">
<!---->
a:link { text-decoration: none }
a:active { text-decoration: none }
a:visited { text-decoration: none }
a:hover {text-decoration: underline; color: #ff0000}

</style>
<base target="_self">
</head>
<body bgcolor="#000000" text="#CCFFFF" link="#66FF99" vlink="#66FF99" alink="#00FF00">
<p>Create a division for tourney :</p>
<?php
$tid = $_POST['tourney_id'];
$div = $_POST['division'];
echo "tourney_id = $tid <br>";
echo "division = $div   <br>";

// set your infomation.
$dbhost='localhost';
$dbusername='skel';
$dbuserpass='Fr3nzY!';
$dbname='dew';

// Connecting, selecting database
$link = mysql_connect($dbhost, $dbusername, $dbuserpass)
   or die('Could not connect: ' . mysql_error());
mysql_select_db($dbname) or die('Could not select database');

// Performing SQL query
$fields = array("tourney_id","name");
$values = array($tid, $div);

$query = ez_insert("division", $fields, $values);
$result = mysql_query($query) or die('Query failed: ' . mysql_error());

// Closing connection
mysql_close($link);

include 'listDivisions.php';

function ez_insert($table_name, $field, $values)
{
	$sql = "insert into `$table_name` (";
	$i = 0;
	while ( $i < count($field) )
	{
		//$sql = $sql . "'" . $field[$i] . "'";
		$sql = $sql . $field[$i];
		$val_part = $val_part . "'" . $values[$i] . "'";
		if ( ($i + 1) != count($field) )
		{
			$val_part = $val_part . ", ";
			$sql = $sql . ", ";
		}
		$i++;
	}
	$sql = $sql . ") values (" . $val_part . ")";

	return $sql;
}

?>

:o
</html>
