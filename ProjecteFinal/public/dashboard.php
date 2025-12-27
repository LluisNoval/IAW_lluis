<?php
// Control de sessió.
session_start();

// Comprova si l'usuari ha iniciat sessió.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no, el redirigeix al login.
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <?php require_once __DIR__ . '/../views/partials/header.php'; ?>
    <h1>Benvingut/da, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    
    <a href="logout.php">Tancar sessió</a>

</body>
</html>