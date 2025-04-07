<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type = 'text/css' href = '../design\main.css' />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studium | Stundenplan</title>
</head>
<body>
    <h1>Stundenplan</h1>
    <table>
        <tr>
            <th>Uhrzeit</th>
            <th>Montag</th>
            <th>Dienstag</th>
            <th>Mittwoch</th>
            <th>Donnerstag</th>
            <th>Freitag</th>
        </tr>
        <?php
        $startTime = strtotime("08:00");
        $endTime = strtotime("20:00");
        while ($startTime < $endTime) {
            $endSlot = strtotime("+90 minutes", $startTime);
            $breakStart = $endSlot;
            $breakEnd = strtotime("+20 minutes", $breakStart);
            echo "<tr>";
            echo "<td>" . date("H:i", $startTime) . " - " . date("H:i", $endSlot) . "</td>";
            for ($i = 0; $i < 5; $i++) {
                echo "<td></td>";
            }
            echo "</tr>";
            $startTime = $breakEnd;
        }
        ?>
    </table>
</body>
</html>