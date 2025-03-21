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
        <iframe src="https://calendar.google.com/calendar/embed?height=600&wkst=2&ctz=Europe%2FBerlin&showPrint=0&showTitle=0&src=OWQ0ZDJmMzgyMTYwMWE3OTIxNTA1ZmYxOWIyMWQ0Njk5MDFiMzBiMzFjOGMyZmQ2NjI3YjEyOGIwMmMxMDUzOEBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&color=%233F51B5" style="border:solid 1px #777" width="800" height="800" frameborder="0" scrolling="yes"></iframe>
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