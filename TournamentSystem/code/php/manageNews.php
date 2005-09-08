<?php
require 'includes.php';

$tid = $_REQUEST['tourney_id'];
$mode = $_REQUEST['mode'];
$p = new player(array('name'=>$_SESSION["username"]));

include 'tourneyLinks.php';

if ($mode == "edit") {
	echo "<b><p>Modify a news item:</b></p>";
	$nid = $_REQUEST['nid'];
	// Create the new division
	$news = new news(array('news_id'=>$nid));

	$writer_id=$news->getValue('writer_id');
	$subject=$news->getValue('subject');
	$news_date=$news->getValue('news_date');
	$text=$news->getValue('text');
}
else {
	echo "<p><b>Create a news item:</b></p>";
	$writer_id=$p->getValue('player_id');
	$subject="";
	$news_date="";
	$text="";
} 
echo "<form action='?a=saveNews' method=post>";
echo "<input type='hidden' name='tourney_id' value='$tid'>";

if ($mode == "edit") {
	echo "<input type='hidden' name='nid' value='$nid'>";
	echo "<input type='hidden' name='mode' value='edit'>";
}
echo "<table border=1 cellpadding=2 cellspacing=0>";
echo "<tr>";
echo "<td>author name:</td><td>";
echo "<input type='text' disabled name='name' maxlength='50' value='",$name,"' size='20'></td>";
echo "<input type='hidden' name='writer_id' value='$writer_id'>";
echo "</tr>";
echo "<tr>";
echo "<td>Subject:</td><td>";
echo "<input type='text' name='max_teams' value='",$max_teams,"' size='4'></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Text:</td><td>";
echo "<input type='text' name='num_games' value='",$num_games,"' size='4'></td>";
echo "</tr>";
echo "<tr><td>&nbsp;</td><td><input type='submit' value='Submit' name='B1' class='button'>";
echo "&nbsp;<input type='reset' value='Reset' name='B2' class='button'></td></tr></table>";
echo "</p></font>";
?>
</form>
<?php
include 'listNews.php';
?>

