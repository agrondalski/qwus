<TABLE cellspacing="0" cellpadding="0" class="newsarchive">
<?php
require_once 'includes.php' ;

$months = Array('','January','February','March','April','May','June','July','August','September','October','November','December');

$currentYear = "";
$currentMonth = "";

try
{
  // Tourney Archive
  if (isset($_GET['tourney_id']))
    {
      $t    = new tourney(array('tourney_id'=>$_GET["tourney_id"])) ;
      $news = $t->getNews(array('news_date', SORT_DESC, 'news_id', SORT_DESC)) ;
      $info = '&amp;tourney_id=' . $_GET['tourney_id'] ;

      include 'userLinks.php';
      echo "<br>" ;
    }

  // Column archive
  elseif (isset($_GET['column']))
    {
      $p    = new player(array('name'=>$_GET['column'])) ;
      $news = $p->getNewsColumns(array('news_date', SORT_DESC, 'news_id', SORT_DESC)) ;
      $info = '&amp;column=' . $_GET['column'] ;
    }

  // General archive
  else
    {
      $news = news::getNews(array('news_date', SORT_DESC, 'news_id', SORT_DESC)) ;
    }
}
catch (Exception $e)
{
  $news = null ;
  $count = 0 ;
}

echo '<TR><TD><TABLE cellspacing="0" cellpadding="0">';

for ($i=0; $i<count($news); $i++)
{
  $news_date = $news[$i]->getValue("news_date") ;

  if ($news_date[5] == "0"){$month = $months[$news_date[6]];}
  else {$month = $months[$news_date[5] . $news_date[6]];}
  $year = $news_date[0] . $news_date[1] . $news_date[2] . $news_date[3];

  if ($year != $currentYear)
    {
      if ($currentYear != "")
	{
	  echo '</TABLE></TD></TR><TR><TD height="10"></TD></TR>';
	  echo '<TR><TD><TABLE cellspacing="0" cellpadding="0">';
	}
      $currentYear = $year;
      $currentMonth = "";
      echo '<TR><TD align="center"><BIG><U>' . $year . '</U></BIG></TD></TR>';
    }
  
  if ($month != $currentMonth)
    {
      if ($currentMonth != "")
	{
	  echo '<TR><TD height="10"></TD></TR>';
	}
      $currentMonth = $month;
      echo '<TR><TD><B>' . $month . '</B></TD></TR>';
    }

  echo '<TR><TD><TABLE cellspacing="0" cellpadding="1"><TR><TD class="file_txt"></TD><TD><A href="?a=home&amp;news_id=' . $news[$i]->getValue("news_id") . $info . '">' . $news[$i]->getValue("subject") . '</A><SMALL>' . $news[$i]->getValue("news_date") . '</SMALL></TD></TR></TABLE></TD></TR>';
}

echo '</TABLE></TD></TR>';
?>
</TABLE>
