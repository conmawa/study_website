<?php
$is_invalid = false;
if($_SERVER["REQUEST_METHOD"] === "POST"){
    $mysqli = require __DIR__ . "/database.php";
    $password = $_POST['password'];
    $email = $_POST['email'];

    $result = $mysqli->query("SELECT id, email, password FROM user WHERE email='$email'");
    $user = $result->fetch_assoc();
    $hash_password_from_db=$user['password'];
    
    if ($user){
        if (password_verify($password, $hash_password_from_db)){
            session_start();
            $_SESSION['user_id'] = $user['id'];
            header('Location: ../index.php');
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
    <script defer src="../js/show.js"></script>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/everything.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informatik-Hefter | Anmelden</title>
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
            <p>Sie haben noch kein Konto? <a id="register" href="register.php">Registrieren</a></p>
            <br>
        </form>
    </div>
</body>
<footer>
    <p id="footerheadline1">Impressum:</p>
    <p id="footerheadline2">Name</p>
    <p id="footerinhalt">Constantin Liepelt</p>
    <p id="footerheadline2">Anschrift</p>
    <p id="footerinhalt">Johann-Meyer-Straße 5</p>
    <p id="footerinhalt">01097, Dresden</p>
    <p id="footerheadline2">E-Mail</p>
    <p id="footerinhalt"><a id="myemail" href="mailto:constantin@liepelts.de">constantin@liepelts.de</a></p>
    <p id="footerheadline2">Telefonnummer</p>
    <p id="footerinhalt">+49 1577 538 4556</p>

</footer>
</html>
