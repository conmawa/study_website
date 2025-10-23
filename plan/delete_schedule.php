<?php
$mysqli = require __DIR__ . '/../database/database.php';
header('Content-Type: application/json'); // wichtig für JS

if(isset($_POST['schedule_id'])) {
    $stmt = $mysqli->prepare("DELETE FROM schedule WHERE schedule_id=?");
    $stmt->bind_param("i", $_POST['schedule_id']);

    if($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Eintrag erfolgreich gelöscht."]);
    } else {
        echo json_encode(["success" => false, "message" => "Fehler beim Löschen: " . $stmt->error]);
    }

    $stmt->close();
    exit;
}

echo json_encode(["success" => false, "message" => "Ungültige Anfrage."]);
echo "<meta http-equiv='refresh' content='0'>";
exit;
