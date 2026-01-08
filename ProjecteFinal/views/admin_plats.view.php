<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestió de Plats</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php require_once __DIR__ . '/partials/header.php'; ?>
    <div class="container">
        <h1>Gestió de la Carta (CRUD Plats)</h1>
        <?php require_once __DIR__ . '/partials/show_messages.php'; ?>

        <!-- FORMULARI CREAR -->
        <div class="form-container" style="max-width: 100%; margin: 0 0 30px 0;">
            <h3>Afegir Nou Plat</h3>
            <form method="POST" action="admin_plats.php" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end;">
                <div style="flex: 2; min-width: 200px;">
                    <label>Nom del Plat</label>
                    <input type="text" name="nom" required style="width: 100%;">
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label>Categoria</label>
                    <select name="categoria_id" style="width: 100%; padding: 10px;">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1; min-width: 100px;">
                    <label>Preu (€)</label>
                    <input type="number" step="0.01" name="preu" required style="width: 100%;">
                </div>
                <div style="flex: 3; min-width: 300px;">
                    <label>Descripció</label>
                    <input type="text" name="descripcio" placeholder="Ingredients principals..." style="width: 100%;">
                </div>
                <button type="submit" name="create_plat" style="width: auto; height: fit-content; margin: 0;">Afegir Plat</button>
            </form>
        </div>

        <!-- TAULA LLISTAT -->
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th>Nom</th>
                        <th>Descripció</th>
                        <th>Preu</th>
                        <th>Disponible</th>
                        <th>Accions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($plats as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['cat_nom']) ?></td>
                            <td><?= htmlspecialchars($p['nom']) ?></td>
                            <td><small><?= htmlspecialchars($p['descripcio']) ?></small></td>
                            
                            <!-- FORMULARI EDICIÓ RÀPIDA -->
                            <form method="POST" action="admin_plats.php">
                                <input type="hidden" name="plat_id" value="<?= $p['id'] ?>">
                                <td>
                                    <input type="number" step="0.01" name="preu" value="<?= $p['preu'] ?>" style="width: 70px;"> €
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" name="disponible" <?= $p['disponible'] ? 'checked' : '' ?>>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 5px;">
                                        <button type="submit" name="update_plat" style="width: auto; padding: 5px; font-size: 0.8em; background: #27ae60;">Guardar</button>
                            </form>
                                        <form method="POST" action="admin_plats.php" onsubmit="return confirm('Eliminar aquest plat definitivament?');" style="margin:0;">
                                            <input type="hidden" name="plat_id" value="<?= $p['id'] ?>">
                                            <button type="submit" name="delete_plat" style="width: auto; padding: 5px; font-size: 0.8em; background: #c0392b;">X</button>
                                        </form>
                                    </div>
                                </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
