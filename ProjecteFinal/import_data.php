<?php
// import_data.php
// Aquest script és per a un sol ús. Llegeix les dades dels fitxers JSON
// i les importa a la base de dades.

set_time_limit(300); // Augmenta el límit de temps d'execució a 5 minuts.
require_once __DIR__ . '/src/database.php';

echo "<h1>Iniciant procés d'importació...</h1>";
echo "<p>Això pot trigar uns segons. Si us plau, espera.</p>";
ob_flush();
flush();

try {
    // 1. Desactivar claus foranes i iniciar transacció per a més rendiment.
    $dbConnection->query("SET FOREIGN_KEY_CHECKS=0;");
    $dbConnection->begin_transaction();

    // 2. Buidar les taules per evitar duplicats si es torna a executar.
    echo "<p>Buidant taules existents...</p>";
    $dbConnection->query("TRUNCATE TABLE attributes;");
    $dbConnection->query("TRUNCATE TABLE items;");
    $dbConnection->query("TRUNCATE TABLE categories;");
    ob_flush();
    flush();

    // 3. Definir els fitxers a importar i les seves categories corresponents.
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
        $categoryId = $dbConnection->insert_id;
        $categoryIds[$fileInfo['name']] = $categoryId;
    }
    $stmtCat->close();
    echo "<p>Categories creades amb èxit.</p>";
    ob_flush();
    flush();

    // 4. Preparar la sentència per inserir items i atributs.
    $stmtItem = $dbConnection->prepare("INSERT INTO items (name, description, image_path, category_id) VALUES (?, ?, ?, ?)");
    $stmtAttr = $dbConnection->prepare("INSERT INTO attributes (item_id, attribute_name, value) VALUES (?, ?, ?)");

    // 5. Recórrer cada fitxer i importar les dades.
    foreach ($filesToImport as $fileName => $fileInfo) {
        $filePath = __DIR__ . '/export/database_base/' . $fileName;
        echo "<p><strong>Processant fitxer: $fileName...</strong></p>";

        if (!file_exists($filePath)) {
            echo "<p style='color:orange;'>Avís: El fitxer $fileName no s'ha trobat.</p>";
            continue;
        }

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        $tableKey = key($data);
        if (!isset($data[$tableKey]) || !is_array($data[$tableKey])) {
             echo "<p style='color:red;'>Error: L'estructura del JSON $fileName no és la esperada.</p>";
             continue;
        }
        
        $itemsToImport = $data[$tableKey];
        $categoryId = $categoryIds[$fileInfo['name']];
        $count = 0;

        foreach ($itemsToImport as $itemData) {
            $name = $itemData['tag']['Name'] ?? $itemData['Id'] ?? null;
            $description = null;
            if (isset($itemData['substance']['description'])) {
                $description = strip_tags($itemData['substance']['description']);
            } elseif (isset($itemData['desc'])) {
                $description = strip_tags($itemData['desc']);
            } elseif (isset($itemData['description'])) {
                $description = strip_tags($itemData['description']);
            }

            if ($name === null) continue;

            $image_path = 'export/ui_image/' . $name . '.png';

            $stmtItem->bind_param('sssi', $name, $description, $image_path, $categoryId);
            if ($stmtItem->execute()) {
                $count++;
                $itemId = $dbConnection->insert_id;

                // Importar atributs comuns
                $common_attrs = ['hardness', 'strength', 'thermalConductivity', 'specificHeatCapacity'];
                foreach ($common_attrs as $attr_name) {
                    if (isset($itemData[$attr_name])) {
                        $stmtAttr->bind_param('iss', $itemId, $attr_name, $itemData[$attr_name]);
                        $stmtAttr->execute();
                    }
                }

                // Importar atributs de 'attributeModifiers'
                if (isset($itemData['attributeModifiers']) && is_array($itemData['attributeModifiers'])) {
                    foreach ($itemData['attributeModifiers'] as $modifier) {
                        if (isset($modifier['AttributeId']) && isset($modifier['Value'])) {
                            $stmtAttr->bind_param('iss', $itemId, $modifier['AttributeId'], $modifier['Value']);
                            $stmtAttr->execute();
                        }
                    }
                }
            }
        }
        echo "<p style='color:green;'>&nbsp;&nbsp;&nbsp;-> S'han importat $count items de '$fileName'.</p>";
        ob_flush();
        flush();
    }
    $stmtItem->close();
    $stmtAttr->close();

    // 6. Reactivar claus foranes i confirmar transacció.
    $dbConnection->commit();
    $dbConnection->query("SET FOREIGN_KEY_CHECKS=1;");

    echo "<h2>Procés d'importació finalitzat amb èxit!</h2>";
    echo "<p>La base de dades ara conté molta més informació.</p>";
    echo "<p><a href='public/items.php'>Fes clic aquí per anar a la pàgina d'ítems.</a></p>";

} catch (Exception $e) {
    // Si hi ha algun error, desfer la transacció.
    $dbConnection->rollback();
    echo "<h2>Ha ocorregut un error durant la importació:</h2>";
    echo "<p style='color:red;'>" . $e->getMessage() . "</p>";
    $dbConnection->query("SET FOREIGN_KEY_CHECKS=1;");
}

$dbConnection->close();

?>

