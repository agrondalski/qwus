<?php

require 'includes.php';

$tid = $_POST['tourney_id'];

// Create the new division 

$mode = $_REQUEST['mode'];

if ($mode=="edit") {
  $nid = $_POST['nid'];
  $news = new news(array('news_id'=>$nid));

  $news->update('subject',$_POST['subject']);
  $news->update('text',$_POST['text']);
  $msg = "<br>News updated!<br>";
}
elseif ($mode=="delete") {

  $nid = $_REQUEST['did'];
  $news = new news(array('news_id'=>$nid));
  try {
    $div->delete();
    $msg = "<br>News item deleted!<br>";
  }
  catch (Exception $e) {
    $msg = "<br>Error deleting!<br>";
  }
}
else {
  $news = new news(array('news_id'=>$nid,
                         'writer_id'=>$_POST['writer_id'],
                         'tourney_id'=>$_POST['tourney_id'],
                         'subject'=>$_POST['subject'],
                         'news_date'=>date("Y-m-d g:ia"),
                 	  	 'text'=>$_POST['text']));

$msg = "<br>New News Item created!<br>";
}
include 'listNews.php';
echo $msg
?>
