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
  <title>Info 1</title>
</head>
<body>
  <p>Hola, <strong><?= $_SESSION['user'] ?></strong>! | 
     <a href="info2.php">Anar a Info 2</a></p>

  <h1>Pàgina d’informació 1</h1>
  <p>Contingut privat 1.</p>
</body>
</html>