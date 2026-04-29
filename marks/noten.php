<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mysqli = require __DIR__ . '/../database/database.php';

// Fetch subjects
$subjects_sql = "SELECT * FROM faecher Order By semester, name";
$subjects_result = $mysqli->query($subjects_sql);

// Note löschen, wenn delete_id übergeben wird
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']); // ID sicherstellen
    $delete_sql = "DELETE FROM noten WHERE noten_id = $delete_id";

    if ($mysqli->query($delete_sql) === TRUE) {
        echo "Note erfolgreich gelöscht.";
    } else {
        echo "Fehler beim Löschen: " . $mysqli->error;
    }
}

// Neue Note hinzufügen
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['fach']) && !empty($_POST['wert']) && !isset($_POST['delete_id'])) {
    $fach = $_POST['fach'];
    $wert = $_POST['wert'];

    $insert_sql = "INSERT INTO noten (wert, faecher_id) VALUES ('$wert', (SELECT faecher_id FROM faecher WHERE name = '$fach'))";
    if ($mysqli->query($insert_sql) === TRUE) {
        echo "Neue Note hinzugefügt";
    } else {
        echo "Error: " . $insert_sql . "<br>" . $mysqli->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type = 'text/css' href = '../design\main.css' />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studium | Noten</title>
</head>
<body>
    <h1>Noten</h1>
    <table>
        <tr>
            <th>Fach</th>
            <th>Semester</th>
            <th>Wert</th>
            <th>Aktionen</th>
        </tr>
        <?php
        $marks_sql = "SELECT noten.noten_id, faecher.name, faecher.semester, noten.wert FROM noten INNER JOIN faecher ON noten.faecher_id = faecher.faecher_id ORDER BY faecher.semester, faecher.name";
        $marks_result = $mysqli->query($marks_sql);

        if ($marks_result->num_rows > 0) {
            while($row = $marks_result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['semester']) . "</td>
                        <td>" . htmlspecialchars($row['wert']) . "</td>
                        <td>
                            <form method='POST' action='' style='display:inline;'>
                                <input type='hidden' name='delete_id' value='" . $row['noten_id'] . "'>
                                <button type='submit' title='Löschen' style='border:none;background:none;cursor:pointer;'>🗑️</button>
                            </form>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>Keine Noten vorhanden</td></tr>";
        }

        // Calculate and display the average grade
        $average_sql = "SELECT AVG(wert) as average_wert FROM noten";
        $average_result = $mysqli->query($average_sql);
        if ($average_result->num_rows > 0) {
            $average_row = $average_result->fetch_assoc();
            echo "<tr><td><strong>Durchschnitt</strong></td><td><strong>" . round($average_row['average_wert'], 2) . "</strong></td><td></td><td></td></tr>";
        }
        ?>
    </table>
    <br><br>
    <h2>Neue Note hinzufügen</h2>
    <form method="post" action="">
        <label for="fach">Fach:</label>
        <select name="fach" id="fach" required>
            <?php
            if ($subjects_result->num_rows > 0) {
                while($row = $subjects_result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['name']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                }
            } else {
                echo "<option value=''>No subjects available</option>";
            }
            ?>
        </select>
        <br><br>
        <label for="wert">Notenwert:</label>
        <input type="number" name="wert" id="wert" required>
        <br><br>
        <input type="submit" value="Note hinzufügen">
    </form>
</body>
</html>