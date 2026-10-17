<?php
if (!isset($_SESSION['user_id'])) exit;

// Only require the database if it hasn't been loaded by another file yet
if (!isset($mysqli)) {
    $mysqli = require __DIR__ . '/../database/database.php';
}

function getAverageGrade($mysqli, $condition) {
    $sql = "SELECT AVG(wert) as average FROM noten JOIN faecher ON noten.faecher_id = faecher.faecher_id WHERE $condition";
    return $mysqli->query($sql)->fetch_assoc()['average'] ?? 0;
}

$subjectsSql = "SELECT * FROM faecher ORDER BY semester, name";
$subjectsResult = $mysqli->query($subjectsSql);
$subjectsBySemester = [];
$semesters = [];

if ($subjectsResult->num_rows > 0) {
    while($row = $subjectsResult->fetch_assoc()) {
        $sem = $row['semester'];
        if (!in_array($sem, $semesters)) $semesters[] = $sem;
        $subjectsBySemester[$sem][] = $row['name'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteMarkId'])) {
    $deleteId = intval($_POST['deleteMarkId']);
    $stmt = $mysqli->prepare("DELETE FROM noten WHERE noten_id = ?");
    $stmt->bind_param("i", $deleteId);
    if ($stmt->execute()) $message = "Note erfolgreich gelöscht.";
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['subject']) && isset($_POST['grade']) && !isset($_POST['deleteId'])) {
    $subject = trim($_POST['subject']);
    $grade = floatval($_POST['grade']);

    // Safely clear output buffer
    while (ob_get_level()) {
        ob_end_clean();
    }

    $stmt = $mysqli->prepare("INSERT INTO noten (wert, faecher_id) VALUES (?, (SELECT faecher_id FROM faecher WHERE name = ? LIMIT 1))");
    $stmt->bind_param("ds", $grade, $subject);
    echo json_encode(["success" => $stmt->execute(), "message" => $stmt->error ?: "Erfolgreich hinzugefügt."]);
    $stmt->close();
    exit;
}
?>

<h1>Noten</h1>
<?php if (isset($message)) echo "<p><strong>$message</strong></p>"; ?>

<table>
    <tr><th>Fach</th><th>Semester</th><th>Wert</th><th>Aktionen</th></tr>
    <?php
    $marksSql = "SELECT noten.noten_id, faecher.name, faecher.semester, noten.wert 
                  FROM noten INNER JOIN faecher ON noten.faecher_id = faecher.faecher_id 
                  ORDER BY faecher.semester, faecher.name";
    $marksResult = $mysqli->query($marksSql);

    if ($marksResult->num_rows > 0) {
        while($row = $marksResult->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . htmlspecialchars($row['semester']) . "</td>
                    <td>" . htmlspecialchars($row['wert']) . "</td>
                    <td>
                        <form method='POST' action='' style='display:inline;'>
                            <input type='hidden' name='deleteMarkId' value='" . $row['noten_id'] . "'>
                            <button type='submit' style='border:none;background:none;cursor:pointer;'>🗑️</button>
                        </form>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>Keine Noten vorhanden</td></tr>";
    }

    // --- ZP Average Calculation ---
    $avgZp1 = getAverageGrade($mysqli, "faecher.semester IN ('Semester 1', 'Semester 2')");
    $avgZp2 = getAverageGrade($mysqli, "faecher.semester = 'Semester 3'");
    $averageZp = ($avgZp1 * 70 + $avgZp2 * 30) / 100;

    echo "<tr><td><strong>Durchschnitt ZP</strong></td><td><strong>" . round($averageZp, 2) . "</strong></td><td></td><td></td></tr>";

    // --- QP Average Calculation ---
    $avgQp1 = getAverageGrade($mysqli, "faecher.semester IN ('Semester 4', 'Semester 5')");
    $avgQp1_1 = getAverageGrade($mysqli, "faecher.name = 'Mündliche Prüfung 1'");
    $avgQp1_2 = getAverageGrade($mysqli, "faecher.name = 'Diplomarbeit'");
    $avgQp1_3 = getAverageGrade($mysqli, "faecher.name = 'Mündliche Prüfung 2'");
    $avgQp2 = getAverageGrade($mysqli, "faecher.semester = 'Semester 6'");
    $avgQp2_1 = getAverageGrade($mysqli, "faecher.name = 'Praxisbeurteilung'");

    $averageQp = ($avgQp1 * 40 + $avgQp1_1 * 4 + $avgQp1_3 * 4 + $avgQp1_2 * 5 + $avgQp2 * 17 + $avgQp2_1 * 4 + $averageZp * 26) / 100;

    echo "<tr><td><strong>Durchschnitt QP</strong></td><td><strong>" . round($averageQp, 2) . "</strong></td><td></td><td></td></tr>";
    ?>
</table>

<br><br>
<button id="add_mark" onclick="openMarkModal()">Neue Note hinzufügen</button>

<div id="markModalOverlay" class="modal-overlay" style="display: none;">
    <div id="modal-content">
        <h2 id="markModalTitle">Neue Note hinzufügen</h2>
        <form id="add-mark-form" method="post" action="">
            <label for="markSemester">Semester:</label>
            <select name="semester" id="markSemester" required>
                <?php foreach($semesters as $sem) echo "<option value='" . htmlspecialchars($sem) . "'>" . htmlspecialchars($sem) . "</option>"; ?>
            </select>

            <label for="markSubject">Fach:</label>
            <select name="subject" id="markSubject" required></select><br><br>

            <label for="grade">Notenwert:</label>
            <input type="number" min="0" max="15" name="grade" id="grade" required><br><br>

            <input type="submit" value="Note hinzufügen">
            <button type="button" onclick="closeMarkModal()">Abbrechen</button>
        </form>
    </div>
</div>
<script>window.subjectsData = <?= json_encode($subjectsBySemester); ?>;</script>
