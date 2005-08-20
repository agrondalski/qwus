<?php
session_start();

if (!isset($_SESSION["inloggad"]))
{
	$_SESSION["inloggad"] = "nej";
	$a == false;
}

$a = ($_SESSION["inloggad"]=="ja") ? true:false;

if (!$a)
{
	if (!$_POST)
	{
		echo '
		<FORM METHOD="POST" ACTION="admin.php">
		<INPUT TYPE="password" name="password"><INPUT TYPE="submit" value="login">
		</FORM>
		';
	}
	else
	{
		if ($_POST["password"] == "qwus")
		{
			$_SESSION["inloggad"] = "ja";
		}
		header("location: admin.php");
	}
}
else
{
	$do = "";
	include("db.php");
	if (!empty($_GET["a"]))
	{
		$do = $_GET["a"];

		if ($do == "logout")
		{
			$_SESSION["inloggad"]="nej";
			header("location: .");
		}
		else if ($do == "delnews")
		{
			$id = $_GET["id"];
			mysql_query("DELETE FROM qwus_news WHERE id='$id'");
		}
	}
	if ($_POST)
	{
		$sub = $_POST["subject"];
		$wtr = $_POST["writer"];
		$txt = $_POST["txt"];
		$dte = date("Y-m-d");
		$nid = mysql_fetch_row(mysql_query("SELECT MAX(id) FROM qwus_news"));
		$nid = $nid[0]+1;
		mysql_query("INSERT INTO qwus_news VALUES('$nid','$sub','$dte','$txt','$wtr')");
	}
	echo '
		<LINK REL="stylesheet" HREF="css/default.css" TYPE="text/css">
		<A href="?a=logout">logout</A><BR>
		<BR>
		<B>Add news</B><BR>
		<FORM METHOD=POST ACTION="admin.php">
		subject: <INPUT TYPE="text" NAME="subject"><BR>
		writer:&nbsp; <INPUT TYPE="text" NAME="writer"><BR>
		<TEXTAREA NAME="txt" ROWS="8" COLS="35"></TEXTAREA><BR>
		<INPUT TYPE="submit" value="Add news">
		</FORM>
		<BR>
		<B>Delete news</B>&nbsp;&nbsp;&nbsp;<A href="admin.php">simple</A>&nbsp;|&nbsp;<A href="?a=detailed">detailed</A><BR><BR><TABLE cellspacing="0" cellpadding="3" width="480"><TR><TD><B>ID</B></TD><TD><B>SUBJECT</B></TD><TD><B>Date</B></TD><TD colspan="2"><B>Writer</B></TD></TR>';
		$news = mysql_query("SELECT * FROM qwus_news ORDER BY ID DESC");
		$color = Array("F0F0F0","F8F8F8");
		$i=0;
		while ($print = mysql_fetch_row($news))
		{
			echo '<TR><TD bgcolor="#' . $color[$i%2] . '">' . $print[0] . '</TD><TD bgcolor="#' . $color[$i%2] . '">' . $print[1] . '</TD><TD bgcolor="#' . $color[$i%2] . '">' . $print[2] . '</TD><TD bgcolor="#' . $color[$i%2] . '">' . $print[4] . '</TD><TD bgcolor="#' . $color[$i%2] . '"><A href="?a=delnews&amp;id=' . $print[0] . '">delete this post</A></TD></TR>';
			if ($do=="detailed")
			{
				echo '<TR><TD colspan="5" bgcolor="#' . $color[$i%2] . '">' . $print[3] . '<BR><BR></TD></TR>';
			}
			$i++;
		}
		echo '</TABLE>';
}
?>