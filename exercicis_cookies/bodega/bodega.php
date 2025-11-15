<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Guardem les cookies durant 1 any
    $expiracio = time() + 365*24*60*60;
    setcookie("majoredat", $_POST["majoredat"], $expiracio, "/");
    setcookie("idioma", $_POST["idioma"], $expiracio, "/");
    setcookie("moneda", $_POST["moneda"], $expiracio, "/");
    
    header("Location: info.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Bodega - Selecció</title>
</head>
<body>
    <h1>Bodega</h1>
    <h3>Configura les teves preferències</h3>

    <form method="POST">
        <label>Ets major d'edat?</label><br>
        <select name="majoredat" required>
            <option value="si">Sí</option>
            <option value="no">No</option>
        </select>
        <br><br>

        <label>Selecciona idioma:</label><br>
        <select name="idioma" required>
            <option value="ca">Català</option>
            <option value="es">Español</option>
            <option value="en">English</option>
        </select>
        <br><br>

        <label>Selecciona moneda:</label><br>
        <select name="moneda" required>
            <option value="eur">Euro (€)</option>
            <option value="gbp">Lliura (£)</option>
            <option value="usd">Dòlar ($)</option>
        </select>
        <br><br>

        <button type="submit">Guardar i continuar</button>
    </form>
</body>
</html>