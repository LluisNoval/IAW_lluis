<?php
/**
 * FITXER: public/admin_plats.php
 * DESCRIPCIÓ: Gestió completa (CRUD) dels plats del menú.
 */
session_start();
require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/flash_messages.php';

// Només Admin pot gestionar plats
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: dashboard.php"); exit;
}

// Obtenim categories per al select
$categories = $dbConnection->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC);

// --- ACCIONS CRUD ---

// 1. CREAR PLAT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_plat'])) {
    $nom = trim($_POST['nom']);
    $desc = trim($_POST['descripcio']);
    $preu = floatval($_POST['preu']);
    $cat_id = intval($_POST['categoria_id']);
    
    if (empty($nom) || $preu <= 0) {
        set_flash_message('error', 'El nom i un preu vàlid són obligatoris.');
    } else {
        $stmt = $dbConnection->prepare("INSERT INTO plats (nom, descripcio, preu, categoria_id, disponible) VALUES (?, ?, ?, ?, 1)");
        $stmt->bind_param("ssdi", $nom, $desc, $preu, $cat_id);
        if ($stmt->execute()) {
            set_flash_message('success', 'Plat creat correctament.');
        } else {
            set_flash_message('error', 'Error al crear el plat.');
        }
    }
    header("Location: admin_plats.php"); exit;
}

// 2. ELIMINAR PLAT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_plat'])) {
    $id = intval($_POST['plat_id']);
    // Fem servir DELETE CASCADE si està configurat a la BD, sino manualment.
    // Assumim que la BD té ON DELETE RESTRICT o CASCADE. 
    // Si hi ha comandes, potser millor marcar com "no disponible" en lloc d'esborrar.
    // Però el requisit demana DELETE. Intentem esborrar.
    
    $stmt = $dbConnection->prepare("DELETE FROM plats WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    try {
        if ($stmt->execute()) {
            set_flash_message('success', 'Plat eliminat.');
        } else {
            throw new Exception($dbConnection->error);
        }
    } catch (Exception $e) {
        set_flash_message('error', 'No es pot eliminar aquest plat perquè té comandes associades. Marca\'l com a no disponible.');
    }
    header("Location: admin_plats.php"); exit;
}

// 3. ACTUALITZAR PLAT (Disponibilitat i Preu ràpid)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_plat'])) {
    $id = intval($_POST['plat_id']);
    $preu = floatval($_POST['preu']);
    $disponible = isset($_POST['disponible']) ? 1 : 0;
    
    $stmt = $dbConnection->prepare("UPDATE plats SET preu = ?, disponible = ? WHERE id = ?");
    $stmt->bind_param("dii", $preu, $disponible, $id);
    $stmt->execute();
    set_flash_message('success', 'Plat actualitzat.');
    header("Location: admin_plats.php"); exit;
}

// --- LLISTAT ---
$plats = $dbConnection->query("SELECT p.*, c.nom as cat_nom FROM plats p JOIN categories c ON p.categoria_id = c.id ORDER BY p.categoria_id, p.nom")->fetch_all(MYSQLI_ASSOC);

require_once __DIR__ . '/../views/admin_plats.view.php';
?>
