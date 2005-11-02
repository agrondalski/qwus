<?php
require 'includes.php';
require_once 'login.php' ;

try
{
  $tid = $_REQUEST['tourney_id'];
  $t = new tourney(array('tourney_id'=>$tid));

  $division_id = $_REQUEST['division_id'];
  $div = new division(array('division_id'=>$division_id));

  $match_id = $_REQUEST['match_id'];
  $m = new match(array('match_id'=>$match_id));

  $game_id =  $_REQUEST['game_id'];
  $g = new game(array('game_id'=>$game_id));

  $t1 = new team(array('team_id'=>$m->getValue('team1_id')));
  $t2 = new team(array('team_id'=>$m->getValue('team2_id')));

  $fail = false ;
  $files = $g->getFiles() ;
  if (array_key_exists(util::MVD_DEMO, $files))
    {
      $mvd_file   = basename($files[util::MVD_DEMO]->getValue('url')) ;
      $uploadfile = util::UPLOAD_DIR . $mvd_file ;

      if (!copy($t->getTourneyRoot() . util::SLASH . $g->getFileDirectory() . util::SLASH . $mvd_file, $uploadfile))
	{
	  $fail = true ;
	}
    }
  else
    {
      $fail = true ;
    }

  if (!$fail)
    {
      $g->deleteAll() ;

      // Post to mvdStats.pl page
      echo "<form action='./perl/mvdStats.pl' method=post>";
      echo "<table border=0 cellpadding=4 cellspacing=0>";
      echo "<tr><td><b>Recompute Game</b></td>";
      echo "<input type='hidden' name='tourney_id' value='$tid'>";
      echo "<input type='hidden' name='division_id' value='$tid'>";
      echo "<input type='hidden' name='match_id' value='$match_id'>";
      echo "<input type='hidden' name='filename' value ='$uploadfile'>";
      echo "<input type='hidden' name='team1' value='",$t1->getValue('name_abbr'),"'>";
      echo "<input type='hidden' name='team2' value='",$t2->getValue('name_abbr'),"'>";
      echo "<td><input type='submit' value='Submit' name='B1' class='button'></td>";
      echo "<td>Please be patient, this process could take a few seconds.</td></tr>";
      echo "</table></form>";
    }
  else
    {
      echo "No mvd available.<br>" ;
      echo "<a href='?a=manageGame&amp;tourney_id=$tid&amp;match_id=$match_id'>Manage Match</a>" ;
    }
}
catch(Exception $e){}
