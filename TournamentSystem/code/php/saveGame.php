<?php

require 'includes.php';
require_once 'login.php' ;

/**
* Resize an image and keep the proportions
* @author Allison Beckwith <allison@planetargon.com>
* @param string $filename
* @param integer $max_width
* @param integer $max_height
* @return image
*/
function resizeImage($filename, $max_width, $max_height)
{

   list($orig_width, $orig_height) = getimagesize($filename);

   $width = $orig_width;
   $height = $orig_height;

   # taller
   if ($height > $max_height) {
       $width = ($max_height / $height) * $width;
       $height = $max_height;
   }

   # wider
   if ($width > $max_width) {
       $height = ($max_width / $width) * $height;
       $width = $max_width;
   }
	echo "a";
   $image_p = imagecreatetruecolor($width, $height);
	echo "b";
   $image = imagecreatefromjpeg($filename);
	echo "c";
   imagecopyresampled($image_p, $image, 0, 0, 0, 0, 
                                     $width, $height, $orig_width, $orig_height);

   return $image_p;
}

try
{
  $tid = $_REQUEST['tourney_id'];
  $t = new tourney(array('tourney_id'=>$tid));

  $match_id = $_REQUEST['match_id'];
  $m = new match(array('match_id'=>$match_id));

  $t1 = new team(array('team_id'=>$m->getValue('team1_id')));
  $t2 = new team(array('team_id'=>$m->getValue('team2_id')));

  try
    {
      $p = new player(array('player_id'=>$_SESSION['user_id'])) ;
    }
  catch(Exception $e) {}

  try
    {
      $tm = new team(array('team_id'=>$_SESSION['team_id'])) ;
    }
  catch(Exception $e) {}

  if (util::isNull($tm) && !$p->isSuperAdmin() && !$p->isTourneyAdmin($t->getValue('tourney_id')))
    {
      util::throwException('not authorized') ;
    }

  // Create new 
  $mode = $_REQUEST['mode'];

  if ($mode=="edit")
    {
      $game_id = $_POST['game_id'];
      $g = new game(array('game_id'=>$game_id));
      $g->update('map_id',$_POST['map_id']);
      $g->update('team1_score',$_POST['team1_score']);
      $g->update('team2_score',$_POST['team2_score']);
      
      if ($_FILES['filename']['size'] != 0) 
	{
	  $uploaddir = '/usr/quake/demos/tourney/';
	  $uploadfile = $uploaddir . basename($_FILES['filename']['name']);
	  
	  if (is_uploaded_file($_FILES['filename']['tmp_name']))
	    {
	      echo "File ". $_FILES['filename']['name'] ." uploaded.<br>\n";
	    }
	  else
	    {
	      echo "File Error on upload: ";
	      echo "filename '". $_FILES['filename']['tmp_name'] . "'.";
	    }

	  if(!preg_match('/\\.(jpg|png|gif)$/i', $_FILES['filename']['name']))
	    {
	      echo("You cannot upload this type of file.  It must be a <b>png, jpg, or gif</b> file.");
	      exit();
	    }

	  if (move_uploaded_file($_FILES['filename']['tmp_name'], $uploadfile))
	    {
	      echo "File was successfully moved for processing.<br>\n";
	    } 
	  else 
	    {
	      echo "Moving the file Failed!<br>\n";			
	    }
	  
	  //resize to 320x200
	  //echo "1";
	  //$pic = resizeImage($uploadfile, 320, 200);
	  //echo "1";
	  //@imagejpeg($pic, $uploadfile);

	  try 
	    {
	      $g->addScreenshot($uploadfile);
	    }
	  catch (Exception $e) 
	    {
	      print $e;
	      echo "Problem adding screenshot to game!<br>";
	    }
	}
      $msg = "<br>Game updated!<br>";
    }
  elseif ($mode=="delete")
    {
      $game_id = $_REQUEST['game_id'];
      $g = new game(array('game_id'=>$game_id));
      try
	{
	  $g->deleteAll();
	  $msg = "<br>Game deleted!<br>";
	}
      catch (Exception $e)
	{
	  $msg = "<br>Error deleting!<br>";
	}
    }

  else
    {
      try
	{
	  $m->addGame(array('map_id'=>$_POST['map_id'],
			    'team1_score'=>$_POST['team1_score'],
			    'team2_score'=>$_POST['team2_score']));
	  
	  $msg = "<br>New game added!<br>";
	}
      catch (Exception $e)
	{
	  $msg = "<br>Error creating!<br>";
	}
    }

  echo $msg ;
  include 'listGames.php';
  echo "<br><br><a href='?a=reportMatch&amp;tourney_id=$tid&amp;division_id=$division_id&amp;match_id=$match_id&amp;approved=$approved&amp;approved_step=1'>Report Match Page</a>";
}
catch (Exception $e) {
  include 'listGames.php';
}
?>
