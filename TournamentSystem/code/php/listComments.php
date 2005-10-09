<?php

require_once 'includes.php';

try
{
  $news_id  = $_REQUEST['news_id'] ;
  $match_id = $_REQUEST['match_id'] ;

  if (!util::isNull($news_id))
    {
      $news = new news(array('news_id'=>$news_id)) ;

      if (!util::isNull($_REQUEST['comment_text']))
	{
	  $news->addComment(array('name'=>$_REQUEST['name'], 'comment_text'=>$_REQUEST['comment_text'])) ;
	}

      $comments = $news->getComments() ;
    }
  else
    {
      $match = new match(array('match_id'=>$match_id)) ;

      if (!util::isNull($_REQUEST['comment_text']))
	{
	  $m->addComment(array('name'=>$_REQUEST['name'], 'comment_text'=>$_REQUEST['comment_text'])) ;
	}

      $comments = $match->getComments() ;
    }

  

  echo "<table width=100% border=0>";
  echo "<tr><td colspan=2><b>Comments:</b><br></td></tr>";
  foreach($comments as $c)
    {
      echo '<tr><td align=left width=33%><b>' . $c->getValue('name') . '</b>&nbsp;&nbsp;</td><td align=right width=67%><SMALL>'  . $c->getValue('comment_date') . '&nbsp;&nbsp;' . substr($c->getValue('comment_time'), 0, 5) . '</SMALL></td></tr>' ;
      //echo '(' . $c->getValue('player_ip') . ')' ;
      echo "<tr bgcolor='#DDDDDD'><td colspan=2>" . $c->getValue('comment_text') . "<br>&nbsp;</td></tr>" ;
      echo "<tr hieght='50'><td colspan=2></td></tr>" ;
    }
  echo "</table>";

  
  echo '<IMG src="img/hr.gif" alt="" width="550" height="22">';

  echo "<form action='?" . $_SERVER['QUERY_STRING'] . "' method=post>";
  echo "<table>" ;

  echo "<tr><td><b>Name:</b></td></tr>" ;
  echo "<tr><td><input type='text' name='name'></td></tr>";

  echo "<tr><td><b>Comment:</b></td></tr>" ;
  echo "<tr><td><textarea name='comment_text' cols='60' rows='5'>$text</textarea></td></tr>";

  echo "<tr><td><input type='submit' value='Submit' name='B1' class='button'>";
  echo "</td></tr>";
  echo "</table>" ;
  echo "</form>" ;
  echo "<small>(Abuse of comments can get you banned!)</small>";
}
catch (Exception $e) {}
