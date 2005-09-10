<?php
require_once 'dbConnect.php' ;
?>

<?php
class match
{
  private $match_id ;
  private $division_id ;
  private $team1_id ;
  private $team2_id ;
  private $winning_team_id ;
  private $approved ;
  private $match_date ;
  private $deadline ;
  private $week_name ;

  function __construct($a)
    {
      $id = $a['match_id'] ;

      if (isset($id) && is_numeric($id))
	{
	  $this->match_id = $id ;

	  if ($this->getMatchInfo()==util::NOTFOUND)
	    {
	      util::throwException("No match exists with specified id");
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

      $sql_str = sprintf("insert into match_table(division_id, team1_id, team2_id, winning_team_id, approved, match_date, deadline, week_name)" .
                         "values(%d, %d, %d, %s, %d, '%s', '%s', '%s')",
			 $this->division_id, $this->team1_id, $this->team2_id, util::nvl($this->winning_team_id, 'null'), $this->approved, $this->match_date,
			 $this->deadline, $this->week_name) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->match_id = mysql_insert_id() ;
    }

  private function getMatchInfo()
    {
      $sql_str = sprintf("select division_id, team1_id, team2_id, winning_team_id, approved, match_date, deadline, week_name from match_table where match_id=%d", $this->match_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->division_id      = $row[0] ;
      $this->team1_id         = $row[1] ;
      $this->team2_id         = $row[2] ;
      $this->winning_team_id  = $row[3] ; 
      $this->approved         = $row[4] ; 
      $this->match_date       = $row[5] ;
      $this->deadline         = $row[6] ;
      $this->week_name        = $row[7] ;

      mysql_free_result($row) ;

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
      if ($col == 'match_id')
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

      elseif ($col == 'division_id')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'team1_id')
	{
 	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'team2_id')
	{
 	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'winning_team_id')
	{
	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'approved')
	{
	  return util::nvl(util::mysql_real_escape_string($val, false)) ;
	}

      elseif ($col == 'match_date')
	{
	  if (!util::isNull($val) && !util::isValidDate($val))
	    {
	      util::throwException('invalid date specified for ' . $col) ;
	    }

	  return util::nvl(util::mysql_real_escape_string($val), util::DEFAULT_DATE) ;
	}

      elseif ($col == 'deadline')
	{
 	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  if (!util::isValidDate($val))
	    {
	      util::throwException('invalid date specified for ' . $col) ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'week_name')
	{
	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      else
	{
	  util::throwException('invalid column specified') ;
	}
    }

  public function getComments()
    {
      $sql_str = sprintf("select c.comment_id from comments c where c.match_id=%d order by comment_date, comment_time", $this->match_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new comment(array('comment_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getGames()
    {
      $sql_str = sprintf("select g.game_id from game g where g.match_id=%d", $this->match_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new game(array('game_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function getTeams()
    {
      return array(new team(array('team_id'=>$this->team2_id)), new team(array('team_id'=>$this->team1_id))) ;
    }

  public function getWinningTeam()
    {
      return new team(array('team_id'=>$this->winning_team_id)) ;
    }

  public function getDivision()
    {
      return new division(array('division_id'=>$this->division_id)) ;
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
	  $sql_str = sprintf("update match_table set %s=%d where match_id=%d", $col, $this->$col, $this->match_id) ;
	}
      else
	{
	  $sql_str = sprintf("update match_table set %s='%s' where match_id=%d", $col, $this->$col, $this->match_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
    }

  public function delete()
    {
      $sql_str = sprintf("delete from match_table where match_id=%d", $this->match_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
    }
}
?>
