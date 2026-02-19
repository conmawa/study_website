<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB-Verbindung
$mysqli = require __DIR__ . '/../database/database.php';

// --- Nachrichtensystem ---
$message = "";

// --- Filter-Logik & Sortierung festlegen ---
$filter_semester = isset($_GET['filter_semester']) ? trim($_GET['filter_semester']) : '';

// --- Fach löschen ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']); 
    $stmt = $mysqli->prepare("DELETE FROM faecher WHERE faecher_id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = "Fach erfolgreich gelöscht. ✅";
    } else {
        $message = "Fehler beim Löschen: " . $stmt->error;
    }
    $stmt->close();
}

// --- Fach hinzufügen oder bearbeiten ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    $name = trim($_POST['name']);
    $dozent = trim($_POST['dozent']);
    $semester = trim($_POST['semester']);

    if ($_POST['action'] === "add") {
        $stmt = $mysqli->prepare("INSERT INTO faecher (name, dozent, semester) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $dozent, $semester);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Neues Fach erfolgreich hinzugefügt."]);
        } else {
            echo json_encode(["success" => false, "message" => "Fehler beim Hinzufügen: " . $stmt->error]);
        }
        $stmt->close();
        exit;
    }
    elseif ($_POST['action'] === "edit") {
        $id = intval($_POST['faecher_id']);
        $stmt = $mysqli->prepare("UPDATE faecher SET name=?, dozent=?, semester=? WHERE faecher_id=?");
        $stmt->bind_param("sssi", $name, $dozent, $semester, $id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Fach erfolgreich aktualisiert."]);
        } else {
            echo json_encode(["success" => false, "message" => "Fehler beim Aktualisieren: " . $stmt->error]);
        }
        $stmt->close();
        exit;
    }
}

// --- Daten abrufen mit dynamischer Sortierung ---
if ($filter_semester !== '') {
    // Wenn gefiltert: Nur das gewählte Semester, alphabetisch nach Fachname
    $stmt = $mysqli->prepare("SELECT * FROM faecher WHERE semester = ? ORDER BY name ASC");
    $stmt->bind_param("s", $filter_semester);
    $stmt->execute();
    $subjects_result = $stmt->get_result();
} else {
    // Wenn kein Filter: Erst nach Semester aufsteigend, dann alphabetisch nach Fachname
    $subjects_result = $mysqli->query("SELECT * FROM faecher ORDER BY semester ASC, name ASC");
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type='text/css' href='../design/main.css' />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../javascript/subject.js" defer></script>
    <title>Studium | Fächer</title>
</head>
<body>

    <h1>Fächerverwaltung</h1>

    <?php if (!empty($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="filter-section">
        <form method="GET" action="">
            <label for="filter_semester">Semester filtern:</label>
            <input type="text" name="filter_semester" id="filter_semester" 
                   value="<?= htmlspecialchars($filter_semester) ?>" placeholder="z.B. 1">
            <button type="submit">Filtern</button>
            
            <?php if ($filter_semester !== ''): ?>
                <a href="<?= $_SERVER['PHP_SELF'] ?>">
                    <button type="button">Filter aufheben</button>
                </a>
            <?php endif; ?>
        </form>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Fach</th>
                <th>Dozent</th>
                <th>Semester</th> 
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($subjects_result->num_rows > 0): ?>
                <?php while ($row = $subjects_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['dozent']) ?></td>
                        <td><?= htmlspecialchars($row['semester']) ?></td>
                        <td>
                            <button style="background:none;border:none;cursor:pointer;" 
                                    onclick='openModal("edit", <?= json_encode($row) ?>)'>✏️</button>
                            <form method="POST" action="" style="display:inline;" 
                                  onsubmit="return confirm('Fach wirklich löschen?');">
                                <input type="hidden" name="delete_id" value="<?= htmlspecialchars($row['faecher_id']) ?>">
                                <button type="submit" title="Löschen" style="background:none;border:none;cursor:pointer;">🗑️</button>
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
    <button id="add_subject" onclick="openModal('add')">Neues Fach hinzufügen</button>

    <div id="subjectModalOverlay" class="modal-overlay">
        <div id="modal-content">
            <h2 id="modalTitle">Fach hinzufügen</h2>
            
            <form id="add-subject-form" method="POST" action="">
                <input type="hidden" id="faecher_id" name="faecher_id">
                <input type="hidden" id="action" name="action" value="add">

                <label for="name">Fach:</label><br>
                <input type="text" id="name" name="name" required><br><br>

                <label for="dozent">Dozent:</label><br>
                <input type="text" id="dozent" name="dozent" required><br><br>

                <label for="semester">Semester:</label><br>
                <input type="text" id="semester" name="semester" required><br><br>

                <div class="modal-buttons">
                    <button type="submit">Speichern</button>
                    <button type="button" onclick="closeModal()">Abbrechen</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>