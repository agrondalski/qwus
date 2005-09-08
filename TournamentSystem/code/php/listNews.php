<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];

$t = new tourney(array('tourney_id'=>$tid));

include 'tourneyLinks.php';
echo "<br>";

echo "<table border=1 cellpadding=2 cellspacing=0>\n";
echo "<th>id</th><th>Author</th><th>Subject</th><th>Date</th><th>Text</th><th colspan=2>Actions</th>";
foreach ($t->getNews() as $news) {
   echo "\t<tr>\n";
   echo "\t<td>",$news->getValue('news_id'),"</td>\n";
   echo "\t<td>",$news->getValue('writer_id'),"</td>\n";
   echo "\t<td>",$news->getValue('subject'),"</td>\n";
   echo "\t<td>",$news->getValue('news_date'),"</td>\n";
   echo "\t<td>",$news->getValue('text'),"</td>\n";
   echo "<td><a href='?a=manageNews&amp;tourney_id=$tid&amp;mode=edit&amp;nid=",$news->getValue('news_id'),"'>
Edit</a></td>";
   echo "<td><a href='?a=saveNews&amp;tourney_id=$tid&amp;mode=delete&amp;nid=",$news->getValue('news_id'),"'>
Delete</a></td>";

   echo "\t</tr>\n";
}
echo "</table>\n";
?>
