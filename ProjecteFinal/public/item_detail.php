<?php
// public/item_detail.php

session_start();
require_once __DIR__ . '/../src/database.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

$item_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$item_id) {
    header("Location: items.php");
    exit();
}

// 1. Dades principals de l'ítem
$stmt = $dbConnection->prepare("SELECT i.id, i.name, i.display_name, i.description, i.image_path, c.name AS category_name FROM items i LEFT JOIN categories c ON i.category_id = c.id WHERE i.id = ?");
$stmt->bind_param('i', $item_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$item) {
    header("Location: items.php");
    exit();
}

// 2. Atributs de l'ítem
$attr_stmt = $dbConnection->prepare("SELECT attribute_name, value FROM attributes WHERE item_id = ? ORDER BY attribute_name ASC");
$attr_stmt->bind_param('i', $item_id);
$attr_stmt->execute();
$attributes = $attr_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$attr_stmt->close();

// 3. Recepta QUE PRODUEIX aquest ítem
// ... (la lògica de receptes es manté igual) ...
$recipe_produces_this = null;
$recipe_ingredients = [];
$prod_stmt = $dbConnection->prepare(
    "SELECT r.id, r.crafting_time, fab.id as fabricator_id, fab.display_name as fabricator_name 
     FROM recipes r
     JOIN items fab ON r.fabricator_item_id = fab.id
     WHERE r.output_item_id = ?"
);
$prod_stmt->bind_param('i', $item_id);
$prod_stmt->execute();
$recipe_produces_this = $prod_stmt->get_result()->fetch_assoc();
$prod_stmt->close();

if ($recipe_produces_this) {
    $ing_stmt = $dbConnection->prepare(
        "SELECT ri.ingredient_quantity, i.display_name as ingredient_display_name, i.id as ingredient_id
         FROM recipe_ingredients ri
         JOIN items i ON ri.ingredient_item_id = i.id
         WHERE ri.recipe_id = ?"
    );
    $ing_stmt->bind_param('i', $recipe_produces_this['id']);
    $ing_stmt->execute();
    $recipe_ingredients = $ing_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $ing_stmt->close();
}

// 4. Receptes ON S'UTILITZA aquest ítem com a ingredient
// ... (la lògica de receptes es manté igual) ...
$uses_stmt = $dbConnection->prepare(
    "SELECT r.output_item_id, i.display_name as output_display_name 
     FROM recipe_ingredients ri
     JOIN recipes r ON ri.recipe_id = r.id
     JOIN items i ON r.output_item_id = i.id
     WHERE ri.ingredient_item_id = ?
     GROUP BY r.output_item_id, i.display_name"
);
$uses_stmt->bind_param('i', $item_id);
$uses_stmt->execute();
$recipes_uses_this = $uses_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$uses_stmt->close();


// 5. NOU: Lògica per a navegació Anterior/Següent
$current_category_name = $item['category_name'];
$current_item_name = $item['name'];

// Trobar ítem ANTERIOR
$prev_stmt = $dbConnection->prepare(
    "SELECT i.id FROM items i JOIN categories c ON i.category_id = c.id 
     WHERE (c.name < ? OR (c.name = ? AND i.name < ?)) 
     ORDER BY c.name DESC, i.name DESC LIMIT 1"
);
$prev_stmt->bind_param('sss', $current_category_name, $current_category_name, $current_item_name);
$prev_stmt->execute();
$prev_item = $prev_stmt->get_result()->fetch_assoc();
$prev_stmt->close();
$prev_id = $prev_item['id'] ?? null;

// Trobar ítem SEGÜENT
$next_stmt = $dbConnection->prepare(
    "SELECT i.id FROM items i JOIN categories c ON i.category_id = c.id 
     WHERE (c.name > ? OR (c.name = ? AND i.name > ?)) 
     ORDER BY c.name ASC, i.name ASC LIMIT 1"
);
$next_stmt->bind_param('sss', $current_category_name, $current_category_name, $current_item_name);
$next_stmt->execute();
$next_item = $next_stmt->get_result()->fetch_assoc();
$next_stmt->close();
$next_id = $next_item['id'] ?? null;


$dbConnection->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detall de <?php echo htmlspecialchars($item['display_name']); ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .detail-container { max-width: 800px; margin: 20px auto; padding: 20px; background-color: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .detail-header { display: flex; align-items: center; gap: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .detail-header img { width: 150px; height: 150px; object-fit: contain; border: 1px solid #eee; border-radius: 5px; }
        .detail-header-info h1 { margin: 0; color: #333; }
        .detail-header-info .category { font-size: 1.1em; color: #666; background-color: #f2f2f2; padding: 5px 10px; border-radius: 15px; display: inline-block; margin-top: 10px; }
        .detail-content { padding-top: 20px; }
        .detail-content h3 { border-bottom: 2px solid #007bff; padding-bottom: 5px; margin-top: 30px;}
        .detail-content p { font-size: 1.1em; line-height: 1.6; }
        .attributes-list, .recipe-list { list-style-type: none; padding: 0; margin-top: 10px; }
        .attributes-list li, .recipe-list li { background-color: #f9f9f9; padding: 10px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        .attributes-list li strong, .recipe-list li strong { color: #0056b3; }
        .recipe-list a { text-decoration: none; color: #0056b3; font-weight: bold; }
        .recipe-list a:hover { text-decoration: underline; }
        .recipe-list .fabricator { font-style: italic; }
        .back-link { display: inline-block; margin-top: 30px; text-decoration: none; background-color: #6c757d; color: white; padding: 10px 15px; border-radius: 5px; }
        .back-link:hover { background-color: #5a6268; }
        /* Nous estils per a la navegació */
        .item-navigation { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .nav-button { padding: 10px 20px; text-decoration: none; background-color: #007bff; color: white; border-radius: 5px; }
        .nav-button:hover { background-color: #0056b3; }
        .nav-button.disabled { background-color: #ccc; cursor: not-allowed; pointer-events: none; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../views/partials/header.php'; ?>
    <main>
        <div class="detail-container">
            <div class="item-navigation">
                <?php if ($prev_id): ?>
                    <a href="item_detail.php?id=<?php echo $prev_id; ?>" class="nav-button">&laquo; Anterior</a>
                <?php else: ?>
                    <span class="nav-button disabled">&laquo; Anterior</span>
                <?php endif; ?>
                
                <?php if ($next_id): ?>
                    <a href="item_detail.php?id=<?php echo $next_id; ?>" class="nav-button">Següent &raquo;</a>
                <?php else: ?>
                    <span class="nav-button disabled">Següent &raquo;</span>
                <?php endif; ?>
            </div>

            <div class="detail-header">
                <img src="../<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['display_name']); ?>">
                <div class="detail-header-info">
                    <h1><?php echo htmlspecialchars($item['display_name']); ?></h1>
                    <span class="category"><?php echo htmlspecialchars($item['category_name']); ?></span>
                </div>
            </div>

            <div class="detail-content">
                <?php if(!empty($item['description'])): ?>
                    <h3>Descripció</h3>
                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($attributes)): ?>
                    <h3>Atributs</h3>
                    <ul class="attributes-list">
                        <?php foreach ($attributes as $attr): ?>
                            <li><strong><?php echo htmlspecialchars($attr['attribute_name']); ?>:</strong> <span><?php echo htmlspecialchars($attr['value']); ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if ($recipe_produces_this): ?>
                    <h3>Recepta de Fabricació</h3>
                    <ul class="recipe-list">
                        <li>
                            <strong>Es fabrica a:</strong> 
                            <a href="item_detail.php?id=<?php echo $recipe_produces_this['fabricator_id']; ?>" class="fabricator">
                                <?php echo htmlspecialchars($recipe_produces_this['fabricator_name']); ?>
                            </a>
                        </li>
                        <li><strong>Temps de fabricació:</strong> <span><?php echo htmlspecialchars($recipe_produces_this['crafting_time']); ?>s</span></li>
                        <?php foreach($recipe_ingredients as $ing): ?>
                            <li>
                                <a href="item_detail.php?id=<?php echo $ing['ingredient_id']; ?>">
                                    <strong>Ingredient:</strong> <?php echo htmlspecialchars($ing['ingredient_display_name']); ?>
                                </a>
                                <span>Quantitat: <?php echo htmlspecialchars($ing['ingredient_quantity']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if (!empty($recipes_uses_this)): ?>
                    <h3>S'utilitza per Fabricar</h3>
                    <ul class="recipe-list">
                        <?php foreach ($recipes_uses_this as $use): ?>
                            <li><a href="item_detail.php?id=<?php echo $use['output_item_id']; ?>"><?php echo htmlspecialchars($use['output_display_name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <a href="items.php" class="back-link">&laquo; Torna a la llista</a>
        </div>
    </main>
</body>
</html>
