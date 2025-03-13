<?php
session_start();
?>


<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/nav2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/everything.css">
    <script src="js/menu.js" defer></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informatik-Hefter | Home</title>
</head>

<body>
    <br>
    <h1>Informatik-Hefter am GDP</h1> 

    <?php if (isset($_SESSION['user_id'])): ?>

        <header class="header">
        <div class="container">
            <div class="menu">
                <div class="head">
                    <button type="button" class="close-menu-btn"></button>
                </div>
                <ul>
                    <li><a href="">Home</a></li>
                    <li><a href="#">Klasse 7</a></li>
                    <li><a href="#">Klasse 8</a></li>
                    <li><a href="#">Klasse 9</a></li>
                    <li class="dropdown"><a href="Unterrichtsthemen/klasse_10/klasse_10.php">Klasse 10</a>
                        <i class="fa-solid fa-chevron-down"></i>
                        <ul class="sub-menu">
                            <li class="dropdown"><a href="Unterrichtsthemen/klasse_10/sprachen.php"><span>Sprachen</span></a>
                                <i class="fa-solid fa-chevron-down"></i>
                                <ul class="sub-menu sub-menu-right">
                                    <li><a href="Unterrichtsthemen/klasse_10/sprachen.php#unterschied_natuerliche_und_formale_sprache"><span>Unterschied natürliche und formale Sprache</span></a></li>
                                    <li><a href="Unterrichtsthemen/klasse_10/sprachen.php#alphabet_und_sprache"><span>Alphabet und Sprache</span></a></li>
                                    <li><a href="Unterrichtsthemen/klasse_10/sprachen.php#formale_sprache"><span>Formale Sprachen</span></a></li>
                                    <li><a href="Unterrichtsthemen/klasse_10/sprachen.php#regulaere_ausdruecke"><span>Reguläre Ausdrücke</span></a></li>
                                    <li><a href="Unterrichtsthemen/klasse_10/sprachen.php#endliche_automaten"><span>Endliche Automaten</span></a></li>
                                    <li><a href="Unterrichtsthemen/klasse_10/sprachen.php#auszeichnungssprachen"><span>Auszeichnungssprachen</span></a></li>
                                    <li><a href="Unterrichtsthemen/klasse_10/sprachen.php#HTML"><span>HTML</span></a></li>
                                </ul>
                            </li>
                            <li class="dropdown"><a href="Unterrichtsthemen/klasse_10/netzwerke.php"><span>Netzwerke</span></a>
                                <i class="fa-solid fa-chevron-down"></i>
                                <ul class="sub-menu sub-menu-right">
                                    <li><a href="Unterrichtsthemen/klasse_10/netzwerke.php#netzwerkgeraete"><span>Netzwerkgeräte</span></a></li>
                                    <li><a href="Unterrichtsthemen/klasse_10/netzwerke.php#netzwerkverbindung"><span>Netzwerkverbindung</span></a></li>
                                    <li><a href="Unterrichtsthemen/klasse_10/netzwerke.php#andere_loesungen_fuer_das_netzwerkproblem"><span>Andere Lösungen für das Netzverkproblem</span></a></li>
                                    <li><a href="Unterrichtsthemen/klasse_10/netzwerke.php#netzwerkarchitekturen"><span>Netzwerkarchitekturen</span></a></li>
                                    <li><a href="Unterrichtsthemen/klasse_10/netzwerke.php#protokolle-dienste"><span>Protokolle und Dienste</span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown"><a href="klasse_11.php">Klasse 11</a>
                        <i class="fa-solid fa-chevron-down"></i>
                        <ul class="sub-menu">
                            <li class="dropdown"><a href="Unterrichtsthemen/klasse_11/technische_informatik.php"><span>Technische Informatik</span></a>
                                <i class="fa-solid fa-chevron-down"></i>
                                <ul class="sub-menu sub-menu-left">
                                    <li class="dropdown"><a href="Unterrichtsthemen/klasse_11/grundlagen_it.html"><span>Grundlagen</span></a>
                                        <i class="fa-solid fa-chevron-down"></i>
                                        <ul class="sub-menu sub-menu-left">
                                            <li><a href="Unterrichtsthemen/klasse_11/grundlagen_ti.html#zahlensysteme"><span>Zahlensysteme</span></a></li>
                                            <li><a href="Unterrichtsthemen/klasse_11/grundlagen_ti.html#kodierung"><span>Kodierung von Zeichen</span></a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown"><a href="Unterrichtsthemen/klasse_11/algorithmen_programmierung.php"><span>Algorithmen und Programmierung</span></a>
                                <i class="fa-solid fa-chevron-down"></i>
                                <ul class="sub-menu sub-menu-left">
                                    <li class="dropdown"><a href="Unterrichtsthemen/klasse_11/grundlagen_ap.php"><span>Grundlagen</span></a>
                                        <i class="fa-solid fa-chevron-down"></i>
                                        <ul class="sub-menu sub-menu-left">
                                            <li><a href="Unterrichtsthemen/klasse_11/grundlagen_ap.php#klassifizierung"><span>Klassifizierung von Programmiersprachen</span></a></li>
                                            <li><a href="Unterrichtsthemen/klasse_11/grundlagen_ap.php#kontrollstrukturen"><span>Kontrollstrukuren</span></a></li>
                                            <li><a href="Unterrichtsthemen/klasse_11/grundlagen_ap.php#algorithmen"><span>Algorithmen</span></a></li>
                                            <li><a href="Unterrichtsthemen/klasse_11/grundlagen_ap.php#unterprogramme"><span>Unterprogramme</span></a></li>
                                            <li><a href="Unterrichtsthemen/klasse_11/grundlagen_ap.php#w_und_r"><span>Werte- und Referenzparamter</span></a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown"><a href="Unterrichtsthemen/klasse_11/komplexitaet.html"><span>Komplexität</span></a>
                                        <i class="fa-solid fa-chevron-down"></i>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li id="logout"><a href="register_form/logout.php">Abmelden</a>
                </ul>
            </div>
            <div class="header-right">
                <button type="button" class="open-menu-btn">
                    <span class="line line-1"></span>
                    <span class="line line-2"></span>
                    <span class="line line-3"></span>
                </button>
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
        <a id="login" href="register_form/login.php">Anmelden</a>
    <?php endif; ?>
    
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
<html>