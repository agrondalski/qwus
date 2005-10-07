<?php

require 'includes.php';
require_once 'login.php';

try
{
  $tid = $_REQUEST['tourney_id'];
  if (!util::isNull($tid))
    {
      $t = new tourney(array('tourney_id'=>$tid));
    }

  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  if (!$p->isSuperAdmin() && $t!=null && !$p->isTourneyAdmin($t->getValue('tourney_id')))
    {
      util::throwException('not authorized') ;
    }

  echo "<br>";

  if (!util::isNull($tid))
    {
      $allNews = $t->getNews(array('news_date', SORT_DESC, 'news_id', SORT_DESC)) ;
    }
  else
    {
      $allNews = news::getNews(array('news_date', SORT_DESC, 'news_id', SORT_DESC)) ;
    }

  echo "<table border=1 cellpadding=2 cellspacing=0>\n";
  echo "<th>Author</th><th>Subject</th><th>Date</th><th>Text</th><th colspan=2>Actions</th>";

  foreach ($allNews as $news)
    {
      $p = new player(array('player_id'=>$news->getValue('writer_id')));
      echo "\t<tr>\n";
      echo "\t<td>",$p->getValue('name'),"</td>\n";
      echo "\t<td>",$news->getValue('subject'),"</td>\n";
      echo "\t<td>",$news->getValue('news_date'),"</td>\n";
      echo "\t<td>",$news->getValue('text'),"</td>\n";
      
      if (!util::isNull($t))
	{
	  echo "<td><a href='?a=manageNews&amp;tourney_id=$tid&amp;mode=edit&amp;nid=",$news->getValue('news_id'),"'>Edit</a></td>";
	  echo "<td><a href='?a=saveNews&amp;tourney_id=$tid&amp;mode=delete&amp;nid=",$news->getValue('news_id'),"'>Delete</a></td>";
	}
      else
	{
	  echo "<td><a href='?a=manageNews&amp;mode=edit&amp;nid=",$news->getValue('news_id'),"'>Edit</a></td>";
	  echo "<td><a href='?a=saveNews&amp;mode=delete&amp;nid=",$news->getValue('news_id'),"'>Delete</a></td>";
	}
      
      echo "\t</tr>\n";
    }

  echo "</table>\n";

  if (!util::isNull($t))
    {
      echo "<p><a href='?a=manageNews&amp;tourney_id=$tid'>Create News</a>";
    }
  else 
    {
      echo "<p><a href='?a=manageNews'>Create News</a>";
    }
}
catch (Exception $e) {}
?>