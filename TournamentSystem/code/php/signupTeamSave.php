<?php
session_start() ;
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
	  $msg = "<b>Error creating team!</b><p>Password can not be null.<br>" ;
	}

      elseif ($_POST['image_password'] != $_SESSION['validate_pw'])
	{
	  $msg = "<b>Error creating team.</b><p>Image password does not match. If you use the back button, you must reload the page to generate a new image.<br>" ;
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

	  $msg = "<b>New team created!</b><p>You can login with your team once you receive an email that your team was approved by an admin.";
	  $msg .= "&nbsp;&nbsp;Once logged in you should be able to sign up for tournaments, create players, and assign players to your team.";
	  $msg .= "&nbsp;&nbsp;Thank you for registering!";
	}
    }
  catch (Exception $e)
    {
      $msg = "<b>Error creating team!</b><br>" ;
    }
  
  echo $msg;
}
catch (Exception $e) {}
?>
