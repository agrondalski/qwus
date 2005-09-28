<?php

require_once 'includes.php';
require_once 'login.php';

try
{
  $tid = $_REQUEST['tourney_id'];
  if (!util::isNull($tid))
    {
      $t = new tourney(array('tourney_id'=>$tid));
    }

  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  if (!$p->isSuperAdmin() && !util::isNull($t) && !$p->isTourneyAdmin($t->getValue('tourney_id')))
    {
      util::throwException('not authorized') ;
    }

  $mode = $_REQUEST['mode'];

  if ($mode=="edit")
    {
      try
	{
	  $nid = $_POST['nid'];
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
	  $msg = "<br>News item deleted!<br>";
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
	  if (util::isNull($tid))
	    {
	      $news = new news(array('writer_id'  => $_POST['writer_id'],
				     'subject'    => $_POST['subject'],
				     'news_date'  => date("Y-m-d"),
				     'news_type'  => news::TYPE_NEWS,
				     'id'         => $tid,
				     'text'       => $_POST['text']));
	    }
	  else
	    {
	      $t->addNews(array('writer_id'  => $_POST['writer_id'],
				'subject'    => $_POST['subject'],
				'news_date'  => date("Y-m-d"),
				'id'         => $tid,
				'text'       => $_POST['text']));
	    }

	  $msg = "<br>New News Item created!<br>";
	}
      catch (Exception $e)
	{
	  $msg = "<br>Unable to create news item!<br>";
	}
    }

  echo $msg ;
  include 'listNews.php';
}
catch (Exception $e) {}
?>
