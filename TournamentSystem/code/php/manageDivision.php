<?php
require 'includes.php';
require_once 'login.php' ;

try
{
  $tid = $_REQUEST['tourney_id'];
  $t = new tourney(array('tourney_id'=>$tid)) ;
  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  if (!$p->isSuperAdmin() && $p->isTourneyAdmin($t->getValue('tourney_id')))
    {
      util::throwException('not authorized') ;
    }

  $mode = $_REQUEST['mode'];

  if ($mode == "edit")
    {
      // Create the new division
      $did = $_REQUEST['did'];
      $div = new division(array('division_id'=>$did));

      echo "<b><p>Modify a division:</b></p>";      
      $name=$div->getValue('name');
      $num_games=$div->getValue('num_games');
      $playoff_spots=$div->getValue('playoff_spots');
      $elim_losses=$div->getValue('elim_losses');
    }

  else
    {
      echo "<p><b>Create a division for tourney:</b></p>";
      $name="";
      $num_games="";
      $playoff_spots="";
      $elim_losses="";
    } 
  
  echo "<form action='?a=saveDivision' method=post>";
  echo "<input type='hidden' name='tourney_id' value='$tid'>";
  if ($mode == "edit")
    {
      echo "<input type='hidden' name='did' value='$did'>";
      echo "<input type='hidden' name='mode' value='edit'>";
    }

  echo "<table border=1 cellpadding=2 cellspacing=0>" ;
  echo "<tr>" ;
  echo "<td>division name:</td><td>" ;

  echo "<input type='text' name='name' maxlength='50' value='",$name,"' size='20'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td># of Games:</td><td>";
  echo "<input type='text' name='num_games' value='",$num_games,"' size='4'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>Playoff Spots:</td><td>";
  echo "<input type='text' name='playoff_spots' value='",$playoff_spots,"' size='4'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>Elim Losses:</td><td>";
  echo "<input type='text' name='elim_losses' value='",$elim_losses,"' size='4'></td>";
  echo "</tr>";
  echo "<tr><td>&nbsp;</td><td><input type='submit' value='Submit' name='B1' class='button'>";
  echo "&nbsp;<input type='reset' value='Reset' name='B2' class='button'></td></tr></table>";
  echo "</p></font>";

  echo "</form>" ;

  include 'listDivisions.php';
}
catch (Exception $e) {}
?>

