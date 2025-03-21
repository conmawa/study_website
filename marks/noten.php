<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mysqli = require __DIR__ . '/../database/database.php';

// Fetch subjects
$subjects_sql = "SELECT * FROM faecher";
$subjects_result = $mysqli->query($subjects_sql);

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['name']) && !empty($_POST['wert'])) {
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
    <form method="post" action="">
        <label for="fach">Fach:</label>
        <select name="fach" id="fach" required>
            <?php
            if ($subjects_result->num_rows > 0) {
                while($row = $subjects_result->fetch_assoc()) {
                    echo "<option value='" . $row['name'] . "'>" . $row['name'] . "</option>";
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