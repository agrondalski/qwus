<?php
session_start();

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
            <FORM METHOD="POST" ACTION="adminLogin.php">
            <INPUT TYPE="text" name="name"><INPUT TYPE="password" name="password"><INPUT TYPE="submit" value="login">
		</FORM>
		';
	}
}