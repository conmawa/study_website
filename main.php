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
        <iframe src="https://calendar.google.com/calendar/embed?height=600&wkst=2&ctz=Europe%2FBerlin&showPrint=0&src=Y29ubWF3YUBnbWFpbC5jb20&src=MzM4NDVhMmYzZTRlZmMwMGQ1YWZjMjBjODY3OWY2YjJlODM0NzFkYzIzYjY1MDc2NjY3MjQ0NTNhNjAwMDRkNkBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&src=NzI1NTE5MTFiZjg2ZjVmZjA0YjVmNjY1YjFmNWY3OTYwMmU1ZTIxYjgxYWEyNzU3ZjFiNTI4YWRiOTlkN2Q4NUBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&src=Mzc4ZDQ1NmU0OGRhNzcwMzg0ODQ4MTJmMzlmYmNlMDk1ZGRmZWEwYmVlYjg0OTRjM2M1YWI2NDVlMWM0MjNlNUBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&src=OWQ0ZDJmMzgyMTYwMWE3OTIxNTA1ZmYxOWIyMWQ0Njk5MDFiMzBiMzFjOGMyZmQ2NjI3YjEyOGIwMmMxMDUzOEBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&src=Njg4NDE1ODAzOWExOThjODc5ZWE4MTg2MTJkZWNmOGFjN2QyZDdhMzgyOTNkZTQzMmQ2NzZjZGNlY2I4Nzg1NkBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&src=ZmFtaWx5MDQxMzE3MjkwMzUwOTc1MzcyNDBAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ&src=ZGUuZ2VybWFuI2hvbGlkYXlAZ3JvdXAudi5jYWxlbmRhci5nb29nbGUuY29t&color=%23D81B60&color=%23616161&color=%237CB342&color=%23E67C73&color=%233F51B5&color=%239E69AF&color=%237986CB&color=%23009688"
            style="border:solid 1px #777" width="800" height="600" frameborder="0" scrolling="no"></iframe>
    </div>

    <div class="noten">
        <?php
        include 'marks/noten.php';
        ?>
    </div>

    <div class="stundenplan">
    <?php
        include 'plan/stundenplan.php';
        ?>
    </div>
    
        
</body>
</html>