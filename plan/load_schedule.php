<?php
$mysqli = require __DIR__ . '/../database/database.php';

// Zeiten, Tage
$zeiten = ["08:00 - 09:30","09:45 - 11:15","11:30 - 13:00","14:00 - 15:30"];
$tage = ["Montag","Dienstag","Mittwoch","Donnerstag","Freitag"];

// Stundenplan laden
$plan_sql = "SELECT s.schedule_id, s.tag, s.zeitslot, s.raum, f.name, f.dozent
             FROM schedule s JOIN faecher f ON s.faecher_id=f.faecher_id";
$result = $mysqli->query($plan_sql);
$plan = [];
while($row = $result->fetch_assoc()) {
    $plan[$row['zeitslot']][$row['tag']][] = $row;
}

// Tabelle
echo "<table border='1' style='border-collapse:collapse;width:100%'>";
echo "<tr><th>Uhrzeit</th>";
foreach($tage as $t) echo "<th>$t</th>";
echo "</tr>";

foreach($zeiten as $z) {
    echo "<tr><th>$z</th>";
    foreach($tage as $t) {
        echo "<td>";
        if(isset($plan[$z][$t])) {
            foreach($plan[$z][$t] as $eintrag) {
                echo "<b>".htmlspecialchars($eintrag['name'])."</b><br>";
                echo "<input type='text' value='".htmlspecialchars($eintrag['raum'])."' onchange='updateRoom(".$eintrag['schedule_id'].", this.value)' style='width:60px'><br>";
                echo "<i>".htmlspecialchars($eintrag['dozent'])."</i><br>";
                echo "<button onclick='deleteEntry(".$eintrag['schedule_id'].")'>🗑️</button><hr>";
            }
        }
        echo "</td>";
    }
    echo "</tr>";
}
echo "</table>";
?>
