<?php
session_start();
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
    <?php if (isset($_SESSION['user_id'])): ?>
        <header class="header">
            <div class="container">
                <div class="menu">
                    <ul">
                        <li><a href="">Home</a></li>
                        <li><a href="#">Kalender</a></li>
                        <li><a href="#">Noten</a></li>
                        <li><a href="#">Stundenplan</a></li>
                        <li id="logout"><a href="database/logout.php">Abmelden</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <br>
        <br>

        <p id="index">
            hi
        </p>
        
    <?php else: ?>
            <p id="login">Bitte melden Sie sich an, um den Inhalt sehen zu können!</p>
            <a id="login" href="index.php">Anmelden</a>
    <?php endif; ?>
        
</body>
<html>