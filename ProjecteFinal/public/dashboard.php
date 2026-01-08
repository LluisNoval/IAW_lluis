<?php
/**
 * FITXER: public/dashboard.php
 * DESCRIPCIÓ: Panell principal amb lògica d'ingredients i nous estils.
 */
session_start();
require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/flash_messages.php';
require_once __DIR__ . '/../src/ingredients.php'; // Incloem lògica d'ingredients

if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];
$rol = $_SESSION['rol'];

// --- ACCIONS (Canvi d\'estat) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accio'], $_POST['comanda_id'])) {
    $id = intval($_POST['comanda_id']);
    $accio = $_POST['accio'];

    // Lògica per CUINER
    if ($rol === 'cuiner') {
        if ($accio === 'comencar') {
            $dbConnection->query("UPDATE comandes SET estat = 'en_preparacio', cuiner_id = $user_id WHERE id = $id");
        } elseif ($accio === 'enllestir') {
            $dbConnection->query("UPDATE comandes SET estat = 'llest' WHERE id = $id");
        }
    }

    // Lògica per CLIENT o ADMIN (Recollir comanda)
    if (($rol === 'client' || $rol === 'admin') && $accio === 'recollir') {
        if ($rol === 'client') {
            // El client només pot recollir les seves pròpies comandes
            $stmt = $dbConnection->prepare("UPDATE comandes SET estat = 'entregat' WHERE id = ? AND usuari_id = ? AND estat = 'llest'");
            $stmt->bind_param("ii", $id, $user_id);
            $stmt->execute();
        } else {
            // L'admin pot marcar qualsevol comanda llesta com a entregada
            $stmt = $dbConnection->prepare("UPDATE comandes SET estat = 'entregat' WHERE id = ? AND estat = 'llest'");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
    }

    // Lògica per ADMIN (Eliminar Comanda)
    if ($rol === 'admin' && $accio === 'eliminar') {
        $stmt = $dbConnection->prepare("DELETE FROM comandes WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            set_flash_message('success', 'Comanda eliminada correctament.');
        } else {
            set_flash_message('error', 'Error al eliminar la comanda.');
        }
    }
    
    header("Location: dashboard.php"); exit;
}

// --- CÀRREGA DE COMANDES ---
if ($rol === 'cuiner' || $rol === 'admin') {
    $sql = "SELECT c.*, u.nom as client_nom, cu.nom as cuiner_nom 
            FROM comandes c JOIN usuaris u ON c.usuari_id = u.id 
            LEFT JOIN usuaris cu ON c.cuiner_id = cu.id 
            WHERE c.estat != 'entregat' ORDER BY c.created_at ASC";
} else {
    // El client només veu les SEVES pròpies comandes
    $sql = "SELECT c.*, cu.nom as cuiner_nom FROM comandes c 
            LEFT JOIN usuaris cu ON c.cuiner_id = cu.id 
            WHERE c.usuari_id = $user_id ORDER BY c.created_at DESC";
}
$comandes = $dbConnection->query($sql)->fetch_all(MYSQLI_ASSOC);

$estats_bonics = [
    'pendent' => 'Pendent d\'acceptar',
    'en_preparacio' => 'En preparació',
    'llest' => 'Llest per recollir', // Blau
    'entregat' => 'Entregat'
];

function getPlats($db, $id) {
    return $db->query("SELECT d.*, p.nom, p.id as plat_original_id FROM detalls_comanda d 
                       JOIN plats p ON d.plat_id = p.id WHERE d.comanda_id = $id")->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php require_once __DIR__ . '/../views/partials/header.php'; ?>
    <div class="container">
        <h1>Benvingut/da, <?= htmlspecialchars($_SESSION['username']) ?> (<?= htmlspecialchars($rol) ?>)</h1>
        <?php require_once __DIR__ . '/../views/partials/show_messages.php'; ?>

        <?php if ($rol === 'client'): ?>
            <a href="menjar.php" class="btn btn-primary" style="margin-bottom: 20px;">+ Nova Comanda</a>
        <?php endif; ?>

        <div class="dashboard-layout">
            <!-- LLISTA DE COMANDES -->
            <div class="orders-list">
                <h2>Les Comandes</h2>
                <?php if (empty($comandes)): ?>
                    <p>No hi ha comandes actives.</p>
                <?php endif; ?>

                <?php foreach ($comandes as $c):
                    // Apliquem classe de fons segons estat ?>
                    <div class="order-card bg-<?= $c['estat'] ?>">
                        <div style="display:flex; justify-content:space-between; flex-wrap: wrap;">
                            <strong>#<?= $c['id'] ?> - <?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></strong>
                            <span><?= $estats_bonics[$c['estat']] ?></span>
                        </div>
                        
                        <p>Total: <?= number_format($c['total'], 2) ?> € 
                           <?= ($rol !== 'client') ? '| Client: '.htmlspecialchars($c['client_nom'] ?? '') : '' ?>
                           | Cuiner: <?= $c['cuiner_nom'] ?? 'Pendent' ?></p>
                        
                        <ul style="list-style: none; padding-left: 0;">
                            <?php foreach (getPlats($dbConnection, $c['id']) as $p):
                                // Comprovem stock si som admin o cuiner
                                $falta_ingredient = false;
                                if ($rol !== 'client') {
                                    $falta_ingredient = !checkStockPlat($dbConnection, $p['plat_original_id']);
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

                        <?php if ($rol === 'cuiner'): ?>
                            <form method="POST" style="margin-top:10px;">
                                <input type="hidden" name="comanda_id" value="<?= $c['id'] ?>">
                                <?php if ($c['estat'] === 'pendent'): ?>
                                    <button name="accio" value="comencar" style="background:white; color:#333;">Cuinar</button>
                                <?php elseif ($c['estat'] === 'en_preparacio' && $c['cuiner_id'] == $user_id): ?>
                                    <button name="accio" value="enllestir" style="background:white; color:#333;">Enllestir</button>
                                <?php endif; ?>
                            </form>
                        <?php endif; ?>

                        <?php // BOTÓ PER RECOLLIR (Client i Admin) ?>
                        <?php if (($rol === 'client' || $rol === 'admin') && $c['estat'] === 'llest'): ?>
                            <form method="POST" style="margin-top:10px;">
                                <input type="hidden" name="comanda_id" value="<?= $c['id'] ?>">
                                <button name="accio" value="recollir" style="background:white; color:#333; cursor: pointer; padding: 5px 10px;">
                                    Marcar com Recollida
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php // BOTÓ PER ELIMINAR (Només Admin) ?>
                        <?php if ($rol === 'admin'): ?>
                            <form method="POST" style="margin-top:10px;" onsubmit="return confirm('Segur que vols eliminar aquesta comanda?');">
                                <input type="hidden" name="comanda_id" value="<?= $c['id'] ?>">
                                <button name="accio" value="eliminar" style="background: #e74c3c; color:white; cursor: pointer; padding: 5px 10px; border:none; border-radius:4px;">
                                    Eliminar Comanda
                                </button>
                            </form>
                        <?php endif; ?>

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
