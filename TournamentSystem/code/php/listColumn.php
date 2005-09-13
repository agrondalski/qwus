<?php

require_once 'includes.php';
require_once 'login.php';

try
{
  $p = new player(array('name'=>$_SESSION['username'])) ;
  $columns = $p->getNewsColumns();
}
catch(Exception $e){}

echo "<table border=1 cellpadding=2 cellspacing=0>\n";
echo "<th>Subject</th><th>Date</th><th>Text</th><th colspan=2>Actions</th>";

foreach ($columns as $news)
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
?>
