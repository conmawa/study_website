<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Stellen Sie sicher, dass die Datenbankverbindung korrekt geladen wird
$mysqli = require __DIR__ . '/../database/database.php';

// --- Lösch-Funktion ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    // ⚠️ SICHERHEITSFIX: Vorbereitetes Statement verwenden, um SQL-Injection zu verhindern
    $delete_id = intval($_POST['delete_id']); 
    
    $stmt = $mysqli->prepare("DELETE FROM faecher WHERE faecher_id = ?");
    $stmt->bind_param("i", $delete_id); // 'i' für integer
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
             echo "Fach erfolgreich gelöscht. ✅";
        } else {
             echo "Fehler: Kein Fach mit dieser ID gefunden.";
        }
    } else {
        echo "Fehler beim Löschen: " . $stmt->error;
    }
    $stmt->close();
}

// --- Bearbeitungs-Funktion (Update) ---
// Diese Sektion ist bereits sicher, da sie prepared statements verwendet.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $fach = $_POST['edit_fach'];
    $raum = $_POST['edit_raum'];
    $dozent = $_POST['edit_dozent'];

    $stmt = $mysqli->prepare("UPDATE faecher SET name=?, raum=?, dozent=? WHERE faecher_id=?");
    $stmt->bind_param("sssi", $fach, $raum, $dozent, $id);
    
    if ($stmt->execute()) {
        echo "Fach erfolgreich aktualisiert. 🔄";
    } else {
        echo "Fehler beim Aktualisieren: " . $stmt->error;
    }
    $stmt->close();
}

// --- Hinzufügen-Funktion ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['fach']) && !empty($_POST['raum']) && !empty($_POST['dozent']) && !isset($_POST['delete_id']) && !isset($_POST['edit_id'])) {
    $fach = $_POST['fach'];
    $raum = $_POST['raum'];
    $dozent = $_POST['dozent'];
    
    // ⚠️ SICHERHEITSFIX: Vorbereitetes Statement verwenden, um SQL-Injection zu verhindern
    $stmt = $mysqli->prepare("INSERT INTO faecher (name, raum, dozent) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fach, $raum, $dozent); // 'sss' für drei strings
    
    if ($stmt->execute()) {
        echo "Neues Fach erfolgreich hinzugefügt. ✨";
    } else {
        echo "Fehler beim Hinzufügen: " . $stmt->error;
    }
    $stmt->close();
}

// Holen Sie die aktuellen Fächer für die Anzeige
$subjects_sql = "SELECT * FROM faecher ORDER BY name ASC";
$subjects_result = $mysqli->query($subjects_sql);

// Schließen der mysqli-Verbindung wird in database.php erwartet oder sollte hier am Ende erfolgen, 
// aber für diesen Snippet lassen wir es offen, da die $mysqli Variable von database.php kommt.
// $mysqli->close(); 
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type='text/css' href='../design\main.css' />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studium | Fächer</title>
</head>
<body>
    <h1>Fächer</h1>
    <form method="POST" action="">
        <h2>Neues Fach hinzufügen</h2>
        <label for="fach">Fach:</label>
        <input type="text" id="fach" name="fach" required>
        <label for="raum">Raum:</label>
        <input type="text" id="raum" name="raum" required>
        <label for="dozent">Dozent:</label>
        <input type="text" id="dozent" name="dozent" required>
        <br><br>
        <input type="submit" value="Fach hinzufügen">
    </form>
    
    
    <h2>Vorhandene Fächer</h2>
    <table>
        <tr>
            <th>Fach</th>
            <th>Raum</th>
            <th>Dozent</th>
            <th>Aktionen</th>
        </tr>
        <?php
        if ($subjects_result->num_rows > 0) {
            while ($row = $subjects_result->fetch_assoc()) {
                // ⚠️ SICHERHEITSFIX: htmlspecialchars() wird hier bereits in der Originalversion verwendet, was gut ist.
                echo "<tr>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['raum']) . "</td>
                        <td>" . htmlspecialchars($row['dozent']) . "</td>
                        <td>
                            <form method='POST' action='' style='display:inline;' onsubmit=\"return confirm('Sind Sie sicher, dass Sie dieses Fach löschen möchten?');\">
                                <input type='hidden' name='delete_id' value='" . htmlspecialchars($row['faecher_id']) . "'>
                                <button type='submit' title='Löschen' style='border:none;background:none;cursor:pointer;'>🗑️</button>
                            </form>
                            <button type='button' title='Bearbeiten' onclick='openEditPopup("
                                // JSON-Kodierung der Strings für JavaScript
                                . htmlspecialchars(json_encode($row['faecher_id']), ENT_QUOTES) . ", "
                                . htmlspecialchars(json_encode($row['name']), ENT_QUOTES) . ", "
                                . htmlspecialchars(json_encode($row['raum']), ENT_QUOTES) . ", "
                                . htmlspecialchars(json_encode($row['dozent']), ENT_QUOTES)
                            . ")'>✏️</button>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Keine Fächer vorhanden</td></tr>";
        }
        ?>
    </table>

    <div id="editModal">
        <form method="POST" action="">
            <h2>Fach bearbeiten</h2>
            <input type="hidden" id="edit_id" name="edit_id">
            <label>Fach:</label>
            <input type="text" id="edit_fach" name="edit_fach" required><br>
            <label>Raum:</label>
            <input type="text" id="edit_raum" name="edit_raum" required><br>
            <label>Dozent:</label>
            <input type="text" id="edit_dozent" name="edit_dozent" required><br><br>
            <button type="submit">Speichern</button>
            <button type="button" onclick="closeEditPopup()">Abbrechen</button>
        </form>
    </div>

    <script>
        function openEditPopup(id, name, raum, dozent) {
            document.getElementById("edit_id").value = id;
            document.getElementById("edit_fach").value = name;
            document.getElementById("edit_raum").value = raum;
            document.getElementById("edit_dozent").value = dozent;
            document.getElementById("editModal").style.display = "block";
        }

        function closeEditPopup() {
            document.getElementById("editModal").style.display = "none";
        }
    </script>

</body>
</html>