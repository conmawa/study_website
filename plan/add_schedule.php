<?php
$mysqli = require __DIR__ . '/../database/database.php';

if(isset($_POST['faecher_id'], $_POST['tag'], $_POST['zeitslot'], $_POST['raum'])) {
    $faecher_id = intval($_POST['faecher_id']);
    $tag = $_POST['tag'];
    $zeitslot = $_POST['zeitslot'];
    $raum = $_POST['raum'];

    $stmt = $mysqli->prepare("INSERT INTO schedule (faecher_id, tag, zeitslot, raum) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $faecher_id, $tag, $zeitslot, $raum);
    $stmt->execute();
}
?>
