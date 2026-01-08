<?php
/**
 * FITXER: src/ingredients.php
 * DESCRIPCIÓ: Funcions per gestionar l'estoc d'ingredients i comprovar disponibilitat.
 */

/**
 * Comprova si hi ha prou stock per a un plat.
 * Retorna true si hi ha ingredients suficients, false si en falta algun.
 */
function checkStockPlat($db, $plat_id) {
    // Obtenim els ingredients necessaris per al plat
    $stmt = $db->prepare("SELECT ri.ingredient_id, ri.quantitat_necessaria, mp.quantitat_stock 
                          FROM recepta_ingredients ri 
                          JOIN materies_primeres mp ON ri.ingredient_id = mp.id 
                          WHERE ri.recepta_id = ?");
    if (!$stmt) return false; // Evitem crash si falla prepare
    
    $stmt->bind_param("i", $plat_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        // Si l'stock és menor que el necessari (o és 0), retornem false (falta ingredient)
        if ($row['quantitat_stock'] < $row['quantitat_necessaria']) {
            return false;
        }
    }
    return true;
}

/**
 * Obté un resum de tots els ingredients, el stock actual, i el necessari 
 * segons les comandes actives (pendent, en_preparacio).
 */
function getIngredientsResum($db) {
    // 1. Obtenim tots els ingredients
    $res = $db->query("SELECT * FROM materies_primeres ORDER BY nom ASC");
    if (!$res) return []; // Retorn buit si falla
    
    $ingredients = $res->fetch_all(MYSQLI_ASSOC);
    
    // 2. Calculem el necessari per les comandes actives
    // Busquem plats en comandes actives
    $sql = "SELECT dc.plat_id, dc.quantitat 
            FROM detalls_comanda dc
            JOIN comandes c ON dc.comanda_id = c.id
            WHERE c.estat IN ('pendent', 'en_preparacio')";
    $result = $db->query($sql);
    
    $necessaris = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $plat_id = $row['plat_id'];
            $qty_plat = $row['quantitat'];
            
            // Busquem ingredients per aquest plat
            $stmt = $db->prepare("SELECT ingredient_id, quantitat_necessaria FROM recepta_ingredients WHERE recepta_id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $plat_id);
                $stmt->execute();
                $ing_result = $stmt->get_result();
                
                while ($ing = $ing_result->fetch_assoc()) {
                    $ing_id = $ing['ingredient_id'];
                    $qty_needed = $ing['quantitat_necessaria'] * $qty_plat;
                    
                    if (!isset($necessaris[$ing_id])) {
                        $necessaris[$ing_id] = 0;
                    }
                    $necessaris[$ing_id] += $qty_needed;
                }
            }
        }
    }
    
    // 3. Fusionem dades
    $final_data = [];
    foreach ($ingredients as $ing) {
        $id = $ing['id'];
        $needed = $necessaris[$id] ?? 0;
        $stock = $ing['quantitat_stock'];
        $diff = $stock - $needed;
        
        $final_data[] = [
            'id' => $ing['id'], // Added ID
            'nom' => $ing['nom'],
            'unitat' => $ing['unitat'],
            'stock' => $stock,
            'necessari' => $needed,
            'diferencia' => $diff,
            'falta' => ($diff < 0) // Bool per saber si falta
        ];
    }
    
    return $final_data;
}
