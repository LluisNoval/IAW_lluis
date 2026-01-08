<?php
// views/admin_users.view.php
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestió d'Usuaris - Admin</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .admin-container { padding: 20px; max-width: 1000px; margin: 0 auto; }
        /* Override specific table styles if needed, but rely on styles.css */
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/partials/header.php'; ?>
    <?php require_once __DIR__ . '/partials/show_messages.php'; ?>

    <div class="admin-container">
        <h1>Gestió d'Usuaris i Rols</h1>
        
        <!-- BUSCADOR -->
        <form action="admin_users.php" method="GET" style="margin-bottom: 20px; display: flex; gap: 10px;">
            <input type="text" name="search" placeholder="Cercar per nom o email..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="flex: 1; padding: 10px;">
            <button type="submit" style="width: auto; margin: 0;">Cercar</button>
            <?php if(!empty($_GET['search'])): ?>
                <a href="admin_users.php" class="btn" style="padding: 10px; background: #95a5a6; color: white; text-decoration: none; border-radius: 6px; display: flex; align-items: center;">Netejar</a>
            <?php endif; ?>
        </form>

        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rol Actual</th>
                        <th>Accions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($usuaris)): ?>
                        <tr><td colspan="5" class="text-center">No s'han trobat usuaris.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($usuaris as $u): ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td><?php echo htmlspecialchars($u['nom']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td><strong><?php echo htmlspecialchars($u['role_name']); ?></strong></td>
                            <td>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <!-- FORMULARI CANVI ROL -->
                                    <form action="admin_users.php" method="POST" style="display: flex; gap: 5px; margin: 0;">
                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                        <select name="new_role_id" style="padding: 5px;">
                                            <?php foreach ($tots_els_rols as $rol): ?>
                                                <option value="<?php echo $rol['id']; ?>" <?php echo ($rol['id'] == $u['role_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($rol['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" name="update_role" style="width: auto; padding: 5px 10px; margin: 0; font-size: 0.9em;">Guardar</button>
                                    </form>

                                    <!-- BOTÓ ELIMINAR -->
                                    <form action="admin_users.php" method="POST" style="margin: 0;" onsubmit="return confirm('Segur que vols eliminar aquest usuari?');">
                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                        <button type="submit" name="delete_user" style="width: auto; padding: 5px 10px; margin: 0; background-color: #e74c3c; font-size: 0.9em;">Eliminar</button>
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