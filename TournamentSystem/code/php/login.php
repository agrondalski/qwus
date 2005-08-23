<?php
session_start();
include("dbConnect.php");

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
	    
            <BR><BR>
            
            <FORM METHOD="POST" ACTION="login.php">
            <table cellspacing="2" cellpadding="2">
            <TR>
              <TD><B>Admin</B>:</TD>
              <TD><INPUT TYPE="text" name="admin"></TD>
            </TR>
            <TR>
              <TD><B>Password</B>:</TD> 
              <TD><INPUT TYPE="password" name="password"></TD>
              <TD><INPUT TYPE="submit" value="Admin Login"></TD>
            </TR>
            </TABLE>
            </FORM>
            ';
	}
}