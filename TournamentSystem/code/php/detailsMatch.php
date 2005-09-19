
<?php

require 'includes.php';
$tid = $_REQUEST['tourney_id'];

$t = new tourney(array('tourney_id'=>$tid));

$match_id = $_REQUEST['match_id'];
$m = new match(array('match_id'=>$match_id));

include 'userLinks.php';
echo "<br>";


echo "<table border=1 cellpadding=2 cellspacing=0>\n";
echo "<tr><th>Week</th><th>Result</th><th>Match Date</th></tr>";
$wid = $m->getValue('winning_team_id');

$t1 = new team(array('team_id'=>$m->getValue('team1_id')));
$t2 = new team(array('team_id'=>$m->getValue('team2_id')));
$ms = new match_schedule(array('schedule_id'=>$m->getValue('schedule_id')));
echo "<tr>";
echo "<td>",$ms->getValue('name'),"</td>";
//echo "<td><a href='?a=detailsMatch&amp;tourney_id=",$tid,"&amp;match_id=",$m->getValue('match_id'),"'>";
echo "<td>";
if ($wid == $m->getValue('team1_id'))
{
	echo "<b>";
	echo $t1->getValue('name');
	echo "</b>";
}
else 
{
	echo $t1->getValue('name');
}

echo "&nbsp;vs&nbsp;";

if ($wid == $m->getValue('team2_id'))
{
	echo "<b>";
	echo $t2->getValue('name');
	echo "</b>";
}
else 
{
	echo $t2->getValue('name');
}
// Don't end row yet
$team1 = 0;
$team2 = 0;
$gameout = "";
foreach ($m->getGames() as $g)
{	
	$gameout .= "<table border=1 cellpadding=2 cellspacing=0 align=center width=100%>";
	if ($g->getValue('team1_score') > $g->getValue('team2_score'))
	{
		$team1 += 1;				
	}
	elseif ($g->getValue('team1_score') < $g->getValue('team2_score'))
	{
		$team2 += 1;				
	}	
	$t1out = "<a href='?a=detailsTeam&amp;tourney_id=".$tid."&amp;team_id=".$t1->getValue('team_id')."'>".$t1->getValue('name')."</a>";
	$t2out = "<a href='?a=detailsTeam&amp;tourney_id=".$tid."&amp;team_id=".$t2->getValue('team_id')."'>".$t2->getValue('name')."</a>";
	$map = new map(array('map_id'=>$g->getValue('map_id')));
	$gameout .= "<tr><td align=center>".$map->getValue('map_abbr')."<br>";
	$gameout .= "<table border=0 cellspacing=4 cellpadding=0 width=75%>";
	$gameout .= "<tr><td align=center>".$t1out."</td>";
	$gameout .= "<td align=center>".$t2out."</td></tr>";
	$gameout .= "<tr><td align=center>".$g->getValue('team1_score')."</td>";
	$gameout .= "<td align=center>".$g->getValue('team2_score')."</td></tr>";
	$gameout .= "</table>";
	$gameout .= "<img src='".$g->getValue('screenshot_url')."'><p>";
	$gameout .= "[score over time graph img]<p>";
	$gameout .= "[detailed stats link]<p>";
	$gameout .= "<a href='".$g->getValue('demo_url')."'>demo url</a><br>";
	$gameout .= "</td></tr></table>\n";
}
echo " (",$team1,"-",$team2,")";
echo "</td>";
echo "<td>",$m->getValue('match_date'),"</td></tr>\n";
echo "</table><br>\n";
echo $gameout;
echo "<p>Comments...(name, ip)<br>";
echo "(text, date, time)";
echo "<p><a href='#'>Add Comment</a>";
?>