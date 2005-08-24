<?php

$morenews = (empty($_GET["id"])) ? true : false;
$printed = 0;

if ($morenews)
{
	$news = mysql_query("select n.news_id, n.subject, n.news_date, n.text, p.name, l.logo_url FROM news n, player p, location l where n.writer_id=p.player_id and p.location_id=l.location_id order by news_id desc limit 0,5");
	$count = mysql_fetch_row(mysql_query("select count(news_id) from news"));
}
else
{
	$id = $_GET["id"];
	$news = mysql_query("select n.news_id, n.subject, n.news_date, n.text, p.name, l.logo_url from news n, player p, location l where n.writer_id=p.player_id and p.location_id=l.location_id and n.news_id=$id");
}

while ($print = mysql_fetch_row($news))
{
	echo '
	<TABLE cellspacing="0" cellpadding="0" class="news">
	<TR>
		<TD>
		<TABLE cellspacing="0" cellpadding="0">
		<TR>
			<TD><B>' . $print[1] . '</B></TD>
			<TD align="right"><SMALL>' . $print[2] . '</SMALL></TD>
		</TR>
		</TABLE>
		</TD>
	</TR>
	<TR>
                <TD>' . $print[3] . '<P>Written by:&nbsp;' . $print[4] . '</P></TD>
	</TR>
	</TABLE>';
	$printed++;
	if ($morenews && $printed != $count[0])
	{
		echo '<IMG src="img/hr.gif" alt="" width="370" height="22">';
	}	
}
$bottom = ($morenews) ? '<P class="gray">viewing news 1-' . $printed . ' of ' . $count[0] .'</P>' : '<P><A href="?a=newsarchive">back to news archive</A></P>';
echo $bottom;
?>