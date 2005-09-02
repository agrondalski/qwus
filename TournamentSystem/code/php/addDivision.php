<?php
require 'includes.php';

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
<table border=1 cellpadding=2 cellspacing=0>
<tr>
<td>division name:</td><td><input type="text" name="name" maxlength="50" size="20"></td>
</tr>
<tr>
<td>Max Teams:</td><td><input type="text" name="max_teams" size="4"></td>
</tr>
<tr>
<td># of Games:</td><td><input type="text" name="num_games" size="4"></td>
</tr>
<tr>
<td>Playoff Spots:</td><td><input type="text" name="playoff_spots" size="4"></td>
</tr>
<tr>
<td>Elim Losses:</td><td><input type="text" name="elim_losses" size="4"></td>
</tr>
<tr><td><input type="submit" value="Submit" name="B1" class='button'></td><td>
<input type="reset" value="Reset" name="B2" class='button'></td></tr></table>
</p></font>
</form>

<?php
include 'listDivisions.php';
?>

