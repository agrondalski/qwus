<?php
require_once 'dbConnect.php' ;
?>

<?php
class team
{
  private $team_id ;
  private $name ;
  private $name_abbr ;
  private $email ;
  private $irc_channel ;
  private $location_id ;
  private $password ;
  private $approved ;

  function __construct($a)
    {
      if (array_key_exists('team_id', $a))
	{
	  $this->team_id = $this->validateColumn($a['team_id'], 'team_id') ;

	  if ($this->getTeamInfo()==util::NOTFOUND)
	    {
	      util::throwException("No team exists with specified id");
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

      $sql_str = sprintf("insert into team(name, name_abbr, email, irc_channel, location_id, password, approved)" .
                         "values('%s', '%s', '%s', '%s', %s, '%s', %d)",
			 $this->name, $this->name_abbr, $this->email, $this->irc_channel, util::nvl($this->location_id, 'null'), $this->password, $this->approved) ;

      $result = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . $mysql_error) ;
      $this->team_id = mysql_insert_id() ;
    }

  private function getTeamInfo()
    {
      $sql_str = sprintf("select name, name_abbr, email, irc_channel, location_id, password, approved from team where team_id=%d", $this->team_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str" . mysql_error());

      if (mysql_num_rows($result)!=1)
	{
	  mysql_free_result($result) ;
	  return util::NOTFOUND ;
	}
      $row = mysql_fetch_row($result) ;

      $this->name         = $row[0] ;
      $this->name_abbr    = $row[1] ;
      $this->email        = $row[2] ;
      $this->irc_channel  = $row[3] ;
      $this->location_id  = $row[4] ;
      $this->password     = $row[5] ; 
      $this->approved     = $row[6] ;

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
      if ($col == 'team_id')
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

      elseif ($col == 'name')
	{
 	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'name_abbr')
	{
	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'email')
	{
	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'irc_channel')
	{
	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'location_id')
	{
	  /*
 	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }
	  */

	  if (!util::isNull($val) && !is_numeric($val))
	    {
	      util::throwException($col . ' is not a numeric value') ;
	    }

	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'password')
	{
 	  if (util::isNull($val))
	    {
	      util::throwException($col . ' cannot be null') ;
	    }

	  return md5($val) ;
	}

      elseif ($col == 'approved')
	{
	  return util::mysql_real_escape_string($val) ;
	}

      elseif ($col == 'isTeamLeader')
	{
	  return util::nvl(util::mysql_real_escape_string($val), 0) ;
	}

      elseif ($col == 'wins')
	{
	  if (!is_numeric($val))
	    {
	      util::throwException($col . ' is not a numeric value') ;
	    }

	  return util::nvl(util::mysql_real_escape_string($val), 0) ;
	}

      elseif ($col == 'losses')
	{
	  if (!is_numeric($val))
	    {
	      util::throwException($col . ' is not a numeric value') ;
	    }

	  return util::nvl(util::mysql_real_escape_string($val), 0) ;
	}

      elseif ($col == 'points')
	{
	  if (!is_numeric($val))
	    {
	      util::throwException($col . ' is not a numeric value') ;
	    }

	  return util::nvl(util::mysql_real_escape_string($val), 0) ;
	}

      elseif ($col == 'maps_won')
	{
	  if (!is_numeric($val))
	    {
	      util::throwException($col . ' is not a numeric value') ;
	    }

	  return util::nvl(util::mysql_real_escape_string($val), 0) ;
	}

      elseif ($col == 'maps_lost')
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

  public static function getAllTeams()
    {
      $sql_str = sprintf('select t.team_id from team t') ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      while ($row=mysql_fetch_row($result))
	{
	  $arr[] = new team(array('team_id'=>$row[0])) ;
	}

      mysql_free_result($result) ;
      return $arr ;
    }

  public function passwordMatches($pass)
    {
      if (md5($pass)==$this->password)
	{
	  return true ;
	}
      else
	{
	  return false ;
	}
    }
    
  public function getPlayers($tid)
      {
	$tid = tourney::validateColumn($tid, 'tourney_id') ;

        $sql_str = sprintf("select pi.player_id from player_info pi where pi.tourney_id=%d and pi.team_id=%d", $tid, $this->team_id) ;
        $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());
  
        while ($row=mysql_fetch_row($result))
  	{
  	  $arr[] = new player(array('player_id'=>$row[0])) ;
  	}
  
        mysql_free_result($result) ;
        return $arr ;
    }

  public function addPlayer($tid, $pid, $itl)
    {
      $tid = tourney::validateColumn($tid, 'tourney_id') ;
      $pid = player::validateColumn($pid, 'player_id') ;
      $itl = $this->validateColumn($itl, 'isTeamLeader') ;

      $sql_str = sprintf("select 1 from player_info pi where pi.tourney_id=%d and pi.player_id=%d", $tid, $pid) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());
      
      if ($row=mysql_fetch_row($result))
	{
	  util::throwException('this player is already on a team for the specified tourney') ;
	}

      $sql_str = sprintf("insert into player_info(tourney_id, team_id, player_id, isTeamLeader) values(%d, %d, %d, false)", $tid, $this->team_id, $pid) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());
      
      if ($itl)
	{
	  $this->updateTeamLeader($tid, $pid) ;
	}
      
      mysql_free_result($result) ;
    }

  public function removePlayer($tid, $pid)
    {
      $tid = tourney::validateColumn($tid, 'tourney_id') ;
      $pid = player::validateColumn($pid, 'player_id') ;

      $sql_str = sprintf("delete from player_info where tourney_id=%d and team_id=%d and player_id=%d", $tid, $this->team_id, $pid) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      mysql_free_result($result) ;
    }

  public function hasPlayer($tid, $pid)
    {
      $tid = tourney::validateColumn($tid, 'tourney_id') ;
      $pid = player::validateColumn($pid, 'player_id') ;

      $sql_str = sprintf("select 1 from player_info pi where pi.tourney_id=%d and pi.team_id=%d and pi.player_id=%d", $tid, $this->team_id, $pid) ;
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

  public function getTeamLeader($tid)
    {
      $tid = tourney::validateColumn($tid, 'tourney_id') ;

      $sql_str = sprintf("select pi.player_id from player_info pi where pi.tourney_id=%d and team_id=%d and isTeamLeader=true", $tid, $this->team_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());

      if ($pid = mysql_fetch_row($result))
	{
	  mysql_free_result($result) ;
	  return new player(array('player_id'=>$pid[0])) ;
	}
      else
	{
	  mysql_free_result($result) ;
	  return null;
	}
    }

  public function updateTeamLeader($tid, $pid)
    {
      $tid = tourney::validateColumn($tid, 'tourney_id') ;
      $pid = player::validateColumn($pid, 'player_id') ;

      $sql_str = sprintf("update player_info pi set pi.isTeamLeader=(case when pi.player_id=%d then 1 else 0 end) where pi.tourney_id=%d and pi.team_id=%d", $pid, $tid, $this->team_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str " . mysql_error());
    }

  public function getLocation()
    {
      return new location(array('location_id'=>$this->location_id)) ;
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
	  $sql_str = sprintf("update team set %s=%d where team_id=%d", $col, $this->$col, $this->team_id) ;
	}
      else
	{
	  $sql_str = sprintf("update team set %s='%s' where team_id=%d", $col, $this->$col, $this->team_id) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
    }

  public function getDivisionInfo($div)
    {
      $div = division::validateColumn($div, 'division_id') ;

      /*
      $sql_str = sprintf("select wins, losses, points, maps_won, maps_lost from division_info where team_id=%d and division_id=%d", $this->team_id, $div) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      $arr = array() ;
      if ($row = mysql_fetch_row($result))
	{
	  $arr['wins']      = $row[0] ;
	  $arr['losses']    = $row[1] ;
	  $arr['points']    = $row[2] ;
	  $arr['maps_won']  = $row[3] ;
	  $arr['maps_lost'] = $row[4] ;
	}
      mysql_free_result($result) ;
      */

      $sql_str = sprintf("select wins, losses, points, maps_won, maps_lost from division_info where team_id=%d and division_id=%d", $this->team_id, $div) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      $arr = array() ;
      if ($row = mysql_fetch_row($result))
	{
	}
      mysql_free_result($result) ;

      $sql_str = sprintf("select max(s.score), min(s.score), avg(s.score), sum(s.score), sum(s.other)
                          from (select g.team1_score score, g.team2_score other from match_schedule ms, match_table m, game g
                                where ms.division_id=%d and ms.schedule_id=m.schedule_id and m.team1_id=%d and m.approved=true and m.match_id=g.match_id
                               union all
                                select g.team2_score score, g.team1_score other from match_schedule ms, match_table m, game g
                                where ms.division_id=%d and ms.schedule_id=m.schedule_id and m.team2_id=%d and m.approved=true and m.match_id=g.match_id) s",
			 $div, $this->team_id, $div, $this->team_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      if ($row = mysql_fetch_row($result))
	{
	  $arr['max_score'] = $row[0] ;
	  $arr['min_score'] = $row[1] ;
	  $arr['avg_score'] = $row[2] ;
	  $arr['frags_for'] = $row[3] ;
	  $arr['frags_against'] = $row[4] ;
	}
      mysql_free_result($result) ;

      $sql_str = sprintf("select winning_team_id,
                                (select count(*) from game g
                                 where g.match_id=m.match_id and
                                       (m.team1_id=%d and g.team1_score>g.team2_score or
                                        m.team2_id=%d and g.team2_score>g.team1_score)) games_won,
                                (select count(*) from game g
                                 where g.match_id=m.match_id) total_games
                          from match_schedule ms, match_table m
                          where ms.division_id=%d and ms.schedule_id=m.schedule_id and m.approved=true and %d in(m.team1_id, m.team2_id)
                           order by match_date desc, match_id desc",
			 $this->team_id, $this->team_id, $div, $this->team_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());

      $total_wins      = 0 ;
      $total_losses    = 0 ;
      $total_maps_won  = 0 ;
      $total_maps_lost = 0 ;
      $winning_streak  = 0 ;
      $losing_streak   = 0 ;

      while ($row = mysql_fetch_row($result))
	{
	  $maps_won  = $row[1] ;
	  $maps_lost = $row[2] - $maps_won ;

	  $total_maps_won  += $maps_won ;
	  $total_maps_lost += $maps_lost ;

	  if ($row[0] == $this->team_id)
	    {
	      $total_wins += 1 ;
	      if ($losing_streak==0)
		{
		  $winning_streak += 1;
		}
	    }
	  else
	    {
	      $total_losses += 1 ;
	      if ($winning_streak==0)
		{
		  $losing_streak += 1;
		}
	    }

	  $arr_idx = 'match_' . $maps_won . '-' . $maps_lost ;

	  if (!util::isNull($arr[$arr_idx]))
	    {
	      $arr[$arr_idx] += 1 ;
	    }
	  else
	    {
	      $arr[$arr_idx] = 1 ;
	    }
	}

      $arr['wins']      = $total_wins ;
      $arr['losses']    = $total_losses ;
      $arr['points']    = 0 ;
      $arr['maps_won']  = $total_maps_won ;
      $arr['maps_lost'] = $total_maps_lost ;
      
      if ($winning_streak>0)
	{
	  $arr['winning_streak'] = $winning_streak ;
	}
      elseif($losing_streak>0)
	{
	  $arr['losing_streak'] = $losing_streak ;
	}
      mysql_free_result($result) ;

      return $arr ;
    }

  public function updateInfo($col, $val, $div)
    {
      $div = division::validateColumn($div, 'division_id') ;
      $val = $this->validateColumn($val, $col) ;

      if (is_numeric($this->$col))
	{
	  $sql_str = sprintf("update division_info set %s=%d where team_id=%d and division_id=%d", $col, $val, $this->team_id, $div) ;
	}
      else
	{
	  $sql_str = sprintf("update division_info set %s='%s' where team_id=%d and division_id=%d", $col, $val, $this->team_id, $div) ;
	}

      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());
      mysql_free_result($result) ;
    }

  public function delete()
    {
      $sql_str = sprintf("delete from team where team_id=%d", $this->team_id) ;
      $result  = mysql_query($sql_str) or util::throwSQLException("Unable to execute : $sql_str : " . mysql_error());      
      mysql_free_result($result) ;
    }
}
?>
