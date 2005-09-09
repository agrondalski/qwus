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
	}
      catch(Exception $e) {}
      header("location: ?" . $_SERVER['QUERY_STRING']);
    }
}
else
{
  print '<table border=0 width=100% cellspacing=0 cellpadding=0><tr>';
  print '<td>Welcome, ' . $_SESSION['username'] . '</td>';
  print '<td align=right><a href="?' . $_SERVER['QUERY_STRING'] . '&action=logout">Logout</a></td></tr></table>' ;

  $do = $_GET['action'] ;

  if ($do=="logout")
    {
      $_SESSION["loggedIn"]="no";
      $_SESSION["username"]="";

      header("location: ?" . str_replace('&action=logout', '', $_SERVER['QUERY_STRING'])) ;
    }
}
?>
