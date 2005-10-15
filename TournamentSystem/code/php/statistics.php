<?php
try
{
  require_once 'includes.php';
  include 'userLinks.php';
  echo "<br>";

  $tid = $_REQUEST['tourney_id'];
  $t = new tourney(array('tourney_id'=>$tid));
  
  echo "<h2>Statistics</h2>";

  $page        = $_REQUEST['page'];
  $sort        = $_REQUEST['sort'];
  $division_id = $_REQUEST['division_id'];
  $map_id      = $_REQUEST['map_id'];
  $show_player = $_REQUEST['show_player'] ;

  if (util::isNull($page))
    {
      $page = "main";
    }

  if (util::isNull($show_player))
    {
      $show_player = 1 ;
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

  try
    {
      if ($map_id != "-1") 
	{
	  $map = new map(array('map_id'=>$map_id));
	}
    }
  catch (Exception $e) 
    {
      $map = "";
    }

  if (util::isNull($sort))
    {
      if ($page == "main")
	{
	  if ($show_player==1)
	    {
	      $sort = util::FRAGS_PER_GAME ;
	    }
	  else
	    {
	      $sort = util::AVG_SCORE ;
	    }
	}
      elseif ($page == "frags")
	{
	  $sort = util::TOTAL_FRAGS;
	}
      elseif ($page == "deaths")
	{
	  $sort = util::TOTAL_DEATHS;
	}
      elseif ($page == "bores")
	{
	  $sort = util::SELF_KILLS;
	}		
    }

  if (util::isNull($division_id))
    {
      $alldivs = "selected";
      $division_id = "-1";
    }

  if (util::isNull($map_id))
    {
      $allmaps = "selected";
      $map_id = "-1";
    }

  if ($page == "main")
    {
      $msel = "selected";
    }
  elseif ($page == "frags")
    {
      $fsel = "selected";
    }
  elseif ($page == "deaths")
    {
      $dsel = "selected";
    }
  elseif ($page == "bores")
    {
      $bsel = "selected";
    }

  if ($show_player==1)
    {
      $psel = "selected" ;
    }
  else
    {
      $tsel = "selected" ;
    }

  // Form to determine what to show
  echo "<form action='?a=statistics' method=post>";
  echo "<table border=0 cellpadding=2 cellspacing=0>";
  echo "<input type='hidden' name='tourney_id' value='$tid'>";

  // Show players or teams
  echo "<tr><td><b>Sort:</b></td>";
  echo "<td><select name='show_player'>";
  echo "<option value='1' $psel>Players";
  echo "<option value='2' $tsel>Teams";
  echo "</select></td><td>&nbsp;&nbsp;</td>";

  // Pick a division if you like
  echo "<td><b>Division:</b></td>";
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

  echo "</select></td><td>&nbsp;&nbsp;</td><tr>";

  // Pick the stat type
  echo "<tr><td><b>Type:</b></td>";
  echo "<td><select name='page'>";
  echo "<option value='main'   $msel>Main Stats";
  echo "<option value='frags'  $fsel>Frag Stats";
  echo "<option value='deaths' $dsel>Death Stats";
  echo "<option value='bores'  $bsel>Bore Stats";
  echo "</select></td><td>&nbsp;&nbsp;</td>";

  // Pick a map if you like
  echo "<td><b>Map:</b></td>";
  echo "<td><select name='map_id'>" ;
  echo "<option value='-1' $allmaps>All Maps" ;

  foreach ($t->getMaps(array('map_abbr', SORT_DESC)) as $tmp)
    {
      $sel = "";

      if ($tmp->getValue('map_id') == $map_id)
        {
          $sel = "selected";
        }

      echo "<option value='" . $tmp->getValue('map_id') . "' " . $sel . ">" . $tmp->getValue('map_abbr');
    }
  echo "</select></td>" ;

  echo "<td>&nbsp;</td><td><input type='submit' value='Okay' name='B1' class='button' $dis>";
  echo "<br></td></tr>";
  echo "</table></form>";

  // Stats table (headers)
  echo "<table border=1 cellpadding=3 cellspacing=0>\n";
  echo "<tr bgcolor='#999999'>";
  echo "<th>#</th>";
  if ($show_player==1)
    {
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=name'>Name</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=team_name'>Team</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=games_played'>GP</a></th>";
    }
  else
    {
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=name'>Name</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;sort=games_played'>GP</a></th>";
    }
  
  if ($page == 'main')
    {
      if ($show_player==1)
	{
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::SCORE,"'>Score</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::FRAGS_PER_GAME,"'>AS</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::TOTAL_FRAGS,"'>Frags</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::TOTAL_DEATHS,"'>Deaths</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::EFFICIENCY,"'>Eff</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::GAMES_WON,"'>RW</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::FRAG_DIFF,"'>+/-</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::FRAG_STREAK,"'>FS</a></th>";
	}
      else
	{
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::SCORE,"'>Score</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::AVG_SCORE,"'>F/G</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::TOTAL_FRAGS,"'>Frags</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::TOTAL_DEATHS,"'>Deaths</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::EFFICIENCY,"'>Eff</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::MINUTESPLAYED,"'>MP</a></th>";
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::MINUTESWITHLEAD,"'>MWL</a></th>";
	}
    }

  elseif ($page == 'frags')
    {
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::TOTAL_FRAGS,"'>Frags</a></th>";

      if ($show_plaer==1)
	{
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::FRAGS_PER_GAME,"'>F/G</a></th>";
	}
      else
	{
	  echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::AVG_SCORE,"'>F/G</a></th>";
	}

      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::AX_FRAGS,"'>Axe</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::SG_FRAGS,"'>SG</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::SSG_FRAGS,"'>SSG</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::NG_FRAGS,"'>NG</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::SNG_FRAGS,"'>SNG</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::GL_FRAGS,"'>GL</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::RL_FRAGS,"'>RL</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::LG_FRAGS,"'>LG</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::TELE_FRAGS,"'>Tele</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::DIS_FRAGS,"'>Dis</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::SQ_FRAGS,"'>Squish</a></th>";
    }

  elseif ($page == 'deaths')
    {
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::TOTAL_DEATHS,"'>Deaths</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::AX_DEATHS,"'>Axe</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::SG_DEATHS,"'>SG</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::SSG_DEATHS,"'>SSG</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::NG_DEATHS,"'>NG</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::SNG_DEATHS,"'>SNG</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::GL_DEATHS,"'>GL</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::RL_DEATHS,"'>RL</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::LG_DEATHS,"'>LG</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::TELE_DEATHS,"'>Tele</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::DIS_DEATHS,"'>Dis</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::SQ_DEATHS,"'>Sqsh</a></th>";
  }

  elseif ($page == 'bores')
    {
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::SELF_KILLS,"'>SelfK</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::DIS_BORES,"'>Dis</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::SQ_BORES,"'>Sqsh</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::LAVA_BORES,"'>Lava</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::SLIME_BORES,"'>Slime</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::WATER_BORES,"'>H2O</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::FALL_BORES,"'>Fall</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::MISC_BORES,"'>Misc</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::GL_BORES,"'>GL</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::RL_BORES,"'>RL</a></th>";
      echo "<th><a href='?a=statistics&amp;tourney_id=$tid&amp;division_id=$division_id&page=$page&amp;map_id=$map_id&amp;show_player=$show_player&amp;sort=",util::TEAM_KILLS,"'>TK</a></th>";  
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
  if ($show_player == 1)
    {
      if ($division_id == "-1") 
	{
	  $arr = $t->getSortedPlayerStats(array($sort, $sortOrder, util::FRAGS_PER_GAME, SORT_DESC), $map_id);
	}
      else 
	{
	  $arr = $div->getSortedPlayerStats(array($sort, $sortOrder, util::FRAGS_PER_GAME, SORT_DESC), $map_id);
	}
    }
  else
    {
      if ($division_id == "-1") 
	{
	  $arr = $t->getSortedTeamStats(array($sort, $sortOrder, util::AVG_SCORE, SORT_DESC), $map_id);
	}
      else 
	{
	  $arr = $div->getSortedTeamStats(array($sort, $sortOrder, util::AVG_SCORE, SORT_DESC), $map_id);
	}
    }

  $count = 0;

  foreach ($arr as $player)
    {
      $clr = ++$count%2==1 ? $clr="#CCCCCC" : $clr="#C0C0C0" ;

      echo "\t<tr bgcolor='$clr'>\n<td nowrap>",$count,"</td>";
      echo "<td nowrap>";

      if ($show_player==1)
	{
	  $tm = new team(array('team_id'=>$player['team_id']));

	  echo "<a href='?a=detailsPlayer&amp;tourney_id=",$tid,"&amp;team_id=",$player['team_id'],"&amp;player_id=",$player['player_id'],"'>";
	  echo $player['name'],"</a></td>\n";
	  echo "<td nowrap><a href='?a=detailsTeam&amp;tourney_id=",$tid,"&amp;team_id=",$player['team_id'],"'>",$tm->getValue('name_abbr'),"</a></td>";
	  echo "<td nowrap>",util::nvl($player['games_played'],0),"</td>";
	}
      else
	{
	  echo "<a href='?a=detailsTeam&amp;tourney_id=",$tid,"&amp;team_id=",$player['team_id'],"&amp;team_id=",$player['team_id'],"'>";
	  echo $player['name'],"</a></td>\n";
	  echo "<td nowrap>",util::nvl($player['games_played'],0),"</td>";
	}
     
      //var_dump($player) ;

      if ($page == 'main')
	{  
	  if ($show_player==1)
	    {
	      echo "<td nowrap>",util::nvl($player[util::SCORE],0),"</td>";
	      echo "<td nowrap>",util::nvl($player[util::FRAGS_PER_GAME],0),"</td>";
	      echo "<td nowrap>",util::nvl($player[util::TOTAL_FRAGS],0),"</td>";
	      echo "<td nowrap>",util::nvl($player[util::TOTAL_DEATHS],0),"</td>";
	      echo "<td nowrap>",util::nvl($player[util::EFFICIENCY], 0),"</td>";				
	      echo "<td nowrap>",util::nvl($player[util::GAMES_WON],0),"-",util::nvl($player[util::GAMES_LOST],0),"</td>";
	      echo "<td nowrap>",util::nvl($player[util::FRAG_DIFF],0),"</td>";
	      echo "<td nowrap>",util::nvl($player[util::FRAG_STREAK],0),"</td>";
	    }
	  else
	    {
	      echo "<td nowrap>",util::nvl($player[util::SCORE],0),"</td>";
	      echo "<td nowrap>",util::nvl($player[util::AVG_SCORE],0),"</td>";
	      echo "<td nowrap>",util::nvl($player[util::TOTAL_FRAGS],0),"</td>";
	      echo "<td nowrap>",util::nvl($player[util::TOTAL_DEATHS],0),"</td>";
	      echo "<td nowrap>",util::nvl($player[util::EFFICIENCY], 0),"</td>";				
	      echo "<td nowrap>",util::nvl($player[util::MINUTESPLAYED],0),"</td>";
	      echo "<td nowrap>",util::nvl($player[util::MINUTESWITHLEAD],0),"</td>";
	    }
	}

      elseif ($page == 'frags')
	{
	  echo "<td nowrap>",util::nvl($player[util::TOTAL_FRAGS],0),"</td>";

	  if ($show_player==1)
	    {
	      echo "<td nowrap>",util::nvl($player[util::FRAGS_PER_GAME],0),"</td>";
	    }
	  else
	    {
	      echo "<td nowrap>",util::nvl($player[util::AVG_SCORE],0),"</td>";
	    }

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
	}

      elseif ($page == 'deaths')
	{
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
	}

      elseif ($page == 'bores')
	{
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
