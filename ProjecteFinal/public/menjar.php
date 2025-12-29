<?php
/**
 * FITXER: public/menjar.php
 * DESCRIPCIÓ: Mostra la carta del restaurant i processa la creació de noves comandes.
 */
session_start();
require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/flash_messages.php';

// Verificació d'accés
if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit; }

/**
 * PROCESSAMENT DE LA COMANDA
 * Quan l'usuari clica "Enviar a Cuina"
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['realitzar_comanda'])) {
    // Filtrem la llista per quedar-nos només amb els plats que tenen quantitat > 0
    $items = array_filter($_POST['items'], fn($i) => $i['quantitat'] > 0);

    if (empty($items)) {
        set_flash_message('error', 'Selecciona almenys un plat per fer la comanda.');
    } else {
        // Iniciem transacció per assegurar que o es guarda tot o no es guarda res
        $dbConnection->begin_transaction();
        
        // 1. Inserim la comanda principal
        $stmt = $dbConnection->prepare("INSERT INTO comandes (usuari_id, notes) VALUES (?, ?)");
        $stmt->bind_param('is', $_SESSION['user_id'], $_POST['notes_generals']);
        $stmt->execute();
        $id_comanda = $dbConnection->insert_id;

        $total = 0;
        // 2. Inserim cada plat als detalls de la comanda
        foreach ($items as $id_plat => $d) {
            // Busquem el preu actual del plat a la base de dades
            $p = $dbConnection->query("SELECT preu FROM plats WHERE id = ".intval($id_plat))->fetch_assoc();
            $preu = $p['preu'];
            $total += $preu * $d['quantitat'];

            // Preferències dietètiques (checkboxes)
            $vega = isset($d['vega']) ? 1 : 0;
            $gluten = isset($d['sense_gluten']) ? 1 : 0;
            
            $stmt = $dbConnection->prepare("INSERT INTO detalls_comanda (comanda_id, plat_id, quantitat, preu_unitari, es_vega, sense_gluten, comentaris) VALUES (?,?,?,?,?,?,?)");
            $stmt->bind_param('iiiddis', $id_comanda, $id_plat, $d['quantitat'], $preu, $vega, $gluten, $d['comentaris']);
            $stmt->execute();
        }

        // 3. Guardem el total calculat final
        $dbConnection->query("UPDATE comandes SET total = $total WHERE id = $id_comanda");
        $dbConnection->commit();

        set_flash_message('success', 'La teva comanda ha estat enviada correctament!');
        header("Location: dashboard.php"); exit;
    }
}

/**
 * LLISTAT DE PLATS
 * Obtenim tots els plats que estiguin marcats com a disponibles
 */
$plats = $dbConnection->query("SELECT * FROM plats WHERE disponible = 1")->fetch_all(MYSQLI_ASSOC);

require_once __DIR__ . '/../views/menjar.view.php';
?>