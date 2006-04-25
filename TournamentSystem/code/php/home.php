<?php

require_once 'includes.php' ;

$morenews = (empty($_GET["news_id"])) ? true : false ;
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
	      $news = $t->getNews(array('news_date', SORT_DESC, 'news_id', SORT_DESC), array('limit'=>5)) ;
	      $count = $t->getNewsCount() ;
	    }

	  // General news
	  else
	    {
	      $news  = news::getNews(array('news_date', SORT_DESC, 'news_id', SORT_DESC), array('limit'=>5)) ;
	      $count = news::getNewsCount() ;
	    }
	}

      // Archive item
      else
	{
	  $news = array(new news(array('news_id'=>$_GET["news_id"]))) ;

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
      if ($morenews)
	{
	  $p = new player(array('name'=>$_GET['column'])) ;
	  $news = $p->getNewsColumns(array('news_date', SORT_DESC, 'news_id', SORT_DESC), array('limit'=>1)) ;
	}
      else
	{
	  $news = array(new news(array('news_id'=>$_GET["news_id"]))) ;
	}
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
  echo "<h2>News</h2>";
}

for ($i=0; $i<count($news); $i++)
{
  $writer = $news[$i]->getWriter()->getValue("name") ;

  echo '
	<TABLE cellspacing="0" cellpadding="0" class="news">
	<TR>
              <TD><B>' . $news[$i]->getValue("subject") . '</B></TD>
	      <TD align="right"><SMALL>' . $news[$i]->getValue("news_date") . '</SMALL></TD>
	</TR>
	<TR>
              <TD colspan=2>' . $news[$i]->getValue("text") . '</TD>
        </TR>
        <TR>
              <TD>&nbsp;</TD>
       </TR>
        <TR>
            <TD>Written by:&nbsp;' . $writer . '</TD>' ;

  if (util::isNull($_REQUEST['news_id']))
    {
      if (!util::isNull($_GET["tourney_id"]))
	{
	  $l = '&amp;tourney_id=' . $_GET["tourney_id"] ;
	}
      elseif (!util::isNull($_GET["column"]))
	{
	  $l = '&amp;column=' . $_GET["column"] ;
	}

      echo "<TD align='right'><SMALL><a href='?a=home&amp;news_id=" . $news[$i]->getValue('news_id') . $l . "'>" . $news[$i]->getCommentCount() . " Comments</a></SMALL></TD>" ;
    }

  echo '</TR>
        </TABLE>';

  $printed++;

  if ($morenews && !$column && $printed != $count)
    {
      echo '<IMG src="img/hr.gif" alt="" width="550" height="22">';
    }	
}

if (!util::isNull($_REQUEST['news_id']))
{
  echo '<IMG src="img/hr.gif" alt="" width="550" height="22">';
  require 'listComments.php' ;
}

$c = ($count==0) ? 0 : 1 ;

$bottom = null ;

if (!$column)
{
  // General News
  if ($morenews)
    {
      $bottom = '<P class="gray">viewing news ' . $c . '-' . $printed . ' of ' . $count .' - <a href="?a=newsarchive">News Archive</a></P>' ;
    }

  // Archive News
  else
    {
      //$bottom = '<P><A href="?a=newsarchive' . $tid . '">back to news archive</A></P>' ;
    }
}
else 
{
  // Column
  if ($printed>0)
    {
       $bottom = '<P><a href="?a=newsarchive&amp;column='.$_GET['column'].'">Column Archive</a>';//$bottom = '<P><A href="?a=newsarchive&amp;column=' .  $_GET['column'] . '">column archive</A></P>' ;
    }
  else
    {
      $bottom = 'No columns yet.' ;
    }
}

echo $bottom;
?>