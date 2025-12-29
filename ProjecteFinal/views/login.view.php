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
        <div class="general-accounts" style="margin-top: 20px; padding: 15px; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 8px; font-size: 0.9em;">
            <h3 style="margin-top:0; text-align: center; color: #2c3e50;">Dades per a proves</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div style="background: white; padding: 10px; border-radius: 4px; border: 1px solid #eee;">
                    <strong>Administrador:</strong><br>
                    Usuari: <code>admin</code><br>
                    Pass: <code>admin1234</code>
                </div>
                <div style="background: white; padding: 10px; border-radius: 4px; border: 1px solid #eee;">
                    <strong>Client de prova:</strong><br>
                    Usuari: <code>joan@gmail.com</code><br>
                    Pass: <code>admin1234</code>
                </div>
                <div style="background: white; padding: 10px; border-radius: 4px; border: 1px solid #eee;">
                    <strong>Cuiner 1:</strong><br>
                    Usuari: <code>Ferran</code><br>
                    Pass: <code>cuiner1234</code>
                </div>
                <div style="background: white; padding: 10px; border-radius: 4px; border: 1px solid #eee;">
                    <strong>Cuiner 2:</strong><br>
                    Usuari: <code>Carme</code><br>
                    Pass: <code>cuiner1234</code>
                </div>
            </div>
        </div>
    </div>
</body>
</html>