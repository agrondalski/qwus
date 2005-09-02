<?php
$tid = $_POST['tourney_id'];

// Create the new division 
$div = new division(array('tourney_id'=>$tid,
                          'name'=>$_POST['name'],
                          'max_teams'=>$_POST['max_teams'],
                          'num_games'=>$_POST['num_games'],
                          'playoff_spots'=>$_POST['playoff_spots'],
                 	  'elim_losses'=>$_POST['elim_losses']));

include 'listDivisions.php';

?>
<br>New division created!
