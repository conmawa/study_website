<?php
$mysqli = require __DIR__ . '/../database/database.php';

// Zeiten und Tage definieren
$zeiten = ["08:00 - 09:30","09:45 - 11:15","11:30 - 13:00","14:00 - 15:30"];
$tage = ["Montag","Dienstag","Mittwoch","Donnerstag","Freitag"];

// Stundenplan für erste Anzeige laden
$plan_sql = "
    SELECT s.schedule_id, s.tag, s.zeitslot, s.raum, f.name, f.dozent
    FROM schedule s
    JOIN faecher f ON s.faecher_id = f.faecher_id
";
$plan_result = $mysqli->query($plan_sql);

$plan = [];
while($row = $plan_result->fetch_assoc()) {
    $plan[$row['zeitslot']][$row['tag']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Stundenplan</title>
    <link rel="stylesheet" href="../design/main.css">
    
    <style>
        table, th, td { border:1px solid black; border-collapse:collapse; text-align:center; padding:8px; }
        th { background:#eee; }
        td { min-width:120px; vertical-align:top; }
        #scheduleModal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); }
        #scheduleModal .modalContent { background:white; margin:5% auto; padding:20px; width:80%; max-width:800px; position:relative; }
        #scheduleModal .closeBtn { position:absolute; top:10px; right:10px; cursor:pointer; font-size:18px; }
        button { cursor:pointer; }
    </style>
</head>
<body>
    <h1>Stundenplan</h1>

    <!-- Button zum Öffnen des Modals -->
    <button id="editScheduleBtn">Stundenplan bearbeiten</button>

    <!-- Stundenplan Tabelle (nur Anzeige) -->
    <h2>Stundenplan Übersicht</h2>
    <table>
        <tr>
            <th>Uhrzeit</th>
            <?php foreach($tage as $t): ?>
                <th><?= $t ?></th>
            <?php endforeach; ?>
        </tr>
        <?php foreach($zeiten as $z): ?>
            <tr>
                <th><?= $z ?></th>
                <?php foreach($tage as $t): ?>
                    <td>
                        <?php
                        if(isset($plan[$z][$t])) {
                            foreach($plan[$z][$t] as $eintrag) {
                                echo "<b>".htmlspecialchars($eintrag['name'])."</b><br>";
                                echo htmlspecialchars($eintrag['raum'])."<br>";
                                echo "<i>".htmlspecialchars($eintrag['dozent'])."</i><br><hr>";
                            }
                        }
                        ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>

    <div id="scheduleModalOverlay" class="modal-overlay">
        <div id="modal-content">
            <h2>Stundenplan bearbeiten</h2>

            <!-- Neues Fach hinzufügen -->
            <h3>Fach zum Stundenplan hinzufügen</h3>
            <label for="newFach">Fach:</label>
            <select id="newFach">
                <option value="">-- Bitte wählen --</option>
                <?php
                $faecher_result = $mysqli->query("SELECT * FROM faecher ORDER BY name ASC");
                while($fach = $faecher_result->fetch_assoc()) {
                    echo "<option value='" . $fach['faecher_id'] . "'>" . htmlspecialchars($fach['name']) . " (" . htmlspecialchars($fach['dozent']) . ")</option>";
                }
                ?>
            </select><br>

            <label for="newTag">Tag:</label>
            <select id="newTag">
                <?php foreach($tage as $t): ?>
                    <option value="<?= $t ?>"><?= $t ?></option>
                <?php endforeach; ?>
            </select><br>

            <label for="newZeitslot">Zeitslot:</label>
            <select id="newZeitslot">
                <?php foreach($zeiten as $z): ?>
                    <option value="<?= $z ?>"><?= $z ?></option>
                <?php endforeach; ?>
            </select><br>

            <label for="newRaum">Raum:</label>
            <input type="text" id="newRaum"><br><br>

            <button id="addEntryButton">Hinzufügen</button>
            <hr>
            <div id="schedule-modal-container">Lade Stundenplan...</div>
            <div class="modal-buttons">
                <button id="saveBtn">Speichern</button>
                <button type="button" id="closeBtn">Abbrechen</button>
            </div>
            
        </div>
    </div>

    <script src="..\javascript\schedule.js"></script>
</body>

