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
      require("php/includes.php") ;

      try
	{
	  $p = new player(array('name'=>$_POST["username"])) ;      
	  if ($p->passwordMatches($_POST["password"]))
	    {
	      $_SESSION["loggedin"] = "yes";
	      $_SESSION["username"] = $_POST["username"] ;
	    }
	}
      catch(Exception $e) {}

      header("location: admin.php");
    }
}
else
{
  $do = "" ;
  require("php/includes.php") ;
  
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
	  $n = new news(array('news_id'=>$_GET["id"])) ;
	  $n->delete() ;
	}
    }
  if ($_POST)
    {
      $sub = $_POST["subject"];
      $wtr = $_SESSION["username"];
      $txt = $_POST["txt"];
      $dte = date("Y-m-d");

      $writer = new player(array('name'=>$wtr)) ;
      $n = new news(array('writer_id'=>$writer->getValue("player_id"), 'subject'=>$sub, 'news_date'=>$dte, 'text'=>$txt)) ;
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
  
  $news = news::getNews(array('order'=>'news_id desc')) ;
  $color = Array("F0F0F0","F8F8F8");

  for ($i=0; $i<count($news); $i++)
    {
      echo '<TR><TD bgcolor="#' . $color[$i%2] . '">' . $news[$i]->getValue("news_id") . '</TD><TD bgcolor="#' . $color[$i%2] . '">' . $news[$i]->getValue("subject") . '</TD><TD bgcolor="#' . $color[$i%2] . '">' . $news[$i]->getValue("news_date") . '</TD><TD bgcolor="#' . $color[$i%2] . '">' . $news[$i]->getWriter()->getValue("name") . '</TD><TD bgcolor="#' . $color[$i%2] . '"><A href="?a=delnews&amp;id=' . $news[$i]->getValue("news_id") . '">delete this post</A></TD></TR>';
      if ($do=="detailed")
	{
	  echo '<TR><TD colspan="5" bgcolor="#' . $color[$i%2] . '">' . $news[$i]->getValue("text") . '<BR><BR></TD></TR>';
	}
    }
  echo '</TABLE>';
}
?>
