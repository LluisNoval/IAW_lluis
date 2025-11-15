<?php
session_start();
$preus = [1 => 39, 2 => 29];

// Calcular total
$total = 0;
foreach ($_SESSION['cistella'] ?? [] as $id => $q) {
    $total += $q * $preus[$id];
}
?>
<!doctype html>
<html lang="ca">
<head>
  <meta charset="utf-8">
  <title>Resum compra</title>
</head>
<body>
  <h1>Resum de la compra</h1>

  <?php if (empty($_SESSION['cistella'])): ?>
    <p>No has comprat res.</p>
  <?php else: ?>
    <ul>
      <?php foreach ($_SESSION['cistella'] as $id => $q):
            if ($q <= 0) continue;
            $nom = $id === 1 ? "Vi Les Terrasses" : "Priorat Selecció"; ?>
        <li><?= $nom ?> x <?= $q ?> = <?= $q * $preus[$id] ?> €</li>
      <?php endforeach; ?>
    </ul>
    <p><strong>Total: <?= $total ?> €</strong></p>
  <?php endif; ?>

  <form action="compra.php" method="post">
    <button type="submit">Confirmar compra</button>
  </form>
</body>
</html>