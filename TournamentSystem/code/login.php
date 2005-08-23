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
 	    echo '
            <FORM METHOD="POST" ACTION="login.php">
            <B>Team</B>: DROPDOWN HERE
            <BR>
            <B>Password</B>: <INPUT TYPE="password" name="password">
            <BR>
            <INPUT TYPE="submit" value="Team Login">
	    </FORM>
	    
            <BR><BR>
            
            <FORM METHOD="POST" ACTION="login.php">
            <B>Admin</B>: <INPUT TYPE="text" name="admin">
            <BR>
            <B>Password</B>: <INPUT TYPE="password" name="password">
            <BR>
            <INPUT TYPE="submit" value="Admin Login">
            </FORM>
            ';
	}
}