<?php
session_start();

// Qualsevol usuari i pwd si són iguals
if ($_POST['user'] === $_POST['pwd'] && $_POST['user'] !== '') {
    $_SESSION['user'] = $_POST['user'];
    header('Location: info1.php');
    exit;
}

header('Location: index.html');
exit;