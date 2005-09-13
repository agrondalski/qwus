<?php

require_once 'includes.php';
require_once 'login.php';

$tid = $_POST['tourney_id'];
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
  $news = new news(array('news_id'=>$nid,
                         'writer_id'=>$_POST['writer_id'],
                         'tourney_id'=>$tid,
                         'subject'=>$_POST['subject'],
                         'news_date'=>date("Y-m-d"),
			 'text'=>$_POST['text'],
			 'isColumn'=>true));

  $msg = "<br>Column entry created!<br>";
}

include 'listColumn.php';
echo $msg ;
?>
