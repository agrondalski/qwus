<?php

require 'includes.php';

$tid = $_POST['tourney_id'];

// Create the new division 

$mode = $_REQUEST['mode'];

if ($mode=="edit") {
  $did = $_POST['did'];
  $div = new division(array('division_id'=>$did));

  $div->update('name',$_POST['name']);
  $div->update('max_teams',$_POST['max_teams']);
  $div->update('num_games',$_POST['num_games']);
  $div->update('playoff_spots',$_POST['playoff_spots']);
  $div->update('elim_losses',$_POST['elim_losses']);
  $msg = "<br>Division updated!<br>";
}
elseif ($mode=="delete") {

  $did = $_REQUEST['did'];
  $div = new division(array('division_id'=>$did));
  $div->delete();
  $msg = "<br>Division deleted!<br>";
}
else {
  $div = new division(array('tourney_id'=>$tid,
                          'name'=>$_POST['name'],
                          'max_teams'=>$_POST['max_teams'],
                          'num_games'=>$_POST['num_games'],
                          'playoff_spots'=>$_POST['playoff_spots'],
                 	  'elim_losses'=>$_POST['elim_losses']));

$msg = "<br>New division created!<br>";
}
include 'listDivisions.php';
echo $msg
?>
