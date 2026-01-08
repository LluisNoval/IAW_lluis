<?php
/**
 * FITXER: public/error.php
 * DESCRIPCIÓ: Pàgina genèrica de gestió d'errors.
 * Rep un codi d'error o missatge per GET i el mostra.
 */
$error_code = $_GET['code'] ?? 500;
$error_message = $_GET['msg'] ?? 'Ha ocorregut un error inesperat.';

// Mapeig de codis HTTP comuns
$codes = [
    403 => 'Accés Denegat',
    404 => 'Pàgina No Trobada',
    500 => 'Error Intern del Servidor'
];

$title = $codes[$error_code] ?? 'Error';
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f8; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; color: #333; }
        .error-card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; max-width: 500px; width: 90%; }
        h1 { color: #e74c3c; margin: 0 0 20px; font-size: 4rem; }
        h2 { margin: 10px 0; }
        p { color: #666; margin-bottom: 30px; }
        .btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 6px; transition: background 0.3s; }
        .btn:hover { background: #2980b9; }
    </style>
</head>
<body>
    <div class="error-card">
        <h1>:(</h1>
        <h2><?= htmlspecialchars($title) ?></h2>
        <p><?= htmlspecialchars($error_message) ?></p>
        <a href="index.php" class="btn">Tornar a l'Inici</a>
    </div>
</body>
</html>
