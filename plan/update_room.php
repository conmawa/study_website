<?php
$mysqli = require __DIR__ . '/../database/database.php';
if(isset($_POST['schedule_id'], $_POST['raum'])) {
    $stmt = $mysqli->prepare("UPDATE schedule SET raum=? WHERE schedule_id=?");
    $stmt->bind_param("si", $_POST['raum'], $_POST['schedule_id']);
    $stmt->execute();
}
?>
