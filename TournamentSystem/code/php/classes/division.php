<?php
class division
{
  private $division_id ;
  private $tourney_id ;
  private $name ;
  private $num_games ;
  private $playoff_spots ;
  private $elim_losses ;

  function __construct($a)
    {
      if (array_key_exists('division_id', $a))
	{
	  $this->division_id = $this->validateColumn($a['division_id'], 'division_id') ;

	  if ($this->getDivisionInfo()==util::NOTFOUND)
	    {
	      util::throwException("No division exists with specified id");
	    }
	  else
	    {
	      return ;
	    }
	}

      foreach($this as $key => $value)
	{
	  $this->$key = $this->validateColumn($a[$key], $key, true) ;
	}

      $sql_str = sprintf("insert into division(tourney_id, name, num_games, playoff_spots, elim_losses)" .
                         "values(%d, '%s', %d, %d, %d)",
			 $this->tourney_id, $this->name, $this->num_games, $this->playoff_spots, $this->elim_losses) ;

      $result = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($GLOBALS['link'])) ;
      $this->division_id = mysql_insert_id() ;
    }

  private function getDivisionInfo()
    {
      $sql_str = sprintf("select tourney_id, name, num_games, playoff_spots, elim_losses from division where division_id=%d", $this->division_id) ;
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($GLOBALS['link']));

      if (mysqli_num_rows($result)!=1)
	{
	  mysqli_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysqli_fetch_row($result) ;

      $this->tourney_id     = $row[0] ;
      $this->name           = $row[1] ;
      $this->num_games      = $row[2] ;
      $this->playoff_spots  = $row[3] ;
      $this->elim_losses    = $row[4] ; 

      mysqli_free_result($result) ;
      return util::FOUND ;
    }

  public function validateColumnName($col)
    {
      foreach($this as $key => $value)
	{
	  if ($col === $key)
	    {
	      return ;
	    }
	}

      util::throwException('invalid column name specified') ;
    }

  public static function validateColumn($val, $col, $cons=false)
    {
      if ($col == 'division_id')
	{
	  if (!$cons)
	    {
	      if (util::isNull($val))
		{
		  util::throwException($col . ' cannot be null') ;
		}

	      if (!is_numeric($val))
		{
		  util::throwException($col . ' is not a numeric value') ;
		}

	      return util::mysql_real_escape_string($val) ;
	    }
	}

      elseif ($col == 'tourney_id')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  if (!is_numeric($val))
	    {
	      util::throwException($col . ' is not a numeric value') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'name')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col = 'num_games')
	{
	  if (!util::isNull($val) && !is_numeric($val))
	    {
	      util::throwException($col . ' is not a numeric value') ;
	    }

	  return util::nvl(util::mysql_real_escape_string($val), 0) ;
	}

      elseif ($col == 'playoff_spots')
	{
	  if (!util::isNull($val) && !is_numeric($val))
	    {
	      util::throwException($col . ' is not a numeric value') ;
	    }

	  return util::nvl(util::mysql_real_escape_string($val), 0) ;
	}

      elseif ($col == 'elim_losses')
	{
	  if (!util::isNull($val) && !is_numeric($val))
	    {
	      util::throwException($col . ' is not a numeric value') ;
	    }

	  return util::nvl(util::mysql_real_escape_string($val), 0) ;
	}

      else
	{
	  util::throwException('invalid column specified') ;
	}
    }

  public function addMatchSchedule($a)
    {
      $a['division_id'] = $this->division_id ;
      $ms = new match_schedule($a) ;
    }

  public function getTeams()
    {
      $sql_str = sprintf("select ti.team_id from tourney_info ti where ti.division_id=%d", $this->division_id) ;
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($GLOBALS['link']));

      while ($row=mysqli_fetch_row($result))
	{
	  $arr[] = new team(array('team_id'=>$row[0])) ;
	}

      mysqli_free_result($result) ;
      return $arr ;
    }

  public function getPlayers()
    {
      $sql_str = sprintf("select pi.player_id
                          from tourney_info ti, player_info pi
                          where ti.division_id=%d and ti.tourney_id=%d and ti.team_id = pi.team_id and pi.tourney_id=ti.tourney_id",
                         $this->division_id, $this->tourney_id) ;
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($GLOBALS['link']));

      while ($row=mysqli_fetch_row($result))
	{
	  $arr[] = new player(array('player_id'=>$row[0])) ;
	}

      mysqli_free_result($result) ;
      return $arr ;
    }

  public function getMatches($team_id)
    {
      if (util::isNull($team_id))
	{
	  $sql_str = sprintf("select m.match_id from match_table m, match_schedule ms where ms.division_id=%d and ms.schedule_id=m.schedule_id", $this->division_id) ;
	}
      else
	{
	  $team_id = team::validateColumn($team_id, 'team_id') ;
	  $sql_str = sprintf("select m.match_id from match_table m, match_schedule ms
                              where ms.division_id=%d and ms.schedule_id=m.schedule_id and (team1_id=%d or team2_id=%d)", $this->division_id, $team_id, $team_id) ;
	}
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($GLOBALS['link']));

      while ($row=mysqli_fetch_row($result))
	{
	  $arr[] = new match(array('match_id'=>$row[0])) ;
	}

      mysqli_free_result($result) ;
      return $arr ;
    }

  public function getApprovedMatches($team_id)
    {
      if (util::isNull($team_id) || !is_numeric($team_id))
	{
	  $sql_str = sprintf("select m.match_id from match_table m, match_schedule ms where ms.division_id=%d and ms.schedule_id=m.schedule_id and m.approved=true", $this->division_id) ;
	}
      else
	{
	  $team_id = team::validateColumn($team_id, 'team_id') ;
	  $sql_str = sprintf("select m.match_id from match_table m, match_schedule ms
                              where ms.division_id=%d and ms.schedule_id=m.schedule_id and m.approved=true and (team1_id=%d or team2_id=%d)", $this->division_id, $team_id, $team_id) ;
	}
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($GLOBALS['link']));

      while ($row=mysqli_fetch_row($result))
	{
	  $arr[] = new match(array('match_id'=>$row[0])) ;
	}

      mysqli_free_result($result) ;
      return $arr ;
    }

  public function getMatchSchedule($a)
    {
      $sql_str = sprintf("select * from match_schedule ms where ms.division_id=%d", $this->division_id) ;
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($GLOBALS['link']));

      $sort = (!util::isNull($a) && is_array($a)) ? true : false ;

      while ($row=mysqli_fetch_assoc($result))
	{
	  if ($sort)
	    {
	      $arr[] = $row ;
	    }
	  else
	    {
	      $arr[] = new match_schedule(array('schedule_id'=>$row['schedule_id'])) ;
	    }
	}

      if ($sort)
	{
	  $sorted_arr = util::row_sort($arr, $a) ;

	  $arr = array() ;
	  foreach($sorted_arr as $row)
	    {
	      $arr[] = new match_schedule(array('schedule_id'=>$row['schedule_id'])) ;
	    }
	}

      mysqli_free_result($result) ;
      return $arr ;
    }

  public function getTourney()
    {
      return new tourney(array('tourney_id'=>$this->tourney_id)) ;
    }

  public function assignTeam($team_id)
    {
      $team_id  = team::validateColumn($team_id, 'team_id') ;
      $tid = $this->getTourney()->getValue('tourney_id') ;

      $sql_str = sprintf("update tourney_info set division_id= values(%d, %d, %d)", $tid, $team_id, $this->division_id) ;
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($GLOBALS['link']));
    }

  public function removeTeam($id)
    {
      $id = team::validateColumn($id, 'team_id') ;
      $tid = $this->getTourney()->getValue('tourney_id') ;

      $sql_str = sprintf("delete from tourney_info where tourney_id=%d and team_id=%d", $tid, $id) ;
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($GLOBALS['link']));
    }

  public function hasTeam($team_id)
    {
      $team_id = team::validateColumn($team_id, 'team_id') ;
      $tid = $this->getTourney()->getValue('tourney_id') ;

      $sql_str = sprintf("select 1 from tourney_info where tourney_id=%d and team_id=%d and division_id=%d", $tid, $team_id, $this->division_id) ;
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysqli_error($GLOBALS['link']));

      if (mysqli_num_rows($result)==1)
	{
	  mysqli_free_result($result) ;
	  return true ;
	}
      else
	{
	  mysqli_free_result($result) ;
	  return false ;
	}
    }

  public function createSchedule($num_weeks)
    {
      $this->removeSchedule() ;

      $num_teams = count($this->getTeams()) ;

      if (util::isNull($num_weeks) || $num_weeks==0 || $num_teams<2)
	{
	  return ;
	}

      $total_games = $num_teams * $this->num_games ;

      if (fmod($total_games,2)==1)
	{
	  $total_games -= $num_teams ;
	}

      $extra_matchups = (fmod($total_games, $num_teams*($num_teams-1)))/$num_teams ;
      $complete_matchups = floor(($total_games - $overflow_matches) / ($num_teams*($num_teams-1))) ;

      $matches = array() ;

      for ($i=0; $i<$num_teams; $i++)
	{
	  $matches[] = array() ;
	}

      for ($i=0; $i<$complete_matchups; $i++)
	{
	  for($j=0; $j<$num_teams; $j++)
	  {
	    for ($k=0; $k<$num_teams; $k++)
	      {
		if ($j<$k)
		  {
		    $matches[$j][] = $k ;
		  }
	      }
	  }
	}

      $rand_array = array() ;
      for ($i=0; $i<($extra_matchups*$num_teams/2); $i++)
	{
	  $k = 0 ;
	  do
	    {
	      $rand_team1 = util::random_integer($num_teams) ;
	      $rand_team2 = util::random_integer($num_teams) ;
	    }
	  while ((util::array_value_count($rand_array, $rand_team1)==($extra_matchups)) ||
		 (util::array_value_count($rand_array, $rand_team2)==($extra_matchups)) ||
		 ($rand_team1 == $rand_team2)) ;

	  $rand_array[] = $rand_team1 ;
	  $rand_array[] = $rand_team2 ;
	  $matches[$rand_team1][] = $rand_team2 ;
	}

      $t = $this->getTeams() ;
      shuffle($t);

      $weeks = array() ;
      for ($i=0; $i<$num_weeks; $i++)
	{
	  $ms = new match_schedule(array('division_id'=>$this->division_id, 'name'=>'week' . ($i+1))) ;
	  $msa[] = $ms ;

	  $weeks[$i] = array() ;  
	}

      $sched = 0;
      for ($i=0; $i<count($matches); $i++)
	{
	  for ($j=0; $j<count($matches[$i]); $j++)
	  {
	    $team1_id = $t[$i]->getValue('team_id') ;
	    $team2_id = $t[$matches[$i][$j]]->getValue('team_id') ;

	    $sched = util::findbestweek($weeks, $sched, $team1_id, $team2_id) ; 
	    $m = new match(array('schedule_id'=>$msa[$sched]->getValue('schedule_id'), 'team1_id'=>$team1_id, 'team2_id'=>$team2_id)) ;

	    $weeks[$sched][] = $team1_id ; 
	    $weeks[$sched][] = $team2_id ; 

	    if (++$sched==count($weeks))
	      {
		$sched = 0 ;
	      }
	  }
	}
    }

  public function getSortedTeamStats($a, $map_id=null)
    {
      if (!util::isNull($map_id) && is_numeric($map_id) && $map_id!=-1)
	{
	  $q['map_id'] = $map_id ;
	}

      $q['tourney_id'] = $this->tourney_id ;
      $q['division_id'] = $this->division_id ;

      $stats = stats_team::getTeamStats($q) ;
      return util::row_sort($stats, $a) ;
    }

  public function getSortedPlayerStats($a, $map_id=null)
    {
      if (!util::isNull($map_id) && is_numeric($map_id) && $map_id!=-1)
	{
	  $q['map_id'] = $map_id ;
	}

      $q['tourney_id'] = $this->tourney_id ;
      $q['division_id'] = $this->division_id ;

      $stats = stats::getPlayerStats($q) ;
      return util::row_sort($stats, $a) ;
    }

  public function removeSchedule()
    {
      $sql_str = sprintf("delete from match_table where schedule_id in (select schedule_id from match_schedule where division_id=%d)", $this->division_id) ;
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS['link']));

      $sql_str = sprintf("delete from match_schedule where division_id=%d", $this->division_id) ;
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS['link']));
    }

  public function getValue($col, $quote_style=ENT_QUOTES)
    {
      $this->validateColumnName($col) ;
      return util::htmlentities($this->$col, $quote_style) ;
    }

  public function update($col, $val)
    {
      $this->$col = $this->validateColumn($val, $col) ;

      if (is_numeric($this->$col))
	{
	  $sql_str = sprintf("update division set %s=%d where division_id=%d", $col, $this->$col, $this->division_id) ;
	}
      else
	{
	  $sql_str = sprintf("update division set %s='%s' where division_id=%d", $col, $this->$col, $this->division_id) ;
	}

      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS['link']));
      $this->$col = $val ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from division where division_id=%d", $this->division_id) ;
      $result  = mysqli_query($GLOBALS['link'], $sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysqli_error($GLOBALS['link']));
    }
}
?>
