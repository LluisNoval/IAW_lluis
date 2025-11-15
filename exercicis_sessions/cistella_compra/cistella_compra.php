<?php
session_start();

// Preus
$preus = [1 => 39, 2 => 29];

// Afegir quantitats a la sessió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['cistella'][1] = ($_SESSION['cistella'][1] ?? 0) + (int)($_POST['q1'] ?? 0);
    $_SESSION['cistella'][2] = ($_SESSION['cistella'][2] ?? 0) + (int)($_POST['q2'] ?? 0);
}
?>
<!doctype html>
<html lang="ca">
<head>
  <meta charset="utf-8">
  <title>La meva cistella</title>
</head>
<body>
  <h1>La meva cistella</h1>

  <?php if (empty($_SESSION['cistella'])): ?>
    <p>Cap producte encara.</p>
  <?php else: ?>
    <ul>
      <?php foreach ($_SESSION['cistella'] as $id => $q):
            if ($q <= 0) continue;
            $nom = $id === 1 ? "Vi Les Terrasses" : "Priorat Selecció"; ?>
        <li><?= $nom ?> x <?= $q ?> (<?= $preus[$id] ?> €/ud)</li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form action="cistella_compra.php" method="post">
    <h3>Vi Les Terrasses - 39 €</h3>
    Quantitat: <input type="number" name="q1" min="0" value="0">

    <h3>Priorat Selecció - 29 €</h3>
    Quantitat: <input type="number" name="q2" min="0" value="0">
    <br><br>
    <button type="submit">Afegir més</button>
    <a href="surt.php">Finalitzar compra</a>
  </form>
</body>
</html>