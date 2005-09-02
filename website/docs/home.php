<?php

require 'php/includes.php' ;

$morenews = (empty($_GET["id"])) ? true : false;
$printed = 0;

if ($morenews)
{
  if (isset($_GET["tourney_id"]))
    {
      $t = new tourney(array('tourney_id'=>$_GET["tourney_id"])) ;
      $news = $t->getNews() ;
      $count = $t->getNewsCount() ;
    }
  else
    {
      $news  = news::getNews(array('limit'=>'0,5')) ;
      $count = news::getNewsCount() ;
    }
}
else
{
  $news = array(new news(array('news_id'=>$_GET["id"]))) ;
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
			<TD align="right"><SMALL>' . $news[$i]->getValue("date") . '</SMALL></TD>
		</TR>
		</TABLE>
		</TD>
	</TR>
	<TR>
                <TD>' . $news[$i]->getValue("text") . '<P>Written by:&nbsp;' . $writer . '</P></TD>
	</TR>
	</TABLE>';
	$printed++;
	if ($morenews && $printed != $count)
	{
		echo '<IMG src="img/hr.gif" alt="" width="370" height="22">';
	}	
}

$c = ($count==0) ? 0 : 1 ;
$bottom = ($morenews) ? '<P class="gray">viewing news ' . $c . '-' . $printed . ' of ' . $count .'</P>' : '<P><A href="?a=newsarchive">back to news archive</A></P>';
echo $bottom;
?>