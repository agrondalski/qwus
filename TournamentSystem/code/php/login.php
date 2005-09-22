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
           <form method="post" action="?' . $_SERVER['QUERY_STRING'] . '">
           <table cellspacing="2" cellpadding="2">

            <TR>
              <TD><B>Team</B>:</TD>
              <TD><select name="team_id">' ;

      $teamlist = team::getAllTeams(array('name', SORT_ASC));

      foreach ($teamlist as $t)
	{
      	  echo "<option value='" . $t->getValue('team_id') . "'>" . $t->getValue('name') ;
	}

      echo '  </select></TD>
            </TR>
            <TR>
               <TD><B>Password</B>:</TD>
               <TD><INPUT TYPE="password" name="password"></TD>
               <TD><INPUT TYPE="submit" value="Team Login"></TD>
            </TR>
            </TABLE>
            </FORM>
    
            <BR><BR>' ;

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
      if (!util::isNUll($_POST["username"]))
	{
	  try
	    {
	      $p = new player(array('name'=>$_POST["username"])) ;      
	      if ($p->passwordMatches($_POST["password"]))
		{
		  $_SESSION["loggedIn"]   = "yes";
		  $_SESSION["username"]   = $_POST["username"] ;
		  $_SESSION["user_id"]  = $p->getValue('player_id') ;
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
	}

      if (!util::isNUll($_POST["team_id"]))
	{
	  try
	    {
	      $t = new team(array('team_id'=>$_POST["team_id"])) ;      
	      if ($t->passwordMatches($_POST["password"]))
		{
		  $_SESSION["loggedIn"] = "yes";
		  $_SESSION["teamname"] = $t->getValue('name') ;
		  $_SESSION["team_id"]  = $_POST["team_id"] ;
		}
	      else
		{
		  util::throwException('invalid password') ;
		}
	    }
	  catch(Exception $e)
	    {
	      $l = new log_entry(array('type'=>'LOGIN', 'str'=>$_POST["team_id"], 'logged_ip'=>$_SERVER['REMOTE_ADDR'], 'log_date'=>util::curdate(), 'log_time'=>util::curtime()));
	    }
	}

      header("location: ?" . $_SERVER['QUERY_STRING']);
    }
}
else
{
  echo '<table border=0 width=100% cellspacing=0 cellpadding=0>' ;
  echo '<tr>';

  if (!util::isNull($_SESSION['username']))
    {
      echo '<td colspan=2>Welcome, ' . $_SESSION['username'] . '</td></tr><tr>';
    }
  elseif (!util::isNull($_SESSION['teamname']))
    {
      echo '<td colspan=2>Welcome, ' . $_SESSION['teamname'] . '</td></tr><tr>';
    }

  if (!util::isNull($_SESSION['tourney_id']) || !util::isNull($_REQUEST['tourney_id']))
    {
      echo "<td align=left><a href='?a=adminHome'>Admin Home</a>&nbsp;<b>&gt;</b>";
      echo "&nbsp;<a href='?a=tourneyHome&amp;tourney_id=" . util::nvl($_SESSION['tourney_id'], $_REQUEST['tourney_id']) . "'>Tourney Home</a></td>";
    }
  else
    {
      echo "<td align=left><a href='?a=adminHome'>Admin Home</a></td>";
    }

  echo '<td align=right><a href="?' . $_SERVER['QUERY_STRING'] . '&action=logout">Logout</a></td>' ;
  echo '</tr></table><hr>' ;

  $do = $_GET['action'] ;

  if ($do=="logout")
    {
      $_SESSION["loggedIn"] = "no";
      $_SESSION["username"] = null;
      $_SESSION["user_id"]  = null ;
      $_SESSION["teamname"] = null ;
      $_SESSION["team_id"]  = null ;

      header("location: ?a=adminHome") ;
    }
}
?>
