<?php

require_once 'includes.php';

try
{
  $news_id  = $_REQUEST['news_id'] ;
  $match_id = $_REQUEST['match_id'] ;

  if (!util::isNull($news_id))
    {
      $news = new news(array('news_id'=>$news_id)) ;
      $comments = $news->getComments() ;
    }
  else
    {
      $match = new match(array('match_id'=>$match_id)) ;
      $comments = $match->getComments() ;
    }

  echo "<h2>Comments</h2>";

  foreach($comments as $c)
    {
      echo '<b>' . $c->getValue('name') . '</b>&nbsp;&nbsp;<i>'  . $c->getValue('comment_date') . '&nbsp;&nbsp;' . substr($c->getValue('comment_time'), 0, 5) . '</i>' ;
      //echo '(' . $c->getValue('player_ip') . ')' ;
      echo '<br>' ;
      echo "<p>" . $c->getValue('comment_text') . "</p>" ;
    }

  
  echo '<IMG src="img/hr.gif" alt="" width="550" height="22">';

  echo "<form action='?" . $_SERVER['QUERY_STRING'] . "' method=post>";
  echo "<table>" ;

  echo "<tr><td>name</td></tr>" ;
  echo "<tr><td><input type='text' name='name'></td></tr>";

  echo "<tr><td>Comment</td></tr>" ;
  echo "<tr><td><textarea name='comment_text' cols='60' rows='5'>$text</textarea></td></tr>";

  echo "<tr><td><input type='submit' value='Submit' name='B1' class='button'>";
  echo "&nbsp;<input type='reset' value='Reset' name='B2' class='button'></td></tr>";

  echo "</table>" ;
  echo "</form>" ;
}
catch (Exception $e){print $e;}
