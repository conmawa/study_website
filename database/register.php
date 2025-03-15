<?php
if($_SERVER["REQUEST_METHOD"] === "POST"){

    $password_check = false;

    $vorname = $_POST['vorname'];
    $nachname = $_POST['nachname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];

    if ($password === $repassword) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);


        $mysqli = require __DIR__ . "/database.php";

        $sql = "INSERT INTO user (vorname, nachname, mail, password_hash) VALUES(?,?,?,?)";

        $stmt = $mysqli->stmt_init();

        if (!$stmt->prepare($sql)){
            die("SQL error: " . $mysqli->error);
        }

        $stmt->bind_param("ssss", $vorname, $nachname, $email, $password_hash);

        if ($stmt->execute()){
            header('Location: /.../index.php');
            exit;
        } else {
            if ($mysqli->errno === 1062) {
                die("Email-Adresse bereits vergeben");
            } else {
                die($mysqli->error . " " . $mysqli->errno);
            }
        }
    }else{
        $password_check = true;
    }
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/register.css">
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/everything.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informatik-Hefter | Registrieren</title>
</head>
<body>
    <div class="register">
        <h1>Registrieren</h1>
            <?php if($password_check):?>
            <p>Passwörter müssen übereinstimmen!</p>
        <?php endif; ?>
        <form method="post">
            <label for="vorname">Vorname</label>
            <input type="text" name="vorname" id="vorname" required value="<?= htmlspecialchars( $_POST['vorname'] ?? "")?>">
            <br>
            
            <label for="nachname">Nachname</label>
            <input type="text" name="nachname" id="nachname" required value="<?= htmlspecialchars( $_POST['nachname'] ?? "")?>">
            <br>
            
            <label for="email">E-Mail</label>
            <input type="text" name="email" id="email" required value="<?= htmlspecialchars( $_POST['email'] ?? "")?>">
            <br>
            
            <label for="password">Passwort</label>
            <input type="password" name="password" id="password" required>
            
            <br>
            <label for="repassword">Passwort Wiederholen</label>
            <input type="password" name="repassword" id="repassword" required>
            
            <br>
            <input id="submit" type="submit" value="Registrieren" required>
            <div class="alacc">
                <p id="alacc">Sie haben schon ein Konto? <a href="login.php">Anmelden</a></p>
            </div>
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