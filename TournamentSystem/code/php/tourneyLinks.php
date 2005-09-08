<?php
include 'login.php' ;                                                                                                                                                                                  
if ($displayLogin)                                                                                                                                                                                     
{                                                                                                                                                                                                      
  return ;                                                                                                                                                                                             
} 
echo "<a href='?a=tourneyHome&amp;tourney_id=$tid'>Admin Home</a><br>";
echo "Logged in as: ",$_SESSION["username"];
?>
