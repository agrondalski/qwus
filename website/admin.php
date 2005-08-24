<?php
session_start();

if (!isset($_SESSION["loggedin"]))
{
	$_SESSION["loggedin"] = "no";
	$a == false;
}

$a = ($_SESSION["loggedin"]=="yes") ? true:false;

if (!$a)
{
	if (!$_POST)
	{
		echo '
		<FORM METHOD="POST" ACTION="admin.php">
		Name <INPUT TYPE="test" name="username">
                Password <INPUT TYPE="password" name="password"><INPUT TYPE="submit" value="login">
		</FORM>
		';
	}
	else
	{
                require("db.php") ;
		$sql_text = sprintf("select count(*) from player where name='%s' and superAdmin=true and password=md5('%s')",
                                      mysql_real_escape_string($_POST["username"]),  mysql_real_escape_string($_POST["password"])) ;

	  	$found_pw = mysql_fetch_row(mysql_query($sql_text)) ;

		if ($found_pw[0]>0)
		{
			$_SESSION["loggedin"] = "yes";
                        $_SESSION["username"] = $_POST["username"] ;
		}
		header("location: admin.php");
	}
}

else
{
  $do = "" ;
  require("db.php") ;
  if (!empty($_GET["a"]))
	{
		$do = $_GET["a"];

		if ($do == "logout")
		{
			$_SESSION["loggedin"]="no";
			header("location: .");
		}
		else if ($do == "delnews")
		{
			$id = $_GET["id"];
			mysql_query("delete from news where news_id=$id");
		}
	}
	if ($_POST)
	{
		$sub = $_POST["subject"];
		$wtr = $_SESSION["username"];
		$txt = $_POST["txt"];
		$dte = date("Y-m-d");

                $sql_text = sprintf("select player_id from player where name='%s'", mysql_real_escape_string($wtr)) ;
                $pid = mysql_fetch_row(mysql_query($sql_text)) ;

                $sql_text = sprintf("insert into news(writer_id, subject, news_date, text) values($pid[0], '%s', '$dte', '%s')",
                                    mysql_real_escape_string($sub), mysql_real_escape_string($txt)) ;
                mysql_query($sql_text);
	}
	echo '
		<LINK REL="stylesheet" HREF="css/default.css" TYPE="text/css">
		<A href="?a=logout">logout</A><BR>
		<BR>
		<B>Add news</B><BR>
		<FORM METHOD=POST ACTION="admin.php">
		subject: <INPUT TYPE="text" NAME="subject"><BR>
		<TEXTAREA NAME="txt" ROWS="8" COLS="35"></TEXTAREA><BR>
		<INPUT TYPE="submit" value="Add news">
		</FORM>
		<BR>
		<B>Delete news</B>&nbsp;&nbsp;&nbsp;<A href="admin.php">simple</A>&nbsp;|&nbsp;<A href="?a=detailed">detailed</A><BR><BR><TABLE cellspacing="0" cellpadding="3" width="480"><TR><TD><B>ID</B></TD><TD><B>SUBJECT</B></TD><TD><B>Date</B></TD><TD colspan="2"><B>Writer</B></TD></TR>';

		$news = mysql_query("select n.news_id, n.subject, n.news_date, n.text, p.name from news n, player p where n.writer_id=p.player_id order by news_id desc");
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