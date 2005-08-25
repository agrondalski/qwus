<?php

$tourney_id = 1;
if (isset($_GET['tourney_id']))
{
  $tourney_id = $_GET['tourney_id'];
}

echo '$tourney_id';
<br>

?>