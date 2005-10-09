<?php
require 'includes.php';
require_once 'login.php';

try
{
  $tid = $_REQUEST['tourney_id'];
  $division_id = $_REQUEST['division_id'];
  $match_id = $_REQUEST['match_id'];

  $m = new match(array('match_id'=>$match_id));
  
  $m->addGame(array('winning_team_id'=>$_REQUEST['winning_team_id'],
		    'filename'=>$_REQUEST['filename'],
		    'map'=>$_REQUEST['map'],
		    'teamStats'=>$_REQUEST['teamStats'],
		    'team1'=>$_REQUEST['team1'],
		    'team2'=>$_REQUEST['team2'],
		    'team_score_graph_small'=>$_REQUEST['team_score_graph_small'],
		    'team_score_graph_large'=>$_REQUEST['team_score_graph_large'],
		    'player_score_graph'=>$_REQUEST['player_score_graph'],
		    'playerFields'=>$_REQUEST['playerFields'],
		    'PlayerStats'=>$_REQUEST['PlayerStats'],
		    'team1players'=>$_REQUEST['team1players'],
		    'team2players'=>$_REQUEST['team2players']));

  echo "<b>Success!</b><br><br>";
  echo "Game was added, click this link to add another game.";
  echo "<br><br><a href='?a=reportMatch&amp;tourney_id=$tid&amp;division_id=$division_id&amp;match_id=$match_id'>Report Match Page</a>";
}
catch (Exception $e) {print 'Unable to Add Game'; print $e;}
?>
