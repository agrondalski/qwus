
<?php
try
{
require 'includes.php';
$tid = $_REQUEST['tourney_id'];
$division_id = $_REQUEST['division_id'];
$sort = $_REQUEST['sort'];

$t = new tourney(array('tourney_id'=>$tid));

try
{
  if ($division_id != "-1") 
  {
    $div = new division(array('division_id'=>$division_id));
  }
}
catch (Exception $e) 
{
  $div = "";
}

if ($sort == "")
{
	$sort = 'frags_per_game';
}

include 'userLinks.php';
echo "<br>";

// Results section

echo "<h2>Statistics</h2>";

if ($division_id == "")
{
	$alldivs = "selected";
	$division_id = "-1";
}
 // Pick a division if you like
echo "<form action='?a=statistics' method=post>";
echo "<table border=0 cellpadding=2 cellspacing=0>";
echo "<tr><td><b>Pick a division:</b></td>";
echo "<input type='hidden' name='tourney_id' value='$tid'>";
echo "<td><select name='division_id'>";
echo "<option value='-1' $alldivs>All divisions";
foreach ($t->getDivisions() as $tmp) 
{
  $sel = "";
  if ($tmp->getValue('division_id') == $division_id) 
  {
    $sel = "selected";
  }
  echo "<option value='",$tmp->getValue('division_id'),"' ",$sel,">",$tmp->getValue('name');
}

echo "</select></td></tr>";
echo "<tr><td>&nbsp;</td><td><input type='submit' value='Okay' name='B1' class='button' $dis>";
echo "<br></td></tr>";
echo "</table></form>";

// Stats table (headers)
echo "<table border=1 cellpadding=4 cellspacing=0>\n";
echo "<tr bgcolor='#999999'>";
echo "<th>#</th>";
echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&amp;sort=name'>Name</a></th>";
echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&amp;sort=team_name'>Team</a></th>";
echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&amp;sort=division_name'>Div</a></th>";
echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&amp;sort=games_played'>GP</a></th>";
echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&amp;sort=frags_per_game'>F/G</a></th>";
echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&amp;sort=total_frags'>Frags</a></th>";
echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&amp;sort=matches_won'>Record with</a></th>";
echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&amp;sort=frag_diff'>+/-</a></th></tr>";

// User division or don't
if ($division_id == "-1") 
{
	$arr = $t->getSortedPlayerInfo(array($sort,SORT_DESC,'frags_per_game', SORT_DESC));
}
else 
{
	$arr = $div->getSortedPlayerInfo(array($sort,SORT_DESC, 'frags_per_game', SORT_DESC));
}
$count = 0;
foreach ($arr as $player)
{
	$count += 1;
	//$loc = new location(array('location_id'=>$player['location_id']));
	//$loc_name = $loc->getValue('country_name').":".$loc->getValue('state_name');
	if ($count % 2 == 1) 
	{
	  $clr = "#CCCCCC";
	}
	else
	{
	  $clr = "#C0C0C0";
	}
	echo "\t<tr bgcolor='$clr'>\n<td nowrap>",$count,"</td>";
	echo "<td nowrap>";
	echo "<a href='?a=detailsPlayer&amp;tourney_id=",$tid,"&amp;team_id=",$player['team_id'],"&amp;player_id=",$player['player_id'],"'>";
	echo $player['name'],"</a></td>\n";
	echo "<td nowrap><a href='?a=detailsTeam&amp;tourney_id=",$tid,"&amp;team_id=",$player['team_id'],"'>",$player['team_name'],"</a></td>";
	echo "<td nowrap>",$player['division_name'],"</td>";
	//echo "\t<td>",$loc_name,"</td>\n";
	echo "<td nowrap>",$player['games_played'],"</td>";
	echo "<td nowrap>",$player['frags_per_game'],"</td>";
	echo "<td nowrap>",$player['total_frags'],"</td>";
	echo "<td nowrap>",$player['matches_won'],"-",$player['matches_lost'],"</td>";
	echo "<td nowrap>",$player['frag_diff'],"</td>";
}
echo "</tr></table>";
}
catch (Exception $e) {print $e;}
?>