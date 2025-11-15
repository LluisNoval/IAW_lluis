<?php
/* ---- comptador de visites ---- */
$cookie = 'visites';
$v = isset($_COOKIE[$cookie]) ? (int)$_COOKIE[$cookie] + 1 : 1;
setcookie($cookie, $v, time() + 365*24*60*60);   // 1 any
?>
<!doctype html>
<html lang="ca">
<head>
  <meta charset="utf-8">
  <title>Comptador de visites</title>
</head>
<body>
  <h1>Comptador de visites</h1>
  <p>Has accedit <strong><?= $v ?></strong> vegades a aquesta pàgina.</p>
</body>
</html>