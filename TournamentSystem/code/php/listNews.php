<?php

require 'includes.php';
require_once 'login.php';

try
{
  $tid = $_REQUEST['tourney_id'];
  $t = new tourney(array('tourney_id'=>$tid));
}
catch(Exception $e) {}

echo "<br>";

if (!util::isNull($t))
{
  $allNews = $t->getNews() ;
}
else
{
  $allNews = news::getNews() ;
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
?>
