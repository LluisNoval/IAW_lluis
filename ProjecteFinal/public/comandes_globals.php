<?php
/**
 * FITXER: public/comandes_globals.php
 * DESCRIPCIÓ: Llista global de totes les comandes (històric) amb estil visual de dashboard.
 * Adaptat perquè els clients també puguin veure el seu propi historial.
 */
session_start();
require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/flash_messages.php';
require_once __DIR__ . '/../src/ingredients.php'; // Important per check d'ingredients

// Verificar que l'usuari està loguejat
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php"); exit;
}

$rol = $_SESSION['rol'];
$user_id = $_SESSION['user_id'];

// Mapa d'estats
$estats_bonics = [
    'pendent' => 'Pendent d\'acceptar',
    'en_preparacio' => 'En preparació',
    'llest' => 'Llest per recollir',
    'entregat' => 'Entregat'
];

// Construcció de la consulta segons el rol
if ($rol === 'admin' || $rol === 'cuiner') {
    // Admin i Cuiner veuen TOTES les comandes entregades
    $sql = "SELECT c.*, u.nom as client_nom, cu.nom as cuiner_nom 
            FROM comandes c JOIN usuaris u ON c.usuari_id = u.id 
            LEFT JOIN usuaris cu ON c.cuiner_id = cu.id 
            WHERE c.estat = 'entregat'
            ORDER BY c.created_at DESC";
} else {
    // Clients només veuen les SEVES comandes entregades
    $sql = "SELECT c.*, u.nom as client_nom, cu.nom as cuiner_nom 
            FROM comandes c JOIN usuaris u ON c.usuari_id = u.id 
            LEFT JOIN usuaris cu ON c.cuiner_id = cu.id 
            WHERE c.estat = 'entregat' AND c.usuari_id = $user_id
            ORDER BY c.created_at DESC";
}

$comandes = $dbConnection->query($sql)->fetch_all(MYSQLI_ASSOC);

function getPlats($db, $id) {
    // Note: included p.id as plat_original_id for stock check
    return $db->query("SELECT d.*, p.nom, p.id as plat_original_id FROM detalls_comanda d 
                       JOIN plats p ON d.plat_id = p.id WHERE d.comanda_id = $id")->fetch_all(MYSQLI_ASSOC);
}

$page_title = ($rol === 'client') ? 'El meu Historial de Comandes' : 'Llista Global de Comandes (Històric)';
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Comandes</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php require_once __DIR__ . '/../views/partials/header.php'; ?>
    <div class="container">
        <h1><?= $page_title ?></h1>
        
        <div class="dashboard-layout">
            <!-- LLISTA DE COMANDES -->
            <div class="orders-list">
                <?php if (empty($comandes)): ?>
                    <p>No hi ha comandes a l\'històric.</p>
                <?php endif; ?>

                <?php foreach ($comandes as $c): ?>
                    <div class="order-card bg-<?= $c['estat'] ?>">
                        <div style="display:flex; justify-content:space-between; flex-wrap: wrap;">
                            <strong>#<?= $c['id'] ?> - <?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></strong>
                            <span><?= $estats_bonics[$c['estat']] ?></span>
                        </div>
                        
                        <p>Total: <?= number_format($c['total'], 2) ?> € | 
                           Client: <?= htmlspecialchars($c['client_nom']) ?> | 
                           Cuiner: <?= $c['cuiner_nom'] ?? '<em>Cap</em>' ?></p>
                        
                        <ul style="list-style: none; padding-left: 0;">
                            <?php foreach (getPlats($dbConnection, $c['id']) as $p): ?>
                                <?php 
                                    // Comprovem stock (encara que sigui històric, mostrem si falta ARA mateix)
                                    $falta_ingredient = !checkStockPlat($dbConnection, $p['plat_original_id']);
                                    
                                    // Si ja està entregat, normalment no marquem falta d'stock a l'històric, queda lleig.
                                    if ($c['estat'] === 'entregat') {
                                        $falta_ingredient = false; 
                                    }
                                ?>
                                <li style="border-bottom: 1px solid rgba(255,255,255,0.3); padding: 5px 0;">
                                    <?= $p['quantitat'] ?>x 
                                    <span class="<?= $falta_ingredient ? 'text-danger-custom' : '' ?>">
                                        <?= htmlspecialchars($p['nom']) ?>
                                    </span>
                                    <?= $p['es_vega'] ? '(Vegà)' : '' ?> 
                                    <?= $p['sense_gluten'] ? '(S.Gluten)' : '' ?>
                                    <?php if($falta_ingredient): ?> 
                                        <small>(Falta Stock!)</small> 
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- LLEGENDA -->
            <aside class="legend-sidebar">
                <h3>Llegenda d'Estats</h3>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: var(--color-pendent);"></div>
                    <span>Pendent d'acceptar</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: var(--color-preparacio);"></div>
                    <span>En preparació</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: var(--color-llest);"></div>
                    <span>Llest per recollir</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: var(--color-entregat);"></div>
                    <span>Entregat</span>
                </div>
            </aside>
        </div>
    </div>
</body>
</html>
