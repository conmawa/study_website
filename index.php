<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$is_invalid = false;
if($_SERVER["REQUEST_METHOD"] === "POST"){
    $mysqli = require 'database/database.php';
    $password = $_POST['password'];
    $email = $_POST['email'];

    $result = $mysqli->query("SELECT user_id, mail, password_hash, vorname, nachname FROM user WHERE mail='$email'");
    $user = $result->fetch_assoc();

    $hash_password_from_db = $user['password_hash'];
    
    if ($user){
        if (password_verify($password, $hash_password_from_db)){
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['vorname'] = $user['vorname'];
            $_SESSION['nachname'] = $user['nachname'];  
            header('Location: main.php');
            exit;
        }else{
            $is_invalid = true;
        }
    }else{
        $is_invalid = true;
    }
    
}

?>


<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <link rel = 'stylesheet' type = 'text/css' href = 'design\index.css' />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studium | Anmelden</title>
</head>

<body>
    <div class="login">
        
        <h1>Anmelden</h1>
            <?php if($is_invalid):?>
            <p>Email-Adresse oder Passwort ist falsch</p>
        <?php endif; ?>
        <form method="post">
            <label for="email">E-Mail</label>
            <input type="text" name="email" placeholder="E-Mail" id="email" required value="<?= htmlspecialchars( $_POST['email'] ?? "")?>">
            <br>
            <label for="password">Passwort</label>
            <input type="password" name="password" placeholder="Passwort" id="password" required>
            <!--<button onclick="show_password()">anzeigen/verstecken</button>-->
            <br>
            <input id="submit" type="submit" value="Anmelden">
        </form>
    </div>
</body>

</html>
