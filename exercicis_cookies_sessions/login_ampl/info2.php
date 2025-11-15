<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.html');
    exit;
}
?>
<!doctype html>
<html lang="ca">
<head>
  <meta charset="utf-8">
  <title>Info 2</title>
</head>
<body>
  <p>
    Hola, <strong><?= $_SESSION['user'] ?></strong> |
    <a href="info1.php">Anar a Info 1</a> |
    <a href="logout.php">Sortir</a>
  </p>

  <h1>Pàgina d'informació 2</h1>
  <p>Contingut privat 2.</p>
</body>
</html>