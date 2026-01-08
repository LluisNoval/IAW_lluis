<?php
/**
 * FITXER: public/ingredients_resum.php
 * DESCRIPCIÓ: Controlador per veure el resum d'estoc i necessitats.
 */
session_start();
require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/ingredients.php';

// Verifiquem permisos
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'cuiner')) {
    header("Location: dashboard.php"); exit;
}

// ACTUALITZAR STOCK (Només Admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock']) && $_SESSION['rol'] === 'admin') {
    $id = intval($_POST['ingredient_id']);
    $new_stock = floatval($_POST['new_stock']);
    
    $stmt = $dbConnection->prepare("UPDATE materies_primeres SET quantitat_stock = ? WHERE id = ?");
    $stmt->bind_param("di", $new_stock, $id);
    
    if ($stmt->execute()) {
        set_flash_message('success', 'Stock actualitzat correctament.');
    } else {
        set_flash_message('error', 'Error al actualitzar l\'stock.');
    }
    header("Location: ingredients_resum.php"); exit;
}

// Obtenim les dades
$dades_ingredients = getIngredientsResum($dbConnection);

require_once __DIR__ . '/../views/ingredients_resum.view.php';
