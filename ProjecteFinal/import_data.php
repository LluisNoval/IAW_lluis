<?php
// import_data.php
set_time_limit(300);
require_once __DIR__ . '/src/database.php';

// --- Funcions auxiliars ---
function find_value(array $haystack, ...$needles) {
    foreach ($needles as $needle) {
        $keys = explode('.', $needle);
        $temp_haystack = $haystack;
        $found = true;
        foreach ($keys as $key) {
            $value = null;
            $found_key = false;
            if (!is_array($temp_haystack)) { $found = false; break; }
            foreach ($temp_haystack as $h_key => $h_value) {
                if (strcasecmp($h_key, $key) === 0) {
                    $value = $h_value;
                    $found_key = true;
                    break;
                }
            }
            if ($found_key) { $temp_haystack = $value; } else { $found = false; break; }
        }
        if ($found && $temp_haystack !== null) { return $temp_haystack; }
    }
    return null;
}

function formatName(string $name): string {
    // Reemplaça _ per espais (p. ex., Atmo_Suit -> Atmo Suit)
    $name_with_spaces = str_replace('_', ' ', $name);
    // Afegeix un espai abans de cada majúscula en paraules compostes (p. ex., BasicPlantFood -> Basic Plant Food)
    $formatted_name = preg_replace('/(?<=\\w)(?=[A-Z])/', " $1", $name_with_spaces);
    return trim($formatted_name);
}

// Mapa per traduir IDs de guèisers a noms correctes.
$geyserNameMap = [
    'GeyserGeneric_steam' => 'Steam Vent',
    'GeyserGeneric_hot_steam' => 'Hot Steam Vent',
    'GeyserGeneric_hot_water' => 'Hot Water Geyser',
    'GeyserGeneric_slush_water' => 'Cool Slush Geyser',
    'GeyserGeneric_filthy_water' => 'Polluted Water Vent',
    'GeyserGeneric_slush_salt_water' => 'Cool Salt Slush Geyser',
    'GeyserGeneric_salt_water' => 'Salt Water Geyser',
    'GeyserGeneric_small_volcano' => 'Minor Volcano',
    'GeyserGeneric_big_volcano' => 'Volcano',
    'GeyserGeneric_liquid_co2' => 'Carbon Dioxide Geyser',
    'GeyserGeneric_hot_co2' => 'Hot Carbon Dioxide Vent',
    'GeyserGeneric_hot_hydrogen' => 'Hot Hydrogen Vent',
    'GeyserGeneric_hot_po2' => 'Infectious Oxygen Vent',
    'GeyserGeneric_slimy_po2' => 'Slimy Polluted Oxygen Vent',
    'GeyserGeneric_chlorine_gas' => 'Chlorine Gas Vent',
    'GeyserGeneric_chlorine_gas_cool' => 'Cool Chlorine Vent',
    'GeyserGeneric_methane' => 'Natural Gas Geyser',
    'GeyserGeneric_molten_copper' => 'Copper Volcano',
    'GeyserGeneric_molten_iron' => 'Iron Volcano',
    'GeyserGeneric_molten_gold' => 'Gold Volcano',
    'GeyserGeneric_oil_drip' => 'Leaky Oil Fissure'
];

echo "<h1>Iniciant procés d'importació avançat...</h1>";
echo "<p>Això pot trigar uns segons. Si us plau, espera.</p>";
ob_flush();
flush();

try {
    $dbConnection->query("SET FOREIGN_KEY_CHECKS=0;");
    $dbConnection->begin_transaction();

    echo "<p>Buidant taules existents...</p>";
    $dbConnection->query("TRUNCATE TABLE attributes;");
    $dbConnection->query("TRUNCATE TABLE recipe_ingredients;");
    $dbConnection->query("TRUNCATE TABLE recipes;");
    $dbConnection->query("TRUNCATE TABLE items;");
    $dbConnection->query("TRUNCATE TABLE categories;");
    ob_flush();
    flush();

    $filesToImport = [
        'elements.json' => ['name' => 'Elements', 'description' => 'Substàncies bàsiques, gasos, líquids i sòlids.'],
        'building.json' => ['name' => 'Edificis', 'description' => 'Estructures construïbles pels duplicants.'],
        'food.json'     => ['name' => 'Aliments', 'description' => 'Recursos comestibles per a la supervivència.'],
        'items.json'    => ['name' => 'Objectes', 'description' => 'Objectes fabricables i equipables.'],
        'geyser.json'   => ['name' => 'Guèisers', 'description' => 'Fonts naturals de recursos periòdics.'],
    ];

    $categoryIds = [];
    $stmtCat = $dbConnection->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    echo "<p>Creant categories...</p>";
    foreach ($filesToImport as $fileInfo) {
        $stmtCat->bind_param('ss', $fileInfo['name'], $fileInfo['description']);
        $stmtCat->execute();
        $categoryIds[$fileInfo['name']] = $dbConnection->insert_id;
    }
    $stmtCat->close();
    echo "<p>Categories creades amb èxit.</p>";
    ob_flush();
    flush();

    $propsToStore = [
        'hardness' => 'Hardness', 'strength' => 'Strength', 'calories' => 'Calories',
        'spoilTime' => 'Spoil Time', 'highTemp' => 'Melting Point', 'lowTemp' => 'Freezing Point',
        'thermalConductivity' => 'Thermal Conductivity', 'specificHeatCapacity' => 'Specific Heat Capacity',
        'lightAbsorptionFactor' => 'Light Absorption', 'radiationAbsorptionFactor' => 'Radiation Absorption',
        'maxMass' => 'Mass per Tile', 'quality' => 'Quality'
    ];

    $stmtItem = $dbConnection->prepare("INSERT INTO items (name, display_name, description, image_path, category_id) VALUES (?, ?, ?, ?, ?)");
    
    $stmtAttr = $dbConnection->prepare("INSERT INTO attributes (item_id, attribute_name, value) VALUES (?, ?, ?)");
    $attrItemId = null;
    $attrName = null;
    $attrValue = null;
    $stmtAttr->bind_param('iss', $attrItemId, $attrName, $attrValue);

    foreach ($filesToImport as $fileName => $fileInfo) {
        $filePath = __DIR__ . '/export/database_base/' . $fileName;
        echo "<p><strong>Processant fitxer: $fileName...</strong></p>";
        if (!file_exists($filePath)) { continue; }

        $itemsToImport = current(json_decode(file_get_contents($filePath), true));
        $categoryId = $categoryIds[$fileInfo['name']];
        $count = 0;

        foreach ($itemsToImport as $itemData) {
            $name = find_value($itemData, 'tag.Name', 'Id');
            if ($name === null) continue;

            // Lògica de format de nom
            if ($fileName === 'geyser.json') {
                $displayName = $geyserNameMap[$name] ?? formatName($name);
            } else {
                $displayName = formatName($name);
            }

            $description = strip_tags(find_value($itemData, 'substance.description', 'Description', 'desc') ?? '');
            $image_path = 'export/ui_image/' . $name . '.png';

            if ($stmtItem->bind_param('ssssi', $name, $displayName, $description, $image_path, $categoryId) && $stmtItem->execute()) {
                $count++;
                $attrItemId = $dbConnection->insert_id;

                foreach ($propsToStore as $jsonKey => $displayNameAttr) {
                    $value = find_value($itemData, $jsonKey);
                    if ($value !== null) {
                        $attrName = $displayNameAttr;
                        $attrValue = $value;
                        $stmtAttr->execute();
                    }
                }
            }
        }
        echo "<p style='color:green;'>&nbsp;&nbsp;&nbsp;-> S'han importat $count items.</p>";
        ob_flush();
        flush();
    }
    $stmtItem->close();
    $stmtAttr->close();

    echo "<p><strong>Processant receptes...</strong></p>";
    $itemMapResult = $dbConnection->query("SELECT id, name FROM items");
    $itemMap = [];
    while ($row = $itemMapResult->fetch_assoc()) {
        $itemMap[$row['name']] = $row['id'];
    }
    
    $recipeFilePath = __DIR__ . '/export/database_base/recipe.json';
    if (file_exists($recipeFilePath)) {
        $recipeData = json_decode(file_get_contents($recipeFilePath), true)['preProcessRecipes'];
        $recipeCount = 0;

        $stmtRecipe = $dbConnection->prepare("INSERT INTO recipes (recipe_json_id, fabricator_item_id, output_item_id, output_item_quantity, crafting_time) VALUES (?, ?, ?, ?, ?)");
        $stmtIngredient = $dbConnection->prepare("INSERT INTO recipe_ingredients (recipe_id, ingredient_item_id, ingredient_quantity) VALUES (?, ?, ?)");

        foreach ($recipeData as $recipe) {
            $fabricatorName = $recipe['fabricators'][0]['Name'] ?? null;
            $outputName = $recipe['results'][0]['material']['Name'] ?? null;
            
            if ($fabricatorName && $outputName && isset($itemMap[$fabricatorName]) && isset($itemMap[$outputName])) {
                $fabricatorId = $itemMap[$fabricatorName];
                $outputId = $itemMap[$outputName];
                $outputQty = $recipe['results'][0]['amount'];
                $time = $recipe['time'];
                $recipeJsonId = $recipe['id'];

                if ($stmtRecipe->bind_param('siidd', $recipeJsonId, $fabricatorId, $outputId, $outputQty, $time) && $stmtRecipe->execute()) {
                    $recipeId = $dbConnection->insert_id;
                    $recipeCount++;

                    foreach ($recipe['ingredients'] as $ingredient) {
                        $ingredientName = $ingredient['material']['Name'] ?? null;
                        if ($ingredientName && isset($itemMap[$ingredientName])) {
                            $ingredientId = $itemMap[$ingredientName];
                            $ingredientQty = $ingredient['amount'];
                            $stmtIngredient->bind_param('iid', $recipeId, $ingredientId, $ingredientQty);
                            $stmtIngredient->execute();
                        }
                    }
                }
            }
        }
        $stmtRecipe->close();
        $stmtIngredient->close();
        echo "<p style='color:green;'>&nbsp;&nbsp;&nbsp;-> S'han importat $recipeCount receptes.</p>";
    }
    
    $dbConnection->commit();
    $dbConnection->query("SET FOREIGN_KEY_CHECKS=1;");

    echo "<h2>Procés d'importació finalitzat amb èxit!</h2>";
    echo "<p>La base de dades ara conté tota la informació detallada dels ítems.</p>";
    echo "<p><a href='public/items.php'>Fes clic aquí per anar a la pàgina d'ítems.</a></p>";

} catch (Exception $e) {
    $dbConnection->rollback();
    echo "<h2>Ha ocorregut un error durant la importació:</h2>";
    echo "<p style='color:red;'>" . $e->getMessage() . "</p>";
    $dbConnection->query("SET FOREIGN_KEY_CHECKS=1;");
}

$dbConnection->close();
?>

