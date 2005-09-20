<?php

require_once 'includes.php';
require_once 'login.php';

try
{
  if (!util::isNull($tid))
    {
      $t = new tourney(array('tourney_id'=>$tid));
    }

  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  if (!$p->hasColumn())
    {
      util::throwException('not authorized') ;
    }

  $mode = $_REQUEST['mode'];

  if ($mode=="edit")
    {
      $nid = $_POST['nid'];
      
      try
	{
	  $news = new news(array('news_id'=>$nid));
	  
	  $news->update('subject',$_POST['subject']);
	  $news->update('text',$_POST['text']);
	  $msg = "<br>News updated!<br>";
	}
      catch (Exception $e)
	{
	  $msg = "<br>Error modifying!<br>";
	}
    }
  
  elseif ($mode=="delete")
    {
      try
	{
	  $nid = $_REQUEST['nid'];
	  $news = new news(array('news_id'=>$nid));
	  $news->delete();
	  $msg = "<br>Column entry deleted!<br>";
	}
      catch (Exception $e)
	{
	  $msg = "<br>Error deleting!<br>";
	}
    }

  else
    {
      $news = new news(array('writer_id'  => $_POST['writer_id'],
			     'subject'    => $_POST['subject'],
			     'news_date'  => date("Y-m-d"),
			     'news_type'  => news::TYPE_COLUMN,
			     'id'         => null,
			     'text'       => $_POST['text'])) ;

      $msg = "<br>Column entry created!<br>";
    }
  
  echo $msg ;
  include 'listColumn.php';
}
catch (Exception $e) {}
?>
