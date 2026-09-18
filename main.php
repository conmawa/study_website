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
        <iframe src="https://calendar.google.com/calendar/embed?height=600&wkst=2&ctz=Europe%2FBerlin&showPrint=0&showTabs=0&showTz=0&mode=WEEK&showTitle=0&src=MjFmNmRmNTNiNjRhYjM3MTcxNTQxOTk0OWM2Y2QwMjFjODMxYTNmZjg4YTE4YzkwMGQyOTlhZTQyZWZmZmVlMUBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&src=ZWJhZjlhZWE4ZWY2N2VkNjFkMDQ2Yzc3MTVjNTZkMjJkNTdhOWE4NjE5YmQ4YTI4ZGMxODYwYzI1N2ExNTdkNUBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&src=MTBxNG4xdjJxMHNzZjBwaW1tYWI3cTM1ZHJwdmpxMHJAaW1wb3J0LmNhbGVuZGFyLmdvb2dsZS5jb20&color=%23e67c73&color=%23a79b8e&color=%23e67c73" style="border-width:0" width="800" height="600" frameborder="0" scrolling="no"></iframe>
    </div>

    <div class="noten">
        <?php
            include 'marks/marks.php';
        ?>
    </div>
    
    <div class="faecher">
        <?php
            include 'subjects/subject.php';
        ?>
    </div>
        
</body>
</html>