<?php
session_start();
require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/flash_messages.php';

// Validar que l\'usuari hagi iniciat sessió
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Obtenir categories per al desplegable
$categories_result = $dbConnection->query("SELECT id, name FROM categories ORDER BY name");
$categories = $categories_result->fetch_all(MYSQLI_ASSOC);

// Processament del formulari
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recollir dades del formulari
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? null);
    $image_path = trim($_POST['image_path'] ?? null);
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    
    // Recollir dades extra (convertir a null si estan buides)
    $calories = !empty($_POST['calories']) ? filter_input(INPUT_POST, 'calories', FILTER_VALIDATE_FLOAT) : null;
    $spoil_time = !empty($_POST['spoil_time']) ? filter_input(INPUT_POST, 'spoil_time', FILTER_VALIDATE_FLOAT) : null;
    $hardness = !empty($_POST['hardness']) ? filter_input(INPUT_POST, 'hardness', FILTER_VALIDATE_FLOAT) : null;
    $thermal_conductivity = !empty($_POST['thermal_conductivity']) ? filter_input(INPUT_POST, 'thermal_conductivity', FILTER_VALIDATE_FLOAT) : null;

    // Validació bàsica
    if (empty($name)) {
        set_flash_message('error', 'El nom de l\'ítem és obligatori.');
    } else {
        $sql = "INSERT INTO items (name, description, image_path, category_id, calories, spoil_time, hardness, thermal_conductivity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $dbConnection->prepare($sql);
        // El tipus de bind_param és 'sssidddd' (string, string, string, integer, decimal, decimal, decimal, decimal)
        $stmt->bind_param('sssidddd', $name, $description, $image_path, $category_id, $calories, $spoil_time, $hardness, $thermal_conductivity);

        if ($stmt->execute()) {
            set_flash_message('success', 'Ítem "' . htmlspecialchars($name) . '" creat correctament.');
            header("Location: items.php");
            exit();
        } else {
            set_flash_message('error', 'Error en crear l\'ítem: ' . $dbConnection->error);
        }
        $stmt->close();
    }
}
$dbConnection->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afegir Nou Ítem</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .form-container { max-width: 600px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box; /* Assegura que el padding no afecti l'amplada total */
        }
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../views/partials/header.php'; ?>

    <div class="form-container">
        <h1>Afegir Nou Ítem</h1>

        <?php require_once __DIR__ . '/../views/partials/show_messages.php'; ?>
        
        <form action="create_item.php" method="post">
            <div class="form-group">
                <label for="name">Nom*</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Descripció</label>
                <textarea id="description" name="description"></textarea>
            </div>
            <div class="form-group">
                <label for="image_path">Ruta de la Imatge</label>
                <input type="text" id="image_path" name="image_path" placeholder="ex: export/ui_image/NomItem.png">
            </div>
            <div class="form-group">
                <label for="category_id">Categoria</label>
                <select id="category_id" name="category_id">
                    <option value="">-- Selecciona una categoria --</option>
                    <?php foreach ($categories as $category):
                        echo '<option value="' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</option>';
                    endforeach; ?>
                </select>
            </div>
            <hr>
            <p><strong>Camps Específics (opcional)</strong></p>
            <div class="form-group">
                <label for="calories">Calories</label>
                <input type="number" step="0.01" id="calories" name="calories">
            </div>
            <div class="form-group">
                <label for="spoil_time">Temps de Descomposició (segons)</label>
                <input type="number" step="0.01" id="spoil_time" name="spoil_time">
            </div>
            <div class="form-group">
                <label for="hardness">Duresa</label>
                <input type="number" step="0.01" id="hardness" name="hardness">
            </div>
            <div class="form-group">
                <label for="thermal_conductivity">Conductivitat Tèrmica</label>
                <input type="number" step="0.0001" id="thermal_conductivity" name="thermal_conductivity">
            </div>

            <button type="submit">Crear Ítem</button>
        </form>
    </div>
</body>
</html>
