<?php

require_once 'includes.php';
require_once 'login.php';

$mode = $_REQUEST['mode'];

try
{
  $p = new player(array('name'=>$_SESSION["username"]));
}
catch(Exception $e)
{
  return ;
}

if ($mode == "edit")
{
  echo "<b><p>Modify a Column:</b></p>";

  try
    {
      $nid = $_REQUEST['nid'];
  
      $news = new news(array('news_id'=>$nid));
      $writer_id=$news->getValue('writer_id');
      $writer_player = new player(array('player_id'=>$writer_id));
      $writer_name = $writer_player->getValue('name');
      $subject=$news->getValue('subject');
      $text=$news->getValue('text');
    }
  catch(Exception $e){}
}

else
{
  echo "<p><b>Create a column:</b></p>";
  $writer_name=$p->getValue('name') ;
  $writer_id=$p->getValue('player_id');
  $subject="";
  $text="";
}

echo "<form action='?a=saveColumn' method=post>";

if ($mode == "edit")
{
  echo "<input type='hidden' name='nid' value='$nid'>";
  echo "<input type='hidden' name='mode' value='edit'>";
}

echo "<table border=1 cellpadding=2 cellspacing=0>";
echo "<tr>";
echo "<td>author:</td><td>";
echo "<input type='text' disabled name='name' maxlength='50' value='",$writer_name,"' size='30'></td>";
echo "<input type='hidden' name='writer_id' value='$writer_id'>";
echo "</tr>";
echo "<tr>";
echo "<td>Subject:</td><td>";
echo "<input type='text' name='subject' value='",$subject,"' size='50'></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Text:</td><td>";
echo "<textarea name='text' cols='80' rows='8'>$text</textarea></td>";
echo "</tr>";
echo "<tr><td>&nbsp;</td><td><input type='submit' value='Submit' name='B1' class='button'>";
echo "&nbsp;<input type='reset' value='Reset' name='B2' class='button'></td></tr></table>";
echo "</p></font>";
echo "</form>" ;
?>

<?php
include 'listColumn.php';
?>
