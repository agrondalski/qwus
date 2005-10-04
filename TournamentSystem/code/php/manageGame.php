<?php
require 'includes.php';
require_once 'login.php' ;

try
{
  $tid = $_REQUEST['tourney_id'];
  $match_id = $_REQUEST['match_id'];
  $t = new tourney(array('tourney_id'=>$tid));
  $m = new match(array('match_id'=>$match_id));
  $t1 = new team(array('team_id'=>$m->getValue('team1_id')));
  $t2 = new team(array('team_id'=>$m->getValue('team2_id')));
  $ms = new match_schedule(array('schedule_id'=>$m->getValue('schedule_id')));
  echo "<b>Match Info:</b> ",$t1->getValue('name')," vs ",$t2->getValue('name');
  echo " (",$ms->getValue('name')," ",$ms->getValue('deadline'),")<br>";
  
  try
  {
      $p = new player(array('player_id'=>$_SESSION['user_id'])) ;
  }
  catch(Exception $e) {}

  try
  {
      $tm = new team(array('team_id'=>$_SESSION['team_id'])) ;
  }
  catch(Exception $e) {}

  if (util::isNull($tm) && !$p->isSuperAdmin() && !$p->isTourneyAdmin($t->getValue('tourney_id')))
  {
      util::throwException('not authorized') ;
  }
  
  $mode = $_REQUEST['mode'];

  if ($mode == "edit")
  {
      $game_id = $_REQUEST['game_id'];
      $g = new game(array('game_id'=>$game_id));
      echo "<b><p>Modify a game:</b></p>";      
      $map_id=$g->getValue('map_id');
      $team1_score=$g->getValue('team1_score');
      $team2_score=$g->getValue('team2_score');
  }

  else
  {
      echo "<p><b>Add a game:</b></p>";
      $map_id="";
      $team1_score="";
      $team2_score="";
      $screenshot_url="";
      $demo_url="";
  } 

  echo "<form action='?a=saveGame' method=post>";
  echo "<input type='hidden' name='tourney_id' value='$tid'>";
  echo "<input type='hidden' name='match_id' value='$match_id'>";
  if ($mode == "edit")
  {
      echo "<input type='hidden' name='game_id' value='$game_id'>";
      echo "<input type='hidden' name='mode' value='edit'>";
  }

  echo "<table border=1 cellpadding=2 cellspacing=0>" ;
  echo "<tr>" ;
  echo "<td>Map:</td>";
  echo "<td><select name='map_id' $dis>";
  
    foreach ($t->getMaps() as $tmp) 
    {
        $sel = "";
        if ($tmp->getValue('map_id') == $map_id) 
  	    {
  	      $sel = "selected";
  	    }
  	    echo "<option value='",$tmp->getValue('map_id'),"' ",$sel,">",$tmp->getValue('map_abbr'),":",$tmp->getValue('map_name');
    }
  
  echo "</select></td></tr>";
  echo "<tr align=center>";
  echo "<td width=50%>",$t1->getValue('name')," <br>";
  echo "<input type='text' name='team1_score' value='",$team1_score,"' size='4'></td>";
  echo "<td width=50%>",$t2->getValue('name')," <br>";
  echo "<input type='text' name='team2_score' value='",$team2_score,"' size='4'></td>";
  echo "</tr>";  
  
  
	if ($mode == "edit")
	{
	    $game_id = $_REQUEST['game_id'];
        $g = new game(array('game_id'=>$game_id));
		$i = 1;
		echo "a";
		foreach ($g->getStats() as $p1) 
		{	echo "a";
		    try {
			    //$p1 = new player(array('player_id'=>$tmp->getValue('player_id')));
			}
			catch (Exception $e) {}
			//if ($p1->getValue('player_id') == $m->getValue('team1_id')) 
			//{
			    echo "a";
				echo "<tr><td><select name='t1p",$i,"'>";
				$sel = "";
				if ($p1->getValue('player_id') == $t1p[$i]) 
				{
					$sel = "selected";
				}
				echo "a";
				echo "<option value='",$p1->getValue('name'),"' ",$sel,">",$p1->getValue('player_id');
				echo "</select></td>";
				echo "<td>",$g->getValue('stat_name'),":&nbsp;";
				echo "<input type='text' name't1p",$i,"' value='",$g->getValue('value'),"' size=</td></tr>";
				$i++;
			//}
		}

	}
	else
	{

	}
  //$pc = 1;
  //$i  = 1;
  //for ($i = 1;$i <= 10;$i++;)
  //{
  //echo "<td><input type='text' name't1p",$i,"' value='",$t1p[$i],"' size=
  
  //}
  echo "<tr><td colspan=2><input type='submit' value='Submit' name='B1' class='button'>";
  echo "&nbsp;<input type='reset' value='Reset' name='B2' class='button'></td></tr></table>";
  echo "</p></font>";

  echo "</form>" ;

  include 'listGames.php';
}
catch (Exception $e) {}
?>

