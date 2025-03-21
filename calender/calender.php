<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$exams = [];

$mysqli = require __DIR__ . '/../database/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] === 'add' && !empty($_POST['exam_name']) && !empty($_POST['Fach']) && !empty($_POST['exam_date'])) {
        $exam_name = $mysqli->real_escape_string($_POST['exam_name']);
        $fach = $mysqli->real_escape_string($_POST['Fach']);
        $exam_date = $mysqli->real_escape_string($_POST['exam_date']);

        $insert_sql = "INSERT INTO exams (exam_name, exam_date, faecher_id) VALUES ('$exam_name', '$exam_date', (SELECT faecher_id FROM faecher WHERE name =  '$fach'))";
        if ($mysqli->query($insert_sql) === TRUE) {
            echo "Neue Prüfung hinzugefügt";
        } else {
            echo "Error: " . $insert_sql . "<br>" . $mysqli->error;
        }
    }   

    if ($_POST['action'] === 'delete' && !empty($_POST['exam_name'])) {
        $exam_name = $_POST['exam_name'];
        $delete_sql = "DELETE FROM exams WHERE name = ?";
        $stmt = $mysqli->prepare($delete_sql);
        $stmt->bind_param("i", $exam_name);
        if ($stmt->execute()) {
            echo "Prüfung gelöscht";
        } else {
            echo "Fehler: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch exams from the database
$sql = "SELECT exam_id, exam_name, exam_date FROM exams";
$result = $mysqli->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $exams[] = $row;
    }
    $result->free();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studium | Kalender</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
    <link rel="stylesheet" href="../design/main.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
</head>
<body>
    <h1>Kalender</h1>
    <div id="calendar"></div>
    <br>
    <br>

    <form id="examForm" method="POST" action="">
        <input type="hidden" name="action" value="add">
        <label for="exam_name">Prüfungsname:</label>
        <input type="text" id="exam_name" name="exam_name" required>
        <br><br>
        <label for="Fach">Fach:</label>
        <input type="text" id="Fach" name="Fach" required>
        <br><br>
        <label for="exam_date">Prüfungsdatum:</label>
        <input type="date" id="exam_date" name="exam_date" required>
        <br><br>
        <input type="submit" value="Prüfung hinzufügen">
    </form>

    <h2>Prüfung löschen</h2>
    <form id='deleteExamForm' method="POST" action="">
        <input type="hidden" name="action" value="delete">
        <label for="exam_name">Prüfungs-Name:</label>
        <input type="text" id="exam_name" name="exam_name" required>
        <br><br>
        <input type="submit" value="Prüfung löschen">

    <script>
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                firstDay: 1,
                events: [
                    <?php foreach ($exams as $index => $exam): ?>{
                        title: "<?php echo htmlspecialchars($exam['exam_name']); ?>",
                        start: "<?php echo $exam['exam_date']; ?>"
                    }<?php if ($index < count($exams) - 1) echo ','; ?>
                    <?php endforeach; ?>
                ]
            });
        });
    </script>
</body>
</html>
