<?php
try
{
  require_once 'includes.php';
  include 'userLinks.php';
  echo "<br>";

  $tid = $_REQUEST['tourney_id'];
  $t = new tourney(array('tourney_id'=>$tid));
  
  $page = $_REQUEST['page'];
  $division_id = $_REQUEST['division_id'];
  $sort = $_REQUEST['sort'];
  
  if ($page == "") 
  {
  	$page = "main";
  }

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
			if ($page == "main") {
				$sort = 'frags_per_game';				
			} elseif ($page == "frags") {
				$sort = util::TOTAL_FRAGS;
			} elseif ($page == "deaths") {
				$sort = util::TOTAL_DEATHS;
			} elseif ($page == "bores") {
				$sort = util::SELF_KILLS;
			}		
    }

  echo "<h2>Statistics</h2>";


  if ($division_id == "")
    {
      $alldivs = "selected";
      $division_id = "-1";
    }

  if ($page == "main") {
		$msel = "selected";
	} elseif ($page == "frags") {
		$fsel = "selected";
	} elseif ($page == "deaths") {
		$dsel = "selected";
	} elseif ($page == "bores") {
		$bsel = "selected";
	}

  // Pick a division if you like
  echo "<form action='?a=statistics' method=post>";
  echo "<table border=0 cellpadding=2 cellspacing=0>";
  echo "<tr><td><b>Division:</b></td>";
  echo "<input type='hidden' name='tourney_id' value='$tid'>";
  echo "<td><select name='division_id'>";
  echo "<option value='-1' $alldivs>All divisions";

  foreach ($t->getDivisions(array('name', SORT_ASC)) as $tmp) 
    {
      $sel = "";
      if ($tmp->getValue('division_id') == $division_id) 
	{
	  $sel = "selected";
	}
      echo "<option value='",$tmp->getValue('division_id'),"' ",$sel,">",$tmp->getValue('name');
    }

  echo "</select></td><td>&nbsp;&nbsp;</td>";
  echo "<td><b>Type:</b></td>";
  echo "<td><select name='page'>";
  echo "<option value='main'   $msel>Main Stats";
  echo "<option value='frags'  $fsel>Frag Stats";
  echo "<option value='deaths' $dsel>Death Stats";
  echo "<option value='bores'  $bsel>Bore Stats";
  echo "</select></td>";
  echo "<td>&nbsp;&nbsp;</td>";
  echo "<td>&nbsp;</td><td><input type='submit' value='Okay' name='B1' class='button' $dis>";
  echo "<br></td></tr>";
  echo "</table></form>";

  // Stats table (headers)
  echo "<table border=1 cellpadding=3 cellspacing=0>\n";
  echo "<tr bgcolor='#999999'>";
  echo "<th>#</th>";
  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=name'>Name</a></th>";
  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=team_name'>Team</a></th>";
  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=games_played'>GP</a></th>";

  if ($page == 'main')
  {  
    echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::TOTAL_FRAGS,"'>Frags</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=frags_per_game'>F/G</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=Efficiency'>Eff</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::TOTAL_DEATHS,"'>Deaths</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::SCORE,"'>Score</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=games_won'>Record with</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=frag_diff'>+/-</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::FRAG_STREAK,"'>FStreak</a></th>";
  
  } elseif ($page == 'frags') {
    echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::TOTAL_FRAGS,"'>Frags</a></th>";
  	echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=frags_per_game'>F/G</a></th>";
  	echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::AX_FRAGS,"'>Axe</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::SG_FRAGS,"'>SG</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::SSG_FRAGS,"'>SSG</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::NG_FRAGS,"'>NG</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::SNG_FRAGS,"'>SNG</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::GL_FRAGS,"'>GL</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::RL_FRAGS,"'>RL</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::LG_FRAGS,"'>LG</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::TELE_FRAGS,"'>Tele</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::DIS_FRAGS,"'>Dis</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::SQ_FRAGS,"'>Squish</a></th>";
	  
  } elseif ($page == 'deaths') {
    echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::TOTAL_DEATHS,"'>Deaths</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::AX_DEATHS,"'>Axe</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::SG_DEATHS,"'>SG</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::SSG_DEATHS,"'>SSG</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::NG_DEATHS,"'>NG</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::SNG_DEATHS,"'>SNG</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::GL_DEATHS,"'>GL</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::RL_DEATHS,"'>RL</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::LG_DEATHS,"'>LG</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::TELE_DEATHS,"'>Tele</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::DIS_DEATHS,"'>Dis</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::SQ_DEATHS,"'>Sqsh</a></th>";
		
  } elseif ($page == 'bores') {
    echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::SELF_KILLS,"'>SelfK</a></th>";
    echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::DIS_BORES,"'>Dis</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::SQ_BORES,"'>Sqsh</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::LAVA_BORES,"'>Lava</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::SLIME_BORES,"'>Slime</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::WATER_BORES,"'>H2O</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::FALL_BORES,"'>Fall</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::MISC_BORES,"'>Misc</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::GL_BORES,"'>GL</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::RL_BORES,"'>RL</a></th>";
		echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=",util::TEAM_KILLS,"'>TK</a></th>";  
  }
  echo "</tr>";
  

  if ($sort == "name" || $sort==team_name) 
    {
      $sortOrder = SORT_ASC;
    }
  else 
    {
      $sortOrder = SORT_DESC;
    }

  // User division or don't
  if ($division_id == "-1") 
    {
      $arr = $t->getSortedPlayerStats(array($sort, $sortOrder,'frags_per_game', SORT_DESC));
    }
  else 
    {
      $arr = $div->getSortedPlayerStats(array($sort, $sortOrder, 'frags_per_game', SORT_DESC));
    }

  $count = 0;
  //var_dump($arr);
  foreach ($arr as $player)
    {
      $count += 1;
      if ($count % 2 == 1) 
	{
	  $clr = "#CCCCCC";
	}
      else
	{
	  $clr = "#C0C0C0";
	}
	$tm = new team(array('team_id'=>$player['team_id']));

      echo "\t<tr bgcolor='$clr'>\n<td nowrap>",$count,"</td>";
      echo "<td nowrap>";
      echo "<a href='?a=detailsPlayer&amp;tourney_id=",$tid,"&amp;team_id=",$player['team_id'],"&amp;player_id=",$player['player_id'],"'>";
      echo $player['name'],"</a></td>\n";
      echo "<td nowrap><a href='?a=detailsTeam&amp;tourney_id=",$tid,"&amp;team_id=",$player['team_id'],"'>",$tm->getValue('name_abbr'),"</a></td>";
      //echo "<td nowrap>",$player['division_name'],"</td>";
      echo "<td nowrap>",$player['games_played'],"</td>";
     
			if ($page == 'main')
			{  
			  echo "<td nowrap>",util::nvl($player[util::TOTAL_FRAGS],0),"</td>";
				echo "<td nowrap>",$player['frags_per_game'],"</td>";
				echo "<td nowrap>",util::nvl($player['Efficiency'], 0),"</td>";				
				echo "<td nowrap>",util::nvl($player[util::TOTAL_DEATHS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::SCORE],0),"</td>";
				echo "<td nowrap>",$player['games_won'],"-",$player['games_lost'],"</td>";
				echo "<td nowrap>",$player['frag_diff'],"</td>";
				echo "<td nowrap>",util::nvl($player[util::FRAG_STREAK],0),"</td>";
			} elseif ($page == 'frags') {
			  echo "<td nowrap>",util::nvl($player[util::TOTAL_FRAGS],0),"</td>";
				echo "<td nowrap>",$player['frags_per_game'],"</td>";
				echo "<td nowrap>",util::nvl($player[util::AX_FRAGS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::SG_FRAGS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::SSG_FRAGS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::NG_FRAGS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::SNG_FRAGS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::GL_FRAGS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::RL_FRAGS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::LG_FRAGS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::TELE_FRAGS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::DIS_FRAGS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::SQ_FRAGS],0),"</td>";				
			} elseif ($page == 'deaths') {
			  echo "<td nowrap>",util::nvl($player[util::TOTAL_DEATHS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::AX_DEATHS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::SG_DEATHS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::SSG_DEATHS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::NG_DEATHS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::SNG_DEATHS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::GL_DEATHS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::RL_DEATHS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::LG_DEATHS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::TELE_DEATHS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::DIS_DEATHS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::SQ_DEATHS],0),"</td>";				
			} elseif ($page == 'bores') {
			  echo "<td nowrap>",util::nvl($player[util::SELF_KILLS],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::DIS_BORES],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::SQ_BORES],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::LAVA_BORES],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::SLIME_BORES],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::WATER_BORES],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::FALL_BORES],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::MISC_BORES],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::GL_BORES],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::RL_BORES],0),"</td>";
				echo "<td nowrap>",util::nvl($player[util::TEAM_KILLS],0),"</td>";
			}

    }

  echo "</tr></table>";
}

catch (Exception $e) {}
?>
