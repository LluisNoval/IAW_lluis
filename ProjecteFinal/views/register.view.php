<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registra't</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="form-container">
        <h1>Crea un Compte</h1>
        
        <?php require_once __DIR__ . '/partials/show_messages.php'; ?>

        <form action="register.php" method="post">
            <div class="form-group">
                <label for="username">Nom d'usuari:</label>
                <input type="text" id="username" name="username" pattern="[a-zA-Z0-9_]+" required>
            </div>
            <div class="form-group">
                <label for="email">Correu electrònic:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Contrasenya:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirma la contrasenya:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">Registra't</button>
        </form>
        <div class="login-section">
            <p>Ja tens un compte? <a href="login.php">Inicia sessió</a></p>
        </div>
    </div>
</body>
</html>