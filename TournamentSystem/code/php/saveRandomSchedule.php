<?php

require 'includes.php';
require_once 'login.php';

try
{
  $tid = $_REQUEST['tourney_id'];

  $t = new tourney(array('tourney_id'=>$tid));
  $p = new player(array('player_id'=>$_SESSION['user_id'])) ;

  if (!$p->isSuperAdmin() && !$p->isTourneyAdmin($t->getValue('tourney_id')))
    {
      util::throwException('not authorized') ;
    }

  //tourney_id,division_id,team1_id,team2_id,deadline,week_name
  $division_id = util::nvl($_POST['division_id'], $_REQUEST['division_id']) ;
  $div = new division(array('division_id'=>$division_id));

  // Try to create a new schedule
  // Now done as part of CreateSchedule
  // $div->removeSchedule();

  $nw = $_POST['num_weeks'];

  try
    {
      $div->createSchedule($nw);
      $msg = "New Schedule created!<br>";
    }
  catch (Exception $e)
    {
      $msg = "Error!<br>";
    }

  echo $msg;
  include 'manageSchedule.php';
}
catch (Exception $e) {}
?>
