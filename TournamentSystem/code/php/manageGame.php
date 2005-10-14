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
  echo "<b>Match Info:</b> <a href='?a=detailsMatch&amp;tourney_id=$tid&amp;match_id=$match_id'>",$t1->getValue('name')," vs ",$t2->getValue('name');
  echo " (",$ms->getValue('name')," ",$ms->getValue('deadline'),")</a><br>";
  
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
  $game_id = "";
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
  
  
  // FORM

  echo "<form action='?a=saveGame' enctype='multipart/form-data' method=post>";
  echo "<input type='hidden' name='tourney_id' value='$tid'>";
  echo "<input type='hidden' name='match_id' value='$match_id'>";
	
  if ($mode == "edit")
  {
  		echo "<input type='hidden' name='MAX_FILE_SIZE' value='9999999'/>";
  		echo "<b>Screenshot:</b>&nbsp;<b>(320x200 gif, png, jpg please)</b><br><input type='file' name='filename'><p>";
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
  
  
	
  echo "<tr><td colspan=2><input type='submit' value='Submit' name='B1' class='button'>";
  echo "&nbsp;<input type='reset' value='Reset' name='B2' class='button'></td></tr></table>";
  echo "</p></font>";

  echo "</form>" ;
    
  if ($mode == "edit")
	{
		$game_id = $_REQUEST['game_id'];
		$g = new game(array('game_id'=>$game_id));
		// screenshot
		echo "<br><b>Current Screenshot:</b>";
		echo "<table border=1 cellpadding=2 cellspacing=0>";
		echo "<tr><th>URL</th><th>View</th></tr>";
		$ss = $g->getScreenshot();
		if (!util::isNull($ss)) 
		{
			echo "<tr><td>",$ss->getValue('url'),"</td><td>";
			echo "<a target=_blank href='",$ss->getValue('url'),"'>View</a></td></tr>";
		}	
		echo "</table>";		
		
		
		// stats
		echo "<br><b>Stats from current game:</b>";
		echo "<table border=1 cellpadding=2 cellspacing=0>";
		echo "<tr><th>Team</th><th>Player</th><th>Stat</th><th>Value</th></tr>";
		foreach ($g->getStats(array('team_id', SORT_DESC, 'value', SORT_DESC)) as $s) 
		{	
			//echo "a";
			$t = new team(array('team_id'=>$s->getValue('team_id')));
			$p = new player(array('player_id'=>$s->getValue('player_id')));
			//echo "a";
			echo "<tr>";
			echo "<td>",$t->getValue('name_abbr'),"</td>";
			echo "<td>",$p->getValue('name'),"</td>";
			echo "<td>",$s->getValue('stat_name'),"</td>";
			echo "<td>",$s->getValue('value'),"</td>";
			echo "</tr>";			
		}
		echo "</table>";

	}
	else
	{

	}
	echo "<hr>";
	echo "<br><b>Current Games:</b>";
  include 'listGames.php';
}
catch (Exception $e) {}
?>
