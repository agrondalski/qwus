<?php

require 'includes.php';

try
{
	// Fix name_abbr to lowercase if needed
	$name_abbr = $_POST['name_abbr'];
	$name_abbr = strtolower($name_abbr);

	try
	{
	  // Make sure this team has a password
	  $pw = $_POST['password'];

	  if (util::isNull($pw))
	    {
	      $msg = "<br>Error creating team! Password can not be null.<br>" ;
	    }
	  else
	    {
	      $tm = new team(array('name'=>$_POST['name'],
				   'name_abbr'=>$name_abbr,
				   'email'=>$_POST['email'],
				   'irc_channel'=>$_POST['irc_channel'],
				   'location_id'=>$_POST['location_id'],
				   'password'=>$pw,
				   'approved'=>0));

	      $msg = "<br><b>New team created!</b><p>You can login with your team once your team is approved by an admin.  You should receive an email.";
	    }
	}
	catch (Exception $e)
	{
	  $msg = "<br>Error creating team!<br>" ;
	}

	echo $msg;
}
catch (Exception $e) {}
?>
