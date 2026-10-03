<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mysqli = require __DIR__ . '/../database/database.php';

// Fetch subjects and group them by semester for the dropdowns
$subjects_sql = "SELECT * FROM faecher ORDER BY semester, name";
$subjects_result = $mysqli->query($subjects_sql);

$subjects_by_semester = [];
$semesters = [];

if ($subjects_result->num_rows > 0) {
    while($row = $subjects_result->fetch_assoc()) {
        $sem = $row['semester'];
        $fach = $row['name'];

        // Build an array of unique semesters
        if (!in_array($sem, $semesters)) {
            $semesters[] = $sem;
        }
        // Group subjects under their respective semester
        $subjects_by_semester[$sem][] = $fach;
    }
}

// Note löschen
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);

    $stmt = $mysqli->prepare("DELETE FROM noten WHERE noten_id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $message = "Note erfolgreich gelöscht.";
    } else {
        $message = "Fehler beim Löschen: " . $mysqli->error;
    }
    $stmt->close();
}

// Neue Note hinzufügen
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['fach']) && isset($_POST['wert']) && !isset($_POST['delete_id'])) {
    $fach = $_POST['fach'];
    $wert = floatval($_POST['wert']);

    $stmt = $mysqli->prepare("INSERT INTO noten (wert, faecher_id) VALUES (?, (SELECT faecher_id FROM faecher WHERE name = ? LIMIT 1))");
    $stmt->bind_param("ds", $wert, $fach);

    if ($stmt->execute()) {
        $message = "Neue Note hinzugefügt.";
    } else {
        $message = "Fehler beim Hinzufügen: " . $mysqli->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="../design/main.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studium | Noten</title>
</head>
<body>
<h1>Noten</h1>

<?php if (isset($message)) echo "<p><strong>$message</strong></p>"; ?>

<table>
    <tr>
        <th>Fach</th>
        <th>Semester</th>
        <th>Wert</th>
        <th>Aktionen</th>
    </tr>
    <?php
    $marks_sql = "SELECT noten.noten_id, faecher.name, faecher.semester, noten.wert 
                      FROM noten 
                      INNER JOIN faecher ON noten.faecher_id = faecher.faecher_id 
                      ORDER BY faecher.semester, faecher.name";
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
                                <button type='submit' title='Löschen' style='border:none;background:none;cursor:pointer;padding:0;margin:0;'>🗑️</button>
                            </form>
                        </td>
                      </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>Keine Noten vorhanden</td></tr>";
    }

    // ==========================================
    // Calculate and display ZP Average
    // ==========================================
    $average_sql_zp1 = "SELECT AVG(wert) as average_wert FROM noten JOIN faecher ON noten.faecher_id = faecher.faecher_id WHERE faecher.semester = 'Semester 1' OR faecher.semester = 'Semester 2'";
    $average_result_zp1 = $mysqli->query($average_sql_zp1);

    $average_sql_zp2 = "SELECT AVG(wert) as average_wert FROM noten JOIN faecher ON noten.faecher_id = faecher.faecher_id WHERE faecher.semester = 'Semester 3'";
    $average_result_zp2 = $mysqli->query($average_sql_zp2);

    $val_zp1 = $average_result_zp1->fetch_assoc()['average_wert'] ?? 0;
    $val_zp2 = $average_result_zp2->fetch_assoc()['average_wert'] ?? 0;

    $average_zp = ($val_zp1 * 70 + $val_zp2 * 30) / 100;
    echo "<tr><td><strong>Durchschnitt ZP</strong></td><td><strong>" . round($average_zp, 2) . "</strong></td><td></td><td></td></tr>";

    // ==========================================
    // Calculate and display QP Average
    // ==========================================
    $average_sql_qp1 = "SELECT AVG(wert) as average_wert FROM noten JOIN faecher ON noten.faecher_id = faecher.faecher_id WHERE faecher.semester = 'Semester 4' OR faecher.semester = 'Semester 5'";
    $average_result_qp1 = $mysqli->query($average_sql_qp1);

    $average_sql_qp1_1 = "SELECT AVG(wert) as average_wert FROM noten JOIN faecher ON noten.faecher_id = faecher.faecher_id WHERE faecher.name = 'Mündliche Prüfung 1'";
    $average_result_qp1_1 = $mysqli->query($average_sql_qp1_1);

    $average_sql_qp1_2 = "SELECT AVG(wert) as average_wert FROM noten JOIN faecher ON noten.faecher_id = faecher.faecher_id WHERE faecher.name = 'Diplomarbeit'";
    $average_result_qp1_2 = $mysqli->query($average_sql_qp1_2);

    $average_sql_qp1_3 = "SELECT AVG(wert) as average_wert FROM noten JOIN faecher ON noten.faecher_id = faecher.faecher_id WHERE faecher.name = 'Mündliche Prüfung 2'";
    $average_result_qp1_3 = $mysqli->query($average_sql_qp1_3);

    $average_sql_qp2 = "SELECT AVG(wert) as average_wert FROM noten JOIN faecher ON noten.faecher_id = faecher.faecher_id WHERE faecher.semester = 'Semester 6'";
    $average_result_qp2 = $mysqli->query($average_sql_qp2);

    $average_sql_qp2_1 = "SELECT AVG(wert) as average_wert FROM noten JOIN faecher ON noten.faecher_id = faecher.faecher_id WHERE faecher.name = 'Praxisbeurteilung'";
    $average_result_qp2_1 = $mysqli->query($average_sql_qp2_1);

    $average_qp1 = $average_result_qp1->fetch_assoc()['average_wert'] ?? 0;
    $average_qp1_1 = $average_result_qp1_1->fetch_assoc()['average_wert'] ?? 0;
    $average_qp1_2 = $average_result_qp1_2->fetch_assoc()['average_wert'] ?? 0;
    $average_qp1_3 = $average_result_qp1_3->fetch_assoc()['average_wert'] ?? 0;
    $average_qp2 = $average_result_qp2->fetch_assoc()['average_wert'] ?? 0;
    $average_qp2_1 = $average_result_qp2_1->fetch_assoc()['average_wert'] ?? 0;

    $average_qp = ($average_qp1 * 40 + $average_qp1_1 * 4 + $average_qp1_3 * 4 + $average_qp1_2 * 5 + $average_qp2 * 17 + $average_qp2_1 * 4 + $average_zp * 26) / 100;
    echo "<tr><td><strong>Durchschnitt QP</strong></td><td><strong>" . round($average_qp, 2) . "</strong></td><td></td><td></td></tr>";

    ?>
</table>

<br><br>

<button id="add_mark" onclick="openMarkModal()">Neue Note hinzufügen</button>

<!-- Modal Overlay -->
<div id="markModalOverlay" class="modal-overlay" style="display: none;">
    <div id="modal-content">
        <h2 id="markModalTitle">Neue Note hinzufügen</h2>

        <form method="post" action="">
            <label for="semester">Semester:</label>
            <select name="semester" id="semester" required>
                <?php
                foreach($semesters as $sem) {
                    echo "<option value='" . htmlspecialchars($sem) . "'>" . htmlspecialchars($sem) . "</option>";
                }
                ?>
            </select>

            <label for="fach">Fach:</label>
            <select name="fach" id="fach" required>
                <!-- JavaScript will populate this based on the semester -->
            </select>

            <br><br>

            <label for="wert">Notenwert:</label>
            <input type="number" min="0" max="15" name="wert" id="wert" required>

            <br><br>

                <input type="submit" value="Note hinzufügen">
                <button type="button" onclick="closeMarkModal()">Abbrechen</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.subjectsData = <?php echo json_encode($subjects_by_semester); ?>;
</script>
<script src="../javascript/marks.js"></script>
</body>
</html>