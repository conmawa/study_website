<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type = 'text/css' href = 'design\main.css' />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studium | Home</title>
</head>

<body>
    <br>
 
    <div class="calender">
        <?php
        include 'calender/calender.php';
        ?>
    </div>

    <div class="noten">
        <?php
        include 'marks/noten.php';
        ?>
    </div>

    <div class="stundenplan">
        <h2>Stundenplan</h2>
    </div>
    
        
</body>
</html>