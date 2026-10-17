<?php
if (!isset($_SESSION['user_id'])) exit;

// Only require the database if it hasn't been loaded by another file yet
if (!isset($mysqli)) {
    $mysqli = require __DIR__ . '/../database/database.php';
}


$message = "";
$filterSemester = trim($_GET['filterSemester'] ?? '');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deleteSubjectId'])) {
    $deleteId = intval($_POST['deleteSubjectId']);
    $stmt = $mysqli->prepare("DELETE FROM faecher WHERE faecher_id = ?");
    $stmt->bind_param("i", $deleteId);
    if ($stmt->execute()) {
        $message = "Fach erfolgreich gelöscht. ✅";
    } else {
        $message = "Fehler beim Löschen: " . $stmt->error;
    }
    $stmt->close();
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    $subjectName = trim($_POST['subjectName'] ?? '');
    $lecturer = trim($_POST['lecturer'] ?? '');
    $semester = trim($_POST['semester'] ?? '');

    // Safely clear all output buffers to prevent HTML from corrupting the JSON
    while (ob_get_level()) {
        ob_end_clean();
    }

    if ($_POST['action'] === "add") {
        $stmt = $mysqli->prepare("INSERT INTO faecher (name, dozent, semester) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $subjectName, $lecturer, $semester);
        echo json_encode(["success" => $stmt->execute(), "message" => $stmt->error ?: "Erfolgreich hinzugefügt."]);
        $stmt->close();
        exit;
    } elseif ($_POST['action'] === "edit") {
        $id = intval($_POST['subjectId'] ?? 0);
        $stmt = $mysqli->prepare("UPDATE faecher SET name=?, dozent=?, semester=? WHERE faecher_id=?");
        $stmt->bind_param("sssi", $subjectName, $lecturer, $semester, $id);
        echo json_encode(["success" => $stmt->execute(), "message" => $stmt->error ?: "Erfolgreich aktualisiert."]);
        $stmt->close();
        exit;
    }
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

if ($filterSemester !== '') {
    $searchTerm = "%" . $filterSemester . "%";
    $stmt = $mysqli->prepare("SELECT * FROM faecher WHERE semester LIKE ? ORDER BY semester ASC, name ASC");
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $subjectsResult = $stmt->get_result();
} else {
    $subjectsResult = $mysqli->query("SELECT * FROM faecher ORDER BY semester ASC, name ASC");
}
?>

<h1>Fächerverwaltung</h1>

<?php if (!empty($message)): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="filter-section">
    <form method="GET" action="">
        <label for="filterSemester">Semester filtern:</label>
        <select name="filterSemester" id="filterSemester" onchange="this.form.submit()">
            <option value="">Alle Semester</option>
            <?php foreach($semesters as $sem): ?>
                <option value="<?= htmlspecialchars($sem) ?>" <?= ($filterSemester === (string)$sem) ? "selected" : "" ?>>
                    <?= htmlspecialchars($sem) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($filterSemester !== ''): ?>
            <br><br><a href="<?= $_SERVER['PHP_SELF'] ?>"><button type="button">Filter aufheben</button></a>
        <?php endif; ?>
    </form>
</div>
<br><br>

<table>
    <thead>
    <tr><th>Fach</th><th>Dozent</th><th>Semester</th><th>Aktionen</th></tr>
    </thead>
    <tbody>
    <?php if ($subjectsResult->num_rows > 0): ?>
        <?php while ($row = $subjectsResult->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['dozent']) ?></td>
                <td><?= htmlspecialchars($row['semester']) ?></td>
                <td>
                    <button style="background:none;border:none;cursor:pointer;padding:0;margin:0;" onclick='openSubjectModal("edit", <?= json_encode($row) ?>)'>✏️</button>
                    <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Fach wirklich löschen?');">
                        <input type="hidden" name="deleteSubjectId" value="<?= htmlspecialchars($row['faecher_id']) ?>">
                        <button type="submit" style="background:none;border:none;cursor:pointer;padding:0;margin:0;">🗑️</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="4">Keine Fächer vorhanden</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<br><br>
<button id="add_subject" onclick="openSubjectModal('add')">Neues Fach hinzufügen</button>

<div id="subjectModalOverlay" class="modal-overlay">
    <div id="modal-content">
        <h2 id="subjectModalTitle">Fach hinzufügen</h2>
        <form id="add-subject-form" method="POST" action="">
            <input type="hidden" id="subjectId" name="subjectId">
            <input type="hidden" id="subjectAction" name="action" value="add">

            <label for="subjectName">Fach:</label><br>
            <input type="text" id="subjectName" name="subjectName" required><br><br>

            <label for="lecturer">Dozent:</label><br>
            <input type="text" id="lecturer" name="lecturer" required><br><br>

            <label for="subjectSemester">Semester:</label><br>
            <input type="text" id="subjectSemester" name="semester" required><br><br>

            <div class="modal-buttons">
                <button type="submit">Speichern</button>
                <button type="button" onclick="closeSubjectModal()">Abbrechen</button>
            </div>
        </form>
    </div>
</div>
