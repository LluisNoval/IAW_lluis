<?php
session_start();
session_unset();               // buida variables
session_destroy();             // destrueix sessió
?>
<!doctype html>
<html lang="ca">
<head>
  <meta charset="utf-8">
  <title>Gràcies</title>
</head>
<body>
  <h1>Gràcies per la teva compra!</h1>
  <p><a href="index.html">Tornar a la botiga</a></p>
</body>
</html>