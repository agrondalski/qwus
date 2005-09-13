<?php
session_start();
include("includes.php");

if (!isset($_SESSION["loggedIn"]))
{
      $_SESSION["loggedIn"] = "no";
      $displayLogin == true;
}

$displayLogin = ($_SESSION["loggedIn"] == "yes") ? false : true;

if ($displayLogin)
{
  if (!$_POST)
    {
      /*
      $tourney_id = $_GET["tourney_id"];
      $q = mysql_query("SELECT team_id from tourney_info where tourney_id = $tourney_id");
      echo mysql_result($q,0,"team_id");
      echo '
            <FORM METHOD="POST" ACTION="login.php">
            <table cellspacing="2" cellpadding="2">
            <TR>
               <TD><B>Team</B>:</TD>
               <TD>DROPDOWN HERE</TD>
            </TR>
            <TR></TR>
            <TR>
               <TD><B>Password</B>:</TD>
               <TD><INPUT TYPE="password" name="password"></TD>
               <TD><INPUT TYPE="submit" value="Team Login"></TD>
            </TR>
            </TABLE>
            </FORM>
    
            <BR><BR>' ;
      */

      echo '
            <FORM METHOD="POST" ACTION="?' . $_SERVER['QUERY_STRING'] . '">
            <table cellspacing="2" cellpadding="2">
            <TR>
              <TD><B>Username</B>:</TD>
              <TD><INPUT TYPE="text" name="username"></TD>
            </TR>
            <TR>
              <TD><B>Password</B>:</TD> 
              <TD><INPUT TYPE="password" name="password"></TD>
              <TD><INPUT TYPE="submit" value="Login"></TD>
            </TR>
            </TABLE>
            </FORM>
            ';

      util::throwException("Login Screen") ;
    }
  else
    {
      try
	{
	  $p = new player(array('name'=>$_POST["username"])) ;      
	  if ($p->passwordMatches($_POST["password"]))
	    {
	      $_SESSION["loggedIn"] = "yes";
	      $_SESSION["username"] = $_POST["username"] ;
	    }
	  else
	    {
	      util::throwException('invalid password') ;
	    }
	}
      catch(Exception $e)
	{
	  $l = new log_entry(array('type'=>'LOGIN', 'str'=>$_POST["username"], 'logged_ip'=>$_SERVER['REMOTE_ADDR'], 'log_date'=>util::curdate(), 'log_time'=>util::curtime()));
	}
      header("location: ?" . $_SERVER['QUERY_STRING']);
    }
}
else
{
  print '<table border=0 width=100% cellspacing=0 cellpadding=0>' ;
  print '<tr>';
  print '<td>Welcome, ' . $_SESSION['username'] . '</td>';

  if (!util::isNull($_SESSION['tourney_id']) || !util::isNull($_REQUEST['tourney_id']))
    {
      print "<td><a href='?a=adminHome'>Admin Home</a></td>";
      print "<td><a href='?a=tourneyHome&amp;tourney_id=" . util::nvl($_SESSION['tourney_id'], $_REQUEST['tourney_id']) . "'>Tourney Home</a></td>";
    }
  else
    {
      print "<td><a href='?a=adminHome'>Admin Home</a></td>";
    }

  print '<td align=right><a href="?' . $_SERVER['QUERY_STRING'] . '&action=logout">Logout</a></td>' ;
  print '</tr></table><hr>' ;

  $do = $_GET['action'] ;

  if ($do=="logout")
    {
      $_SESSION["loggedIn"]="no";
      $_SESSION["username"]="";

      header("location: ?" . str_replace('&action=logout', '', $_SERVER['QUERY_STRING'])) ;
    }
}
?>
