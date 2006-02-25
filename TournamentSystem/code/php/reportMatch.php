<?php
require 'includes.php';
require_once 'login.php';

try
{
  $tid = $_REQUEST['tourney_id'];
  $t = new tourney(array('tourney_id'=>$tid));

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

  if (!util::isNull($tm))
    {
      echo '<br>Matches currently have to be reported by an admin.' ;
      util::throwException('not authorized') ;
    }

  $division_id = $_REQUEST['division_id'];
  $match_id    = $_REQUEST['match_id'];

  if ($_REQUEST['approved_step'])
    {
      $approved = $_REQUEST['approved'];

      if (util::isNull($approved))
	{
	  $approved = 0 ;
	}
    }


  $t = new tourney(array('tourney_id'=>$tid));

  try 
    {
      $div = new division(array('division_id'=>$division_id));
    } 
  catch (Exception $e) 
    {
      $div = null;
    }

  try 
    {
      $m = new match(array('match_id'=>$match_id));
    } 
  catch (Exception $e) 
    {
      $m = null;
    }
  
  echo "<br>";

  // *** PART 1
  echo "<h2>Report a Match</h2>";

  // Pick a division
  echo "<form action='?a=reportMatch' method=post>";
  echo "<table border=0 cellpadding=2 cellspacing=0>";
  echo "<tr><td><b>Pick a division:</b></td>";
  echo "<input type='hidden' name='tourney_id' value='$tid'>";
  if (!util::isNull($div))
    {
      $dis = "disabled";
    }

  echo "<td><select name='division_id' $dis>";

  foreach ($t->getDivisions() as $tmp) 
    {
      $sel = null;
      if ($tmp->getValue('division_id') == $division_id) 
	{
	  $sel = "selected";
	}

	echo "<option value='",$tmp->getValue('division_id'),"' ",$sel,">",$tmp->getValue('name');
    }

  echo "</select></td></tr>";
  echo "<tr><td>&nbsp;</td><td><input type='submit' value='Okay' name='B1' class='button' $dis>";
  echo "<br></td></tr>";
  echo "</table></form>";

  $dis = null;

  // *** PART 2
  if (!util::isNull($div))
    {
      if (!util::isNull($match_id))
	{
	  $dis = "disabled";
	}

      echo "<form action='?a=reportMatch' method=post>";
      echo "<table border=0 cellpadding=2 cellspacing=0>";
      echo "<tr><td><b>Pick a match:</b></td>";
      echo "<input type='hidden' name='tourney_id' value='$tid'>";
      echo "<input type='hidden' name='division_id' value='$division_id'>";
      echo "<td><select name='match_id' $dis>";

      foreach($div->getMatchSchedule(array('name', SORT_ASC)) as $ms)
	{
	  if (!util::isNull($tm))
	    {
	      $matches = $ms->getMatches($tm->getValue('team_id')) ;
	    }
	  else
	    {
	      $matches = $ms->getMatches() ;
	    }

	  foreach ($matches as $tmp)
	    {
	      $teams = $tmp->getTeams() ;
	      $t1 = $teams[0] ;
	      $t2 = $teams[1] ;
	      $sel = null;
	  
	      if ($tmp->getValue('match_id') == $match_id) 
		{
		  $sel = "selected";
		}

	      echo "<option value='" . $tmp->getValue('match_id') . "' " . $sel . ">" . $t1->getValue('name') . " vs " . $t2->getValue('name') . " (" . $ms->getValue('name') . ")";
	    }	  
	}

      echo "</select></td></tr>";
      echo "<tr><td>&nbsp;</td><td><input type='submit' value='Okay' name='B1' class='button' $dis>";
      echo "<br></td></tr>";
      echo "</table></form>";
    }

  echo "<hr>";
  $dis = null;

  // *** PART 3
  if ((!util::isNull($match_id)) && (!util::isNull($div)))
    {
      if (util::isLoggedInAsTeam())
	{
	  // Check if match is approved to disable dropdown + button
	  $dis = "disabled";
	  $m = new match(array('match_id'=>$match_id));
	}
      else
	{
	  $dis = null;
	}
      
      if ($approved == "1")
	{
	  $checked = "checked";
	}
      else 
	{
	  // First time here, so check the db
	  if (util::isNull($approved))
	    {
	      $m = new match(array('match_id'=>$match_id));
	      if ($m->getValue('approved') == "1")
		{
		  $checked = "checked";
		}
	      else
		{
		  $checked = null;
		}
	    }
	  else
	    {
	      $checked = null;
	    }
	}

      echo "<h2>Match Details</h2>";
      echo "<form action='?a=reportMatch' method=post>";
      echo "<table border=0 cellpadding=2 cellspacing=0>";
      echo "<input type='hidden' name='tourney_id' value='$tid'>";
      echo "<input type='hidden' name='division_id' value='$division_id'>";
      echo "<input type='hidden' name='match_id' value='$match_id'>";

      // $m is the match object
      $teams = $m->getTeams() ;
      $t1 = $teams[0] ;
      $t2 = $teams[1] ;

      if (!util::isNull($approved))
	{
	  $disableMatchChange = "disabled" ;
	}

      echo "<tr><td><b>Match Approved?</b></td>";
      echo "<td><input type='checkbox' name='approved' value='1' $dis $checked $disableMatchChange></td></tr>";
      echo "<input type='hidden' name='approved_step' value='1'>";
      echo "<tr><td>&nbsp;</td><td><input type='submit' value='Okay' name='B1' class='button' $disableMatchChange>";
      echo "<br></td></tr>";
      echo "</table></form>";
    }

  // *** PART 4
  if ((!util::isNull($approved)) && (!util::isNull($match_id)) && (!util::isNull($div)))
    {
      // Try to save the match
      $m = new match(array('match_id'=>$match_id));

      if (!util::isLoggedInAsTeam())
	{
	  if ($_REQUEST['approved'] == "1")
	    {
	      $m->update('approved',"1");
	    }
	  else
	    {
	      $m->update('approved',"0");
	    }
	}
      echo "<hr>";
      echo "<h2>Add Game Data</h2>";
      
      // Post to mvdStats.pl page
      echo "<form action='?a=reportMatch' enctype='multipart/form-data' method=post>";
      echo "<table border=0 cellpadding=4 cellspacing=0>";
      echo "<tr><td><b>Add game MVD:</b></td>";
      echo "<input type='hidden' name='tourney_id' value='$tid'>";
      echo "<input type='hidden' name='division_id' value='$division_id'>";
      echo "<input type='hidden' name='match_id' value='$match_id'>";
      echo "<input type='hidden' name='approved' value='$approved'>";
      echo "<input type='hidden' name='approved_step' value='1'>";
      echo "<input type='hidden' name='MAX_FILE_SIZE' value='9999999'/>";
      echo "<td><input type='file' name='filename'></td>";
      echo "<td><input type='submit' value='Submit' name='B1' class='button'></td></tr>";
      echo "</form>";

      // Get a file from the local file server
      if ($p->isSuperAdmin())
	{
	  echo "<tr><td colspan=3 align=center><b>OR</b></td></tr>";
	  echo "<form action='?a=reportMatch' method=post>";
	  echo "<tr><td><b>Add local file:</b></td>";
	  echo "<input type='hidden' name='tourney_id' value='$tid'>";
	  echo "<input type='hidden' name='division_id' value='$division_id'>";
	  echo "<input type='hidden' name='match_id' value='$match_id'>";
	  echo "<input type='hidden' name='approved' value='$approved'>";
	  echo "<input type='hidden' name='approved_step' value='1'>";
	  echo "<input type='hidden' name='local_file' value='1'>";
	  echo "<td><input type='text'   name='filename' maxlength='250' value='' size='25'></td>";
	  echo "<td><input type='submit' value='Submit' name='B1' class='button'></td></tr>";
	  echo "</form>";
	}
      
      // Post to reportGames
      echo "<tr><td colspan=3 align=center><b>OR</b></td></tr>";
      echo "<form action='?a=manageGame' method=post>";
      echo "<input type='hidden' name='tourney_id' value='$tid'>";
      echo "<input type='hidden' name='division_id' value='$division_id'>";
      echo "<input type='hidden' name='match_id' value='$match_id'>";
      echo "<tr><td nowrap colspan=2><b>Manually add a game:</b></td>";
      echo "<td><input type='submit' value='Okay' name='B1' class='button'></td></tr>";
      echo "</table></form>";
    }

  // *** PART 5
  if (($_FILES['filename']['size'] != 0) && (!util::isNull($approved)) && (!util::isNull($match_id)) && (!util::isNull($div)))
    {
      print 6;
      $uploadfile = util::UPLOAD_DIR . basename($_FILES['filename']['name']);

      if (is_uploaded_file($_FILES['filename']['tmp_name']))
	{
	  echo "File ". $_FILES['filename']['name'] ." uploaded.\n";
	}
      else
	{
	  echo "File Error on upload: ";
	  echo "filename '". $_FILES['filename']['tmp_name'] . "'.";
	}
		
      if(!preg_match("/.gz$|.mvd$/i", $_FILES['filename']['name'])) 
	{
	  echo("You cannot upload this type of file.  It must be an <b>mvd or gz</b> file.");
	  util::throwException('invalid file') ;
	}

      if (move_uploaded_file($_FILES['filename']['tmp_name'], $uploadfile))
	{
	  echo "File was successfully moved for processing.\n";
	} 
      else 
	{
	  echo "Moving the file Failed!\n";
	  util::throwException('file move failed') ;
	}
		
      $m = new match(array('match_id'=>$match_id));
      $t1 = new team(array('team_id'=>$m->getValue('team1_id')));
      $t2 = new team(array('team_id'=>$m->getValue('team2_id')));

      echo "<hr>";
      echo "<h2>Process your Demo</h2>";

      // Post to mvdStats.pl page
      echo "<form action='./perl/mvdStats.pl' method=post>";
      echo "<table border=0 cellpadding=4 cellspacing=0>";
      echo "<tr><td><b>Make it happen:</b></td>";
      echo "<input type='hidden' name='tourney_id' value='$tid'>";
      echo "<input type='hidden' name='division_id' value='$division_id'>";
      echo "<input type='hidden' name='match_id' value='$match_id'>";
      echo "<input type='hidden' name='approved' value='$approved'>";
      echo "<input type='hidden' name='filename' value ='$uploadfile'>";
      echo "<input type='hidden' name='team1' value='",$t1->getValue('name_abbr'),"'>";
      echo "<input type='hidden' name='team2' value='",$t2->getValue('name_abbr'),"'>";
      echo "<td><input type='submit' value='Submit' name='B1' class='button'></td>";
      echo "<td>Please be patient, this process could take a few seconds.</td></tr>";
      echo "</table></form>";
    }

  if ($_REQUEST['local_file']==1 && $p->isSuperAdmin())
    {
      $localfile = util::UPLOAD_DIR . basename($_REQUEST['filename']) ;

      if(!preg_match("/.gz$|.mvd$/i", $localfile))
	{
	  echo("You cannot upload this type of file.  It must be an <b>mvd or gz</b> file.");
	  util::throwException('invalid file') ;
	}

      if (copy($_REQUEST['filename'], $localfile))
	{
	  echo "File was successfully moved for processing.\n";
	} 
      else 
	{
	  echo "Moving the file Failed!\n";
	  util::throwException('file move failed') ;
	}
		
      $m = new match(array('match_id'=>$match_id));
      $t1 = new team(array('team_id'=>$m->getValue('team1_id')));
      $t2 = new team(array('team_id'=>$m->getValue('team2_id')));

      echo "<hr>";
      echo "<h2>Process your Demo</h2>";

      // Post to mvdStats.pl page
      echo "<form action='./perl/mvdStats.pl' method=post>";
      echo "<table border=0 cellpadding=4 cellspacing=0>";
      echo "<tr><td><b>Make it happen:</b></td>";
      echo "<input type='hidden' name='tourney_id' value='$tid'>";
      echo "<input type='hidden' name='division_id' value='$division_id'>";
      echo "<input type='hidden' name='match_id' value='$match_id'>";
      echo "<input type='hidden' name='approved' value='$approved'>";
      echo "<input type='hidden' name='filename' value ='$localfile'>";
      echo "<input type='hidden' name='team1' value='",$t1->getValue('name_abbr'),"'>";
      echo "<input type='hidden' name='team2' value='",$t2->getValue('name_abbr'),"'>";
      echo "<td><input type='submit' value='Submit' name='B1' class='button'></td>";
      echo "<td>Please be patient, this process could take a few seconds.</td></tr>";
      echo "</table></form>";
    }
}
catch (Exception $e) {}
?>
