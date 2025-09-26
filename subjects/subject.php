<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mysqli = require __DIR__ . '/../database/database.php';

// Fach löschen, wenn delete_id übergeben wird
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']); // ID sicherstellen
    $delete_sql = "DELETE FROM faecher WHERE faecher_id = $delete_id";

    if ($mysqli->query($delete_sql) === TRUE) {
        echo "Fach erfolgreich gelöscht.";
    } else {
        echo "Fehler beim Löschen: " . $mysqli->error;
    }
}

// Neues Fach hinzufügen
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['fach']) && !empty($_POST['raum']) && !empty($_POST['dozent']) && !isset($_POST['delete_id'])) {
    $fach = $_POST['fach'];
    $raum = $_POST['raum'];
    $dozent = $_POST['dozent'];

    $insert_sql = "INSERT INTO faecher (name, raum, dozent) VALUES ('$fach', '$raum', '$dozent')";
    if ($mysqli->query($insert_sql) === TRUE) {
        echo "Neues Fach hinzugefügt";
    } else {
        echo "Error: " . $insert_sql . "<br>" . $mysqli->error;
    }
}
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
        <label for="fach">Fach:</label>
        <input type="text" id="fach" name="fach" required>
        <label for="raum">Raum:</label>
        <input type="text" id="raum" name="raum" required>
        <label for="dozent">Dozent:</label>
        <input type="text" id="dozent" name="dozent" required>
        <br><br>
        <input type="submit" value="Fach hinzufügen">
    </form>
    <br>
    <table>
        <tr>
            <th>Fach</th>
            <th>Raum</th>
            <th>Dozent</th>
            <th>Aktionen</th>
        </tr>
        <?php
        $subjects_sql = "SELECT * FROM faecher ORDER BY name ASC";
        $subjects_result = $mysqli->query($subjects_sql);

        if ($subjects_result->num_rows > 0) {
            while ($row = $subjects_result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['raum']) . "</td>
                        <td>" . htmlspecialchars($row['dozent']) . "</td>
                        <td>
                            <form method='POST' action='' style='display:inline;'>
                                <input type='hidden' name='delete_id' value='" . $row['faecher_id'] . "'>
                                <button type='submit' title='Löschen' style='border:none;background:none;cursor:pointer;'>🗑️</button>
                            </form>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Keine Fächer vorhanden</td></tr>";
        }
        ?>
    </table>
</body>
</html>