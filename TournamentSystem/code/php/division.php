<?php
require_once 'dbConnect.php' ;
?>

<?php
class division
{
  const FOUND    = 1 ;
  const NOTFOUND = 0 ;

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

	  if ($this->getDivisionInfo()==self::NOTFOUND)
	    {
	      util::throwException("No division exists with specified id");
	    }
	  else
	    {
	      return ;
	    }
	}

      util::canNotBeNull($a, 'tourney_id') ;
      util::canNotBeNull($a, 'name') ;

      $this->tourney_id     = util::mysql_real_escape_string($a['tourney_id']) ;
      $this->name           = util::mysql_real_escape_string($a['name']) ;

      $this->max_teams      = util::nvl(util::mysql_real_escape_string($a['max_teams']), 0) ;
      $this->num_games      = util::nvl(util::mysql_real_escape_string($a['num_games']), 0) ;
      $this->playoff_spots  = util::nvl(util::mysql_real_escape_string($a['playoff_spots']), 0) ;
      $this->elim_losses    = util::nvl(util::mysql_real_escape_string($a['elim_losses']), 0) ;

      $sql_str = sprintf("insert into division(tourney_id, name, max_teams, num_games, playoff_spots, elim_losses)" .
                         "values(%d, '%s', %d, %d, %d, %d)",
			 $this->tourney_id, $this->name, $this->max_teams, $this->num_games, $this->playoff_spots, $this->elim_losses) ;

      $result = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . $mysql_error) ;
      $this->division_id = mysql_insert_id() ;
    }

  private function getDivisionInfo()
    {
      $sql_str = sprintf("select tourney_id, name, max_teams, num_games, playoff_spots, elim_losses from division where division_id=%d", $this->division_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return self::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->tourney_id     = $row[0] ;
      $this->name           = $row[1] ;
      $this->max_teams      = $row[2] ;
      $this->num_games      = $row[3] ;
      $this->playoff_spots  = $row[4] ;
      $this->elim_losses    = $row[5] ; 

      mysql_free_result($row) ;
      return self::FOUND ;
    }

  public function getTeams()
    {
      $sql_str = sprintf("select di.team_id from division_info di where di.division_id=%d", $this->division_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

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
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

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
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

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

  public function addTeam($id, $app)
    {
      $sql_str = sprintf("insert into division_info(division_id, team_id, approved) values(%d, %d, %d)", $this->division_id, $id, $app) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($row) ;
    }

  public function removeTeam($id, $app)
    {
      $sql_str = sprintf("delete from division_info where division_id=%d and team_id=%d", $this->division_id, $id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($row) ;
    }

  public function hasTeam($id)
    {
      $sql_str = sprintf("select 1 from division_info where division_id=%d and team_id=%d", $this->division_id, $id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str " . mysql_error());

      if (mysql_num_rows($result)==1)
	{
	  mysql_free_result($row) ;
	  return true ;
	}
      else
	{
	  mysql_free_result($row) ;
	  return false ;
	}
    }

  public function getValue($col)
    {
      if (! isset($col) || !isset($this->$col))
	{
	  return null ;
	}      

      return $this->$col ;
    }

  public function update($col, $val)
    {
      if (!isset($col) || !isset($val))
	{
	  return null ;
	}

      $this->$col = util::mysql_real_escape_string($val) ;

      if (is_numeric($val))
	{
	  $sql_str = sprintf("update division set %s=%d where division_id=%d", $col, $this->$col, $this->division_id) ;
	}
      else
	{
	  $sql_str = sprintf("update division set %s='%s' where division_id=%d", $col, $this->$col, $this->division_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());
    }

  public function delete()
    {
      $sql_str = sprintf("delete from division where division_id=%d", $this->division_id) ;
      $result  = mysql_query($sql_str) or util::throwException("Unable to execute : $sql_str : " . mysql_error());      
    }
}
?>
