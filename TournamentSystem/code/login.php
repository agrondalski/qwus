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
            <FORM METHOD="POST" ACTION="login.php">
            <TD>Team: INSERT COOL DROPDOWN HERE</TD>
            <TD>Password: <INPUT TYPE="password" name="password"></TD>
            <TD><INPUT TYPE="submit" value="teamLogin"></TD>
		</FORM>
		<BR><BR>
            <FORM METHOD="POST" ACTION="login.php">
            <TD>Admin: <INPUT TYPE="text" name="name"></TD>
            <TD>Password: <INPUT TYPE="password" name="password"></TD>
            <TD><INPUT TYPE="submit" value="adminLogin"></TD>
		</FORM>
    		';
	}
}