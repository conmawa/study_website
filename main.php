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
    <link rel="stylesheet" type = 'text/css' href = 'design\main.css'/>
    <link rel="icon" type="image/x-icon" href="design\favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://apis.google.com/js/api.js"></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <title>Studium | Home</title>
</head>

<body>
    <br>
    <h1 id="head">Willkommen <?php echo $_SESSION['vorname'] ?></h1>
    <br>
    <div class="calender">
        <?php
            include 'calender/calender.html';
        ?>
    </div>

    <div class="stundenplan">
        <?php
            include 'plan/stundenplan.php';
        ?>
    </div>

    <div class="noten">
        <?php
            include 'marks/noten.php';
        ?>
    </div>
    
    <div class="faecher">
        <?php
            include 'subjects/subject2.php';
        ?>
    </div>
        
</body>
</html>