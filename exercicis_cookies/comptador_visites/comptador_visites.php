<?php
/* ---------- CONFIG ---------- */
$cookie_visites = 'visites';
$cookie_compra  = 'ha_comprat';
$any            = 365 * 24 * 3600;

/* ---------- LÒGICA ---------- */
$visites = isset($_COOKIE[$cookie_visites]) ? (int)$_COOKIE[$cookie_visites] + 1 : 1;
setcookie($cookie_visites, $visites, time() + $any);

/* Si arriba el formulari de “comprar” -> marquem la cookie */
if (isset($_GET['comprar'])) {
    setcookie($cookie_compra, 'true', time() + $any);
    $_COOKIE[$cookie_compra] = 'true';   // perquè funcioni ja en aquesta petició
}

$ha_comprat = isset($_COOKIE[$cookie_compra]);

/* Decidim el missatge */
if (!$ha_comprat) {
    if ($visites >= 10) {
        $msg = "Oferta exclusiva sols per a tu! Utilitza el codi <b>BOTIGA50</b> per obtenir un 50% de descompte";
    } elseif ($visites >= 5) {
        $msg = "Oferta exclusiva! Utilitza el codi <b>BOTIGA20</b> per obtenir un 20% de descompte";
    } else {
        $msg = "Has visitat la tenda $visites vegades.";
    }
} else {
    $msg = "Has visitat la tenda $visites vegades.";
}
?>
<!doctype html>
<html lang="ca">
<head>
  <meta charset="utf-8">
  <title>Tenda</title>
</head>
<body>
  <h1>Tenda</h1>
  <h3>Amb comptador de visites!!</h3>

  <p><?= $msg ?></p>

  <!-- Formulari sense JS -->
  <form action="" method="get">
    <label>Codi descompte:</label>
    <input type="text" name="codi">
    <button type="submit" name="aplicar">Aplica descompte</button>
    <br><br>
    <button type="submit" name="comprar">Compra</button>
  </form>

  <?php
  /* Missatge addicional després de prémer un botó */
  if (isset($_GET['aplicar'])) {
      $codi = htmlspecialchars($_GET['codi']);
      echo "<p>Has aplicat el codi: <b>$codi</b></p>";
  }
  if (isset($_GET['comprar'])) {
      echo "<p>Compra realitzada!</p>";
  }
  ?>
</body>
</html>