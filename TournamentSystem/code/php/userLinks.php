<?php

$tid = $_REQUEST['tourney_id'];

if ($tid != "") 
{
	// tourney logo = img/tourney_logo###.jpg
	echo '<style type="text/css">';
	echo "<!--";
	echo ".logo {width: 680px; height: 250px; background: url(img/tourney_logo".$_REQUEST['tourney_id'].".jpg) #FFFFFF no-repeat center;}";
	echo "-->";
	echo "</style>";
	
  echo "<a href='?a=home&amp;tourney_id=" . $tid . "'>News</a>&nbsp;-";
  echo "&nbsp;<a href='?a=standings&amp;tourney_id=" . $tid . "'>Standings</a>&nbsp;-";
  echo "&nbsp;<a href='?a=schedule&amp;tourney_id=" . $tid . "'>Schedule</a>&nbsp;-";
  echo "&nbsp;<a href='?a=statistics&amp;tourney_id=" . $tid . "'>Stats</a>&nbsp;-";
  echo "&nbsp;<a href='?a=results&amp;tourney_id=" . $tid . "'>Results</a>&nbsp;-";
  echo "&nbsp;<a href='?a=displayRules&amp;tourney_id=" . $tid . "'>Rules</a>&nbsp;";
  echo "<br>";
}
?>