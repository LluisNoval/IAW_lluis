<?php
// public/items.php

// Control de sessió i accés.
session_start();
require_once __DIR__ . '/../src/database.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Obtenir tots els items de la base de dades, ordenats per categoria i nom.
// Fem un JOIN amb la taula de categories per obtenir el nom de la categoria.
$sql = "SELECT i.id, i.name, i.description, i.image_path, c.name AS category_name 
        FROM items i 
        LEFT JOIN categories c ON i.category_id = c.id
        ORDER BY c.name ASC, i.name ASC";

$result = $dbConnection->query($sql);

?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ítems del Joc - Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php require_once __DIR__ . '/../views/partials/header.php'; ?>

    <main class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
        <h1>Ítems del Joc</h1>
        <p>Aquí pots veure tots els ítems disponibles al joc, agrupats per categoria.</p>

        <!-- Botó per afegir nous ítems (funcionalitat futura) -->
        <a href="#" class="button" style="display: inline-block; margin-bottom: 20px;">Afegir Nou Ítem</a>

        <?php 
        if ($result && $result->num_rows > 0) {
            $currentCategory = null;
            while($row = $result->fetch_assoc()) {
                // Si la categoria canvia (o és la primera), obrim una nova secció
                if ($row['category_name'] !== $currentCategory) {
                    // Si ja hi havia una secció oberta, la tanquem
                    if ($currentCategory !== null) {
                        echo '</div></details>';
                    }
                    $currentCategory = $row['category_name'];
                    echo '<details open>';
                    echo '<summary>' . htmlspecialchars($currentCategory ?? 'Sense Categoria') . '</summary>';
                    echo '<div class="item-grid">';
                }

                // Mostrem la targeta de l'ítem actual
                echo '<a href="item_detail.php?id=' . $row['id'] . '" class="item-card">';
                if (!empty($row['image_path']) && file_exists(__DIR__ . '/../' . $row['image_path'])) {
                    echo '<img src="../' . htmlspecialchars($row['image_path']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                } else {
                    echo '<img src="https://via.placeholder.com/80" alt="Imatge no disponible">';
                }
                echo '<span>' . htmlspecialchars($row['name']) . '</span>';
                echo '</a>';
            }
            // Tanquem l'última secció que va quedar oberta
            if ($currentCategory !== null) {
                echo '</div></details>';
            }
        } else {
            echo "<p>No s'han trobat ítems a la base de dades.</p>";
        }
        ?>
    </main>
</body>
</html>
<?php
// Tanquem la connexió
$dbConnection->close();
?>
