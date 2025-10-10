<?php
$mysqli = require __DIR__ . '/../database/database.php';
if(isset($_POST['schedule_id'])) {
    $stmt = $mysqli->prepare("DELETE FROM schedule WHERE schedule_id=?");
    $stmt->bind_param("i", $_POST['schedule_id']);
    $stmt->execute();
}
?>
