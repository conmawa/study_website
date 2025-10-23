<?php
$mysqli = require __DIR__ . '/../database/database.php';

$plan_sql = "SELECT schedule.schedule_id, faecher.name, faecher.dozent, schedule.tag, schedule.zeitslot, schedule.raum 
             FROM schedule 
             JOIN faecher ON schedule.faecher_id = faecher.faecher_id 
             ORDER BY tag, zeitslot";

$result = $mysqli->query($plan_sql);

$entries = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $entries[] = [
            'schedule_id' => $row['schedule_id'],
            'name' => $row['name'],
            'dozent' => $row['dozent'],
            'tag' => $row['tag'],
            'zeitslot' => $row['zeitslot'],
            'raum' => $row['raum']
        ];
    }
    echo json_encode(['entries' => $entries]);
} else {
    echo json_encode(['entries' => [], 'error' => 'Fehler beim Abrufen des Stundenplans.']);
}
?>