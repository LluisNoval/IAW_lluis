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
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:hover { background-color: #f9f9f9; }
        .role-select { padding: 5px; border-radius: 4px; }
        .btn-update { 
            background-color: #3498db; 
            color: white; 
            padding: 5px 10px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer;
            font-size: 0.9em;
            width: auto;
            margin-top: 0;
        }
        .btn-update:hover { background-color: #2980b9; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/partials/header.php'; ?>
    <?php require_once __DIR__ . '/partials/show_messages.php'; ?>

    <div class="admin-container">
        <h1>Gestió d'Usuaris i Rols</h1>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rol Actual</th>
                    <th>Canviar Rol</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuaris as $u): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['nom']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><strong><?php echo htmlspecialchars($u['role_name']); ?></strong></td>
                        <td>
                            <form action="admin_users.php" method="POST" style="display: flex; gap: 10px; align-items: center;">
                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                <select name="new_role_id" class="role-select">
                                    <?php foreach ($tots_els_rols as $rol): ?>
                                        <option value="<?php echo $rol['id']; ?>" <?php echo ($rol['id'] == $u['role_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($rol['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="update_role" class="btn-update">Actualitzar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
