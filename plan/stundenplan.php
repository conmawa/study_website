<?php

$mysqli = require __DIR__ . '/../database/database.php';

// Fach in Stundenplan eintragen
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['faecher_id'], $_POST['tag'], $_POST['zeitslot'])) {
    $faecher_id = intval($_POST['faecher_id']);
    $tag = $_POST['tag'];
    $zeitslot = $_POST['zeitslot'];

    $insert_sql = "INSERT INTO schedule (faecher_id, tag, zeitslot) VALUES ('$faecher_id', '$tag', '$zeitslot')";
    if ($mysqli->query($insert_sql) === TRUE) {
        echo "<p style='color:green;'>Fach erfolgreich eingetragen.</p>";
    } else {
        echo "<p style='color:red;'>Fehler: " . $mysqli->error . "</p>";
    }
}

// Eintrag löschen
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $delete_sql = "DELETE FROM schedule WHERE id = $delete_id";
    if ($mysqli->query($delete_sql) === TRUE) {
        echo "<p style='color:green;'>Eintrag gelöscht.</p>";
    } else {
        echo "<p style='color:red;'>Fehler beim Löschen: " . $mysqli->error . "</p>";
    }
}

// Alle Fächer holen
$faecher_result = $mysqli->query("SELECT * FROM faecher Order By name ASC");

// Stundenplan holen
$plan_sql = "
    SELECT schedule.schedule_id, schedule.tag, schedule.zeitslot, faecher.name, faecher.raum, faecher.dozent
    FROM schedule
    JOIN faecher ON schedule.faecher_id = faecher.faecher_id
";
$plan_result = $mysqli->query($plan_sql);

// Daten in Array: [zeitslot][tag] = Fach
$plan = [];
if ($plan_result->num_rows > 0) {
    while ($row = $plan_result->fetch_assoc()) {
        $plan[$row['zeitslot']][$row['tag']] = $row;
    }
}

// Zeit-Slots und Tage definieren
$zeiten = [
    "08:00 - 09:30",
    "09:45 - 11:15",
    "11:30 - 13:00",
    "14:00 - 15:30",
];

$tage = ["Montag","Dienstag","Mittwoch","Donnerstag","Freitag"];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type='text/css' href='../design/main.css' />
    <title>Studium | Stundenplan</title>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            text-align: center;
            padding: 8px;
        }
        th {
            background: #eee;
        }
        td {
            min-width: 120px;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <h1>Stundenplan</h1>

    <!-- Formular -->
    <h2>Fach zu Stundenplan hinzufügen</h2>
    <form method="POST" action="">
        <label for="faecher_id">Fach:</label>
        <select id="faecher_id" name="faecher_id" required>
            <option value="">-- Bitte wählen --</option>
            <?php
            if ($faecher_result->num_rows > 0) {
                while ($fach = $faecher_result->fetch_assoc()) {
                    echo "<option value='" . $fach['faecher_id'] . "'>" . htmlspecialchars($fach['name']) . " (" . htmlspecialchars($fach['dozent']) . ")</option>";
                }
            }
            ?>
        </select>

        <label for="tag">Tag:</label>
        <select id="tag" name="tag" required>
            <?php foreach ($tage as $t): ?>
                <option value="<?= $t ?>"><?= $t ?></option>
            <?php endforeach; ?>
        </select>

        <label for="zeitslot">Zeit:</label>
        <select id="zeitslot" name="zeitslot" required>
            <?php foreach ($zeiten as $z): ?>
                <option value="<?= $z ?>"><?= $z ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Hinzufügen</button>
    </form>

    <br><br>

    <!-- Raster-Stundenplan -->
    <table>
        <tr>
            <th>Uhrzeit</th>
            <?php foreach ($tage as $t): ?>
                <th><?= $t ?></th>
            <?php endforeach; ?>
        </tr>
        <?php foreach ($zeiten as $z): ?>
            <tr>
                <th><?= $z ?></th>
                <?php foreach ($tage as $t): ?>
                    <td>
                        <?php if (isset($plan[$z][$t])): 
                            $eintrag = $plan[$z][$t]; ?>
                            <b><?= htmlspecialchars($eintrag['name']) ?></b><br>
                            <?= htmlspecialchars($eintrag['raum']) ?><br>
                            <i><?= htmlspecialchars($eintrag['dozent']) ?></i><br>
                            <form method="POST" action="" style="margin-top:5px;">
                                <input type="hidden" name="delete_id" value="<?= $eintrag['id'] ?>">
                                <button type="submit" style="border:none;background:none;cursor:pointer;">🗑️</button>
                            </form>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
