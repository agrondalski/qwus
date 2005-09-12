<?php
require_once 'dbConnect.php' ;
?>

<?php
class division
{
  private $division_id ;
  private $tourney_id ;
  private $name ;
  private $max_teams ;
  private $num_games ;
  private $playoff_spots ;
  private $elim_losses ;

  function __construct($a)
    {
      $id = $a['division_id'] ;

      if (isset($id) && is_numeric($id))
	{
	  $this->division_id = $id ;

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

      $sql_str = sprintf("insert into division(tourney_id, name, max_teams, num_games, playoff_spots, elim_losses)" .
                         "values(%d, '%s', %d, %d, %d, %d)",
			 $this->tourney_id, $this->name, $this->max_teams, $this->num_games, $this->playoff_spots, $this->elim_losses) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->division_id = mysql_insert_id() ;
    }

  private function getDivisionInfo()
    {
      $sql_str = sprintf("select tourney_id, name, max_teams, num_games, playoff_spots, elim_losses from division where division_id=%d", $this->division_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->tourney_id     = $row[0] ;
      $this->name           = $row[1] ;
      $this->max_teams      = $row[2] ;
      $this->num_games      = $row[3] ;
      $this->playoff_spots  = $row[4] ;
      $this->elim_losses    = $row[5] ; 

      mysql_free_result($result) ;
      return util::FOUND ;
    }

  public function validateColumnName($col)
    {
      $found ;
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

	      return util::mysql_real_escape_string($val) ;
	    }
	}

      elseif ($col == 'tourney_id')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
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

      elseif ($col == 'max_teams')
	{
	  return util::nvl(util::mysql_real_escape_string($val), 0) ;
	}

      elseif ($col = 'num_games')
	{
	  return util::nvl(util::mysql_real_escape_string($val), 0) ;
	}

      elseif ($col == 'playoff_spots')
	{
	  return util::nvl(util::mysql_real_escape_string($val), 0) ;
	}

      elseif ($col == 'elim_losses')
	{
	  return util::nvl(util::mysql_real_escape_string($val), 0) ;
	}

      else
	{
	  util::throwException('invalid column specified') ;
	}
    }

  public function getTeams()
    {
      $sql_str = sprintf("select di.team_id from division_info di where di.division_id=%d", $this->division_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new team(array('team_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getPlayers()
    {
      $sql_str = sprintf("select pi.player_id from division_info di, player_info pi " .
                         "where di.division_id=%d and di.team_id = pi.team_id and pi.tourney_id=%d",
                         $this->division_id, $this->tourney_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new player(array('player_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getMatches()
    {
      $sql_str = sprintf("select m.match_id from match_table m where m.division_id=%d", $this->division_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new match(array('match_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getTourney()
    {
      return new tourney(array('tourney_id'=>$this->tourney_id)) ;
    }

  public function addTeam($id)
    {
      $id  = team::validateColumn($id, 'team_id') ;

      $sql_str = sprintf("select count(*) from division_info where division_id=%d", $this->division_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      $row = mysql_fetch_row($result) ;
      $val = $row[0] ;

      if ($val>=$this->max_teams)
	{
	  util::throwException('division has too many teams') ;
	}

      $sql_str = sprintf("insert into division_info(division_id, team_id) values(%d, %d)", $this->division_id, $id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($result) ;
    }

  public function removeTeam($id)
    {
      $id = team::validateColumn($id, 'team_id') ;

      $sql_str = sprintf("delete from division_info where division_id=%d and team_id=%d", $this->division_id, $id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($result) ;
    }

  public function hasTeam($id)
    {
      $id = team::validateColumn($id, 'team_id') ;

      $sql_str = sprintf("select 1 from division_info where division_id=%d and team_id=%d", $this->division_id, $id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)==1)
	{
	  mysql_free_result($result) ;
	  return true ;
	}
      else
	{
	  mysql_free_result($result) ;
	  return false ;
	}
    }

  public function createSchedule($num_weeks)
    {
      $num_teams = count($this->getTeams()) ;
      $total_games = $num_teams * $this->num_games ;

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
	  do
	    {
	      $rand_team1 = util::random_integer($num_teams) ;
	      $rand_team2 = util::random_integer($num_teams) ;
	    }
	  while ((util::array_value_count($rand_array, $rand_team1)>($extra_matchups)) ||
		 (util::array_value_count($rand_array, $rand_team2)>($extra_matchups)) ||
		 ($rand_team1 == $rand_team2)) ;

	  $rand_array[] = $rand_team1 ;
	  $rand_array[] = $rand_team2 ;
	  $matches[$rand_team1][] = $rand_team2 ;
	}

      $t = $this->getTeams() ;
      $week_count = 1 ;
      for ($i=0; $i<count($matches); $i++)
	{
	  for ($j=0; $j<count($matches[$i]); $j++)
	  {
	    $x = $matches[$i][$j] ;
	    $team1_id = $t[$i]->getValue('team_id') ;
	    $team2_id = $t[$x]->getValue('team_id') ;

	    $m = new match(array('division_id'=>$this->division_id, 'team1_id'=>$team1_id, 'team2_id'=>$team2_id, 'week_name'=>"week$week_count")) ;

	    if (++$week_count > $num_weeks)
	      {
		$week_count = 1 ;
	      }
	  }
	}
    }

  public function removeSchedule()
    {
      $sql_str = sprintf("delete from comments where match_id in(select match_id from match_table where division_id=%d)", $this->division_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      mysql_free_result($result) ;

      $sql_str = sprintf("delete from stats where game_id in(select game_id from game where match_id in (select match_id from match_table where division_id=%d))", $this->division_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      mysql_free_result($result) ;

      $sql_str = sprintf("delete from game where match_id in (select match_id from match_table where division_id=%d)", $this->division_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      mysql_free_result($result) ;

      $sql_str = sprintf("delete from match_table where division_id=%d", $this->division_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      mysql_free_result($result) ;
    }

  public function getValue($col)
    {
      $this->validateColumnName($col) ;
      return htmlentities($this->$col) ;
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

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      mysql_free_result($result) ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from division where division_id=%d", $this->division_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
      mysql_free_result($result) ;
    }
}
?>
