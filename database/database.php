<?php

$dbname = 'dbs14013315';
$hostname = 'db5017468837.hosting-data.io';
$username = 'dbu96477';
$password = 'Studium_Verwaltungsinformtik=2025';

$mysqli = new mysqli($hostname, $username, $password, $dbname);

if ($mysqli->connect_errno){
  die("Connection error: " . $mysqli->connect_error);
}

return $mysqli;
?>
