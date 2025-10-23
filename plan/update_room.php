<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mysqli = require __DIR__ . '/../database/database.php';

header('Content-Type: application/json'); // Wichtig für JSON

if(isset($_POST['schedule_id'], $_POST['raum'])) {
    $stmt = $mysqli->prepare("UPDATE schedule SET raum=? WHERE schedule_id=?");
    $stmt->bind_param("si", $_POST['raum'], $_POST['schedule_id']);

    if($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Raum erfolgreich aktualisiert."]);
    } else {
        echo json_encode(["success" => false, "message" => "Fehler beim Aktualisieren: " . $stmt->error]);
    }

    $stmt->close();
    exit; // wichtig, damit keine weiteren Zeichen ausgegeben werden
}

// Falls POST-Daten fehlen
echo json_encode(["success" => false, "message" => "Ungültige Anfrage."]);
echo "<meta http-equiv='refresh' content='0'>";
exit;
