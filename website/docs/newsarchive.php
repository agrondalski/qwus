<TABLE cellspacing="0" cellpadding="0" class="newsarchive">
<?php
$months = Array('','January','February','March','April','May','June','July','August','September','October','November','December');

	$currentYear = "";
	$currentMonth = "";
	$news = mysql_query("select n.news_id, n.subject, n.news_date, n.text, p.name from news n, player p where n.writer_id=p.player_id order by news_id");
	echo '<TR><TD><TABLE cellspacing="0" cellpadding="0">';
			
	while($out = mysql_fetch_row($news))
	{
		if ($out[2][5] == "0"){$month = $months[$out[2][6]];}
		else {$month = $months[$out[2][5] . $out[2][6]];}
		$year = $out[2][0] . $out[2][1] . $out[2][2] . $out[2][3];
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
		echo '<TR><TD><TABLE cellspacing="0" cellpadding="1"><TR><TD class="file_txt"></TD><TD><A href="?a=home&amp;id=' . $out[0] . '">' . $out[1] . '</A><SMALL>' . $out[2] . '</SMALL></TD></TR></TABLE></TD></TR>';
	}

	echo '</TABLE></TD></TR>';
?>
</TABLE>