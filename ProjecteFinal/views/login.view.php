<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
    <div class="login-container">
        <h1 class="titol-login">Login</h1>
        <form action="public/login.php" method="post">
            <div class="form-group">
                <label for="username">Usuari:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Contrasenya:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Inicia sessió</button>
        </form>
        <div class="register-section">
            <p>No tens un compte? <a href="/public/register.php">Crea un compte</a></p>
        </div>
        <div class="general-accounts">
            <h3>Comptes Generals de Prova:</h3>
            <p>Usuari: admin</p>
            <p>Contrasenya: 1234</p>
        </div>
    </div>
</body>
</html>