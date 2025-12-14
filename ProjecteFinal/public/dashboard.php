<?php
session_start();

// Comprovem si l'usuari està logat
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login/login.html"); // Redirigim al login si no està logat
    exit();
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dintre</title>
</head>
<body>
    <h1 bold>Benvingut <?php echo $_SESSION['username']; ?></h1>
    <a href="../login/logout.php">Tancar sessió</a>

</body>
</html>