<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php require_once __DIR__ . '/partials/header.php'; ?>
    <div class="form-container">
        <h1>Login</h1>

        <?php require_once __DIR__ . '/partials/show_messages.php'; ?>

        <form action="login.php" method="post">
            <div class="form-group">
                <label for="login_identifier">Usuari o Correu Electrònic:</label>
                <input type="text" id="login_identifier" name="login_identifier" pattern="[a-zA-Z0-9_@.]+" title="Només lletres, números i els caràcters _ @ ." required>
            </div>
            <div class="form-group">
                <label for="password">Contrasenya:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Inicia sessió</button>
        </form>
        <div class="register-section">
            <p>No tens un compte? <a href="register.php">Crea un compte</a></p>
        </div>
        <div class="general-accounts" style="margin-top: 20px; padding: 15px; background-color: #f0f0f0; border-radius: 5px; text-align: center;">
            <h3 style="margin-top:0;">Dades per a proves</h3>
            <p style="margin: 5px 0;"><strong>Rol Admin:</strong><br>Usuari: admin<br>Contrasenya: admin1234</p>
        </div>
    </div>
</body>
</html>