<?php
require_once 'dbConnect.php' ;
?>

<?php
class match
{
  const FOUND    = 1 ;
  const NOTFOUND = 0 ;

  private $match_id ;
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

	  if ($this->getMatchInfo()==self::NOTFOUND)
	    {
	      util::throwException("No match exists with specified id");
	    }
	  else
	    {
	      return ;
	    }
	}

      $this->division_id      = util::mysql_real_escape_string($a['division_id']) ;
      $this->team1_id         = util::mysql_real_escape_string($a['team1_id']) ;
      $this->team2_id         = util::mysql_real_escape_string($a['team2_id']) ;
      $this->winning_team_id  = util::mysql_real_escape_string($a['winning_team_id']) ;
      $this->approved         = util::mysql_real_escape_string($a['approved']) ;
      $this->match_date       = util::mysql_real_escape_string($a['match_date']) ;
      $this->deadline         = util::mysql_real_escape_string($a['deadline']) ;
      $this->week_name        = util::mysql_real_escape_string($a['week_name']) ;

      $sql_str = sprintf("insert into match_table(division_id, team1_id, team2_id, winning_team_id, approved, match_date, deadline, week_name)" .
                         "values(%d, %d, %d, %s, %d, '%s', '%s', '%s')",
			 $this->division_id, $this->team1_id, $this->team2_id, util::nvl($this->winning_team_id), $this->approved, $this->match_date,
			 $this->deadline, $this->week_name) ;

      $result = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->match_id = mysql_insert_id() ;
    }

  private function getMatchInfo()
    {
      $sql_str = sprintf("select division_id, team1_id, team2_id, winning_team_id, approved, match_date, deadline, week_name from match_table where match_id=%d", $this->match_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return self::NOTFOUND ;
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

      return self::FOUND ;
    }

  public function getComments()
    {
      $sql_str = sprintf("select c.comment_id from comments c where c.match_id=%d order by comment_date, comment_time", $this->match_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

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
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

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
      if (! isset($col) || !isset($this->$col))
	{
	  return ;
	}      

      return $this->$col ;
    }

  public function update($col, $val)
    {
      if (! isset($col) || !isset($val) || !isset($this->$col))
	{
	  return ;
	}

      $this->$col = util::mysql_real_escape_string($val) ;

      if (is_numeric($val))
	{
	  $sql_str = sprintf("update match_table set %s=%d where match_id=%d", $col, $this->$col, $this->match_id) ;
	}
      else
	{
	  $sql_str = sprintf("update match_table set %s='%s' where match_id=%d", $col, $this->$col, $this->match_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());
    }

  public function delete()
    {
      $sql_str = sprintf("delete from match_table where match_id=%d", $this->match_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());      
    }
}
?>
