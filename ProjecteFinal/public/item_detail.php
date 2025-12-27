<?php
// public/item_detail.php

session_start();
require_once __DIR__ . '/../src/database.php';

// 1. Validar que l'usuari hagi iniciat sessió
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// 2. Obtenir i validar l'ID de l'ítem de l'URL
$item_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$item_id) {
    // Si no hi ha ID o no és un número, redirigir a la llista
    header("Location: items.php");
    exit();
}

// 3. Obtenir les dades principals de l'ítem
$stmt = $dbConnection->prepare(
    "SELECT i.id, i.name, i.description, i.image_path, c.name AS category_name 
     FROM items i 
     LEFT JOIN categories c ON i.category_id = c.id
     WHERE i.id = ?"
);
$stmt->bind_param('i', $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

// Si no es troba l'ítem, mostrar un error
if (!$item) {
    // Podríem crear una pàgina d'error 404, de moment redirigim
    header("Location: items.php");
    exit();
}

// 4. Obtenir els atributs de l'ítem
$attr_stmt = $dbConnection->prepare("SELECT attribute_name, value FROM attributes WHERE item_id = ?");
$attr_stmt->bind_param('i', $item_id);
$attr_stmt->execute();
$attributes_result = $attr_stmt->get_result();
$attributes = [];
while ($row = $attributes_result->fetch_assoc()) {
    $attributes[] = $row;
}
$attr_stmt->close();

$dbConnection->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detall de <?php echo htmlspecialchars($item['name']); ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .detail-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .detail-header {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .detail-header img {
            width: 150px;
            height: 150px;
            object-fit: contain;
            border: 1px solid #eee;
            border-radius: 5px;
        }
        .detail-header-info h1 {
            margin: 0;
            color: #333;
        }
        .detail-header-info .category {
            font-size: 1.1em;
            color: #666;
            background-color: #f2f2f2;
            padding: 5px 10px;
            border-radius: 15px;
            display: inline-block;
            margin-top: 10px;
        }
        .detail-content {
            padding-top: 20px;
        }
        .detail-content p {
            font-size: 1.1em;
            line-height: 1.6;
        }
        .attributes-list {
            list-style-type: none;
            padding: 0;
            margin-top: 20px;
        }
        .attributes-list li {
            background-color: #f9f9f9;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .attributes-list li strong {
            color: #0056b3;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            background-color: #6c757d;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
        }
        .back-link:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../views/partials/header.php'; ?>

    <main>
        <div class="detail-container">
            <div class="detail-header">
                <img src="../<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                <div class="detail-header-info">
                    <h1><?php echo htmlspecialchars($item['name']); ?></h1>
                    <span class="category"><?php echo htmlspecialchars($item['category_name']); ?></span>
                </div>
            </div>

            <div class="detail-content">
                <h3>Descripció</h3>
                <p><?php echo htmlspecialchars($item['description'] ?? 'No hi ha descripció disponible.'); ?></p>
                
                <?php if (!empty($attributes)): ?>
                    <h3>Atributs</h3>
                    <ul class="attributes-list">
                        <?php foreach ($attributes as $attr): ?>
                            <li><strong><?php echo htmlspecialchars($attr['attribute_name']); ?>:</strong> <?php echo htmlspecialchars($attr['value']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <a href="items.php" class="back-link">&laquo; Torna a la llista</a>
        </div>
    </main>
</body>
</html>
