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

  if (!$p->hasColumn())
    {
      util::throwException('not authorized') ;
    }

  echo "<table border=1 cellpadding=2 cellspacing=0>\n";
  echo "<th>Subject</th><th>Date</th><th>Text</th><th colspan=2>Actions</th>";

  foreach ($p->getNewsColumns() as $news)
    {
      echo "\t<tr>\n";
      echo "\t<td>",$news->getValue('subject'),"</td>\n";
      echo "\t<td>",$news->getValue('news_date'),"</td>\n";
      echo "\t<td>",$news->getValue('text'),"</td>\n";
      echo "<td><a href='?a=manageColumn&amp;mode=edit&amp;nid=",$news->getValue('news_id'),"'>Edit</a></td>";
      echo "<td><a href='?a=saveColumn&amp;mode=delete&amp;nid=",$news->getValue('news_id'),"'>Delete</a></td>";
      
      echo "\t</tr>\n";
    }

  echo "</table>\n";

  echo "<p><a href='?a=manageColumn'>Create Column</a>";
}
catch(Exception $e){}
?>
