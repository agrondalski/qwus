<html>
<head>
<script language="JavaScript">
</script>
<title>NAQL</title>
<link rel="stylesheet" type="text/css" href="css/style.css" />
<style type="text/css">
<!--
a:link { text-decoration: none }
a:active { text-decoration: none }
a:visited { text-decoration: none }
a:hover {text-decoration: underline; color: #ff0000}-->
</style>
<base target="_self">
</head>
<?php
$tid = $_REQUEST['tourney_id'];
include 'tourneyLinks.php';
?>
<body bgcolor="#000000" text="#CCFFFF" link="#66FF99" vlink="#66FF99" alink="#00FF00">
<font size='2'>
<p>Create a division for tourney :</p>
<?php
echo "<form action='?a=saveDivision' method=post>";
echo "<input type='hidden' name='tourney_id' value='$tid'>";
?>
  <p>division name: <input type="text" name="division" size="20"></p>
  <p>
<input type="submit" value="Submit" name="B1" class='button'>
<input type="reset" value="Reset" name="B2" class='button'>
</p></font>
</form>

<?php
include 'listDivisions.php';
?>


</html>
