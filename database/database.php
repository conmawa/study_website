<?php

$mysqli = new mysqli(
hostname: 'db5017468837.hosting-data.io',
username: 'dbu96477',
password: 'Studium_Verwaltungsinformtik=2025',
database: 'dbs14013315');

if ($mysqli->connect_errno){
  die("Connection error: " . $mysqli->connect_error);
}

return $mysqli;
?>
