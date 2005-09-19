<?php

require 'php/includes.php' ;

$morenews = (empty($_GET["id"])) ? true : false ;
$column   = (empty($_GET["column"])) ? false: true ;
$printed = 0;

try
{
  if (!$column)
    {
      if ($morenews)
	{
	  // Tourney news
	  if (isset($_GET["tourney_id"]))
	    {
	      $t = new tourney(array('tourney_id'=>$_GET["tourney_id"])) ;
	      $news = $t->getNews(array('news_date', SORT_DESC, 'news_id', SORT_DESC), array('limit'=>'0,5')) ;
	      $count = $t->getNewsCount() ;
	    }

	  // General news
	  else
	    {
	      $news  = news::getNews(array('news_date', SORT_DESC, 'news_id', SORT_ASC), array('limit'=>'0,5')) ;
	      $count = news::getNewsCount() ;
	    }
	}

      // Archive item
      else
	{
	  $news = array(new news(array('news_id'=>$_GET["id"]))) ;

	  if (isset($_GET["tourney_id"]))
	    {  
	      $tid  = '&amp;tourney_id=' . $_GET['tourney_id'] ;
	    }

	  if (isset($_GET["column"]))
	    {  
	      $tid  = '&amp;column=' . $_GET['column'] ;
	    }
	}
    }
  else
    {
      $p = new player(array('name'=>$_GET['column'])) ;
      $news = $p->getNewsColumns(array('news_date', SORT_DESC, 'news_id', SORT_DESC), array('limit'=>'0,1')) ;
    }
}
catch(Exception $e)
{
  $news = null ;
  $count = 0 ;
}

if (isset($_GET["tourney_id"]))
{
  include 'userLinks.php';
  echo "<br>";
}

for ($i=0; $i<count($news); $i++)
{
  $writer = $news[$i]->getWriter()->getValue("name") ;

  echo '
	<TABLE cellspacing="0" cellpadding="0" class="news">
	<TR>
		<TD>
		<TABLE cellspacing="0" cellpadding="0">
		<TR>
			<TD><B>' . $news[$i]->getValue("subject") . '</B></TD>
			<TD align="right"><SMALL>' . $news[$i]->getValue("news_date") . '</SMALL></TD>
		</TR>
		</TABLE>
		</TD>
	</TR>
	<TR>
                <TD>' . $news[$i]->getValue("text") . '<P>Written by:&nbsp;' . $writer . '</P></TD>
	</TR>
	</TABLE>';
	$printed++;

	if ($morenews && !$column && $printed != $count)
	{
		echo '<IMG src="img/hr.gif" alt="" width="370" height="22">';
	}	
}

$c = ($count==0) ? 0 : 1 ;

if (!$column)
{
  // General News
  if ($morenews)
    {
      $bottom = '<P class="gray">viewing news ' . $c . '-' . $printed . ' of ' . $count .'</P>' ;
    }

  // Archive News
  else
    {
      $bottom = '<P><A href="?a=newsarchive' . $tid . '">back to news archive</A></P>' ;
    }
}
else 
{
  // Column
  if ($printed>0)
    {
      $bottom = '<P><A href="?a=newsarchive&amp;column=' .  $_GET['column'] . '">column archive</A></P>' ;
    }
  else
    {
      $bottom = 'No columns yet.' ;
    }
}

echo $bottom;
?>