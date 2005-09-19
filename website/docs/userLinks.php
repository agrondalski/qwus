<?php

$tid = $_REQUEST['tourney_id'];

if ($tid != "") 
{
	echo "<a href='?a=home&tourney_id=",$tid,"'>News</a>&nbsp;-";
	echo "&nbsp;<a href='?a=standings&tourney_id=",$tid,"'>Standings</a>&nbsp;-";
	echo "&nbsp;<a href='?a=schedule&tourney_id=",$tid,"'>Schedule</a>&nbsp;-";
	echo "&nbsp;<a href='?a=statistics&tourney_id=",$tid,"'>Stats</a>&nbsp;-";
	echo "&nbsp;<a href='?a=standings&tourney_id=",$tid,"'>Results</a>";
	echo "<br>";
}
?>