<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resum Ingredients</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php require_once __DIR__ . '/partials/header.php'; ?>
    
    <div class="container">
        <h1>Resum d'Ingredients i Estoc</h1>
        <?php require_once __DIR__ . '/partials/show_messages.php'; ?>
        
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Ingredient</th>
                        <th>Stock Actual</th>
                        <th>Necessari (Comandes Actives)</th>
                        <th>Diferència</th>
                        <th>Estat</th>
                        <?php if($_SESSION['rol'] === 'admin'): ?>
                            <th>Accions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dades_ingredients as $ing): ?>
                        <tr style="<?= $ing['falta'] ? 'background-color: #ffe6e6;' : '' ?>">
                            <td data-label="Ingredient"><?= htmlspecialchars($ing['nom']) ?></td>
                            <td data-label="Stock"><?= $ing['stock'] ?> <?= $ing['unitat'] ?></td>
                            <td data-label="Necessari"><?= $ing['necessari'] ?> <?= $ing['unitat'] ?></td>
                            <td data-label="Diferència">
                                <span style="<?= $ing['falta'] ? 'color:red; font-weight:bold;' : 'color:green;' ?>">
                                    <?= $ing['diferencia'] ?> <?= $ing['unitat'] ?>
                                </span>
                            </td>
                            <td data-label="Estat">
                                <?php if ($ing['falta']): ?>
                                    <span style="color:red; font-weight: bold;">FALTA STOCK</span>
                                <?php else: ?>
                                    <span style="color:green;">OK</span>
                                <?php endif; ?>
                            </td>
                            <?php if($_SESSION['rol'] === 'admin'): ?>
                                <td>
                                    <form method="POST" action="ingredients_resum.php" style="display:flex; gap:5px; margin:0;">
                                        <input type="hidden" name="ingredient_id" value="<?= $ing['id'] ?>">
                                        <input type="number" step="0.01" name="new_stock" placeholder="Nou Stock" style="width: 80px; padding: 5px;" required>
                                        <button type="submit" name="update_stock" style="width: auto; padding: 5px 10px; font-size: 0.8rem; margin:0;">Actualitzar</button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
