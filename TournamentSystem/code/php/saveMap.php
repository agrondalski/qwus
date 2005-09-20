<?php

require 'includes.php';
require_once 'login.php' ;

try
{
  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  if (!$p->isSuperAdmin())
    {
      util::throwException('not authorized') ;
    }

  $mode = $_REQUEST['mode'];

  if ($mode=="edit")
    {
      $map_id = $_POST['map_id'];
      $m = new map(array('map_id'=>$map_id));

      try
	{
	  $m->update('map_name', $_POST['map_name']);
	  $m->update('map_abbr', $_POST['map_abbr']);
	  $m->update('game_type_id', $_POST['game_type_id']);
      
	  $msg = "<br>Map updated!<br>";
	}
      catch (Exception $e)
	{
	  print $e ;
	  $msg = "<br>Error updating!<br>";
	}
    }

  elseif ($mode=="delete")
    {
      $map_id = $_REQUEST['map_id'];
      $m = new map(array('map_id'=>$map_id));

      try
	{
	  $m->delete();
	  $msg = "<br>Map deleted!<br>";
	}
      catch (Exception $e)
	{
	  $msg = "<br>Error deleting!<br>";
	}
    }

  else
    {
      $m = new map(array('map_name'=>$_POST['map_name'],
			 'map_abbr'=>$_POST['map_abbr'],
			 'game_type_id'=>$_POST['game_type_id'])) ;
	
      $msg = "<br>New Map created!<br>";
    }

  echo $msg;
  include 'listMaps.php';
}
catch (Exception $e) {}
?>
