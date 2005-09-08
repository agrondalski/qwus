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