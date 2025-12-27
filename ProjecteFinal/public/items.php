<?php
// public/items.php

session_start();
require_once __DIR__ . '/../src/database.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Lògica del cercador
$search_term = trim($_GET['search'] ?? '');
$sql = "SELECT i.id, i.name, i.display_name, i.description, i.image_path, c.name AS category_name 
        FROM items i 
        LEFT JOIN categories c ON i.category_id = c.id";

if (!empty($search_term)) {
    $sql .= " WHERE i.display_name LIKE ?";
}

$sql .= " ORDER BY c.name ASC, i.name ASC";

$stmt = $dbConnection->prepare($sql);

if (!empty($search_term)) {
    $like_term = "%" . $search_term . "%";
    $stmt->bind_param('s', $like_term);
}

$stmt->execute();
$result = $stmt->get_result();

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
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <h1>Ítems del Joc</h1>
            
            <!-- Formulari de Cerca -->
            <form action="items.php" method="get" style="display: flex; gap: 10px;">
                <input type="text" name="search" placeholder="Busca un ítem..." value="<?php echo htmlspecialchars($search_term); ?>" style="padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                <button type="submit" class="button">Cercar</button>
            </form>
        </div>

        <p>Aquí pots veure tots els ítems disponibles al joc, agrupats per categoria.</p>

        <!-- Botó per afegir nous ítems (funcionalitat futura) -->
        <a href="create_item.php" class="button" style="display: inline-block; margin-bottom: 20px;">Afegir Nou Ítem</a>

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
                    echo '<img src="../' . htmlspecialchars($row['image_path']) . '" alt="' . htmlspecialchars($row['display_name']) . '">';
                } else {
                    echo '<img src="https://via.placeholder.com/80" alt="Imatge no disponible">';
                }
                echo '<span>' . htmlspecialchars($row['display_name']) . '</span>';
                echo '</a>';
            }
            // Tanquem l'última secció que va quedar oberta
            if ($currentCategory !== null) {
                echo '</div></details>';
            }
        } else {
            echo '<div style="padding: 40px; text-align: center; background-color: #fff3cd; border: 1px solid #ffeeba; border-radius: 8px; margin-top: 20px;">';
            if (!empty($search_term)) {
                echo '<h2>No s\'han trobat resultats</h2>';
                echo '<p>No hi ha cap ítem que coincideixi amb la cerca "<strong>' . htmlspecialchars($search_term) . '</strong>".</p>';
                echo '<a href="items.php" class="button" style="margin-top: 15px; display: inline-block; text-decoration: none;">Netejar cerca</a>';
            } else {
                echo '<h2>La base de dades sembla buida!</h2>';
                echo '<p>L\'estructura de la base de dades està llesta, però no conté cap ítem. Per omplir-la amb totes les dades del joc, has d\'executar l\'script d\'importació.</p>';
                echo '<a href="../import_data.php" class="button" style="margin-top: 15px; display: inline-block; text-decoration: none;">Executar l\'importador ara</a>';
            }
            echo '</div>';
        }
        ?>
    </main>
</body>
</html>
<?php
// Tanquem la connexió
$stmt->close();
$dbConnection->close();
?>
