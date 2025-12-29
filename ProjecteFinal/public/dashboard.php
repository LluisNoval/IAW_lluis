<?php
/**
 * FITXER: public/dashboard.php
 * DESCRIPCIÓ: Panell principal de l'usuari. Mostra comandes pròpies als clients
 * i totes les comandes actives als cuiners i administradors.
 */
session_start();
require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/flash_messages.php';

// Si l'usuari no està loguejat, el redirigim a la pàgina de login
if (!isset($_SESSION['loggedin'])) { header("Location: login.php"); exit; }

$user_id = $_SESSION['user_id'];
$rol = $_SESSION['rol'];

/**
 * GESTIÓ D'ACCIONS DEL CUINER
 * Processa quan un cuiner accepta o finalitza una comanda
 */
if ($rol === 'cuiner' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['comanda_id']);
    if ($_POST['accio'] === 'comencar') {
        // Assignem la comanda al cuiner loguejat i canviem estat
        $dbConnection->query("UPDATE comandes SET estat = 'en_preparacio', cuiner_id = $user_id WHERE id = $id");
    } else {
        // Marquem la comanda com a llista per recollir
        $dbConnection->query("UPDATE comandes SET estat = 'llest' WHERE id = $id");
    }
    header("Location: dashboard.php"); exit;
}

/**
 * CÀRREGA DE DADES
 * Seleccionem les comandes que l'usuari ha de veure segons el seu rol
 */
if ($rol === 'cuiner' || $rol === 'admin') {
    // Cuiners i Admins veuen totes les comandes que encara no s'han entregat
    $sql = "SELECT c.*, u.nom as client_nom, cu.nom as cuiner_nom 
            FROM comandes c JOIN usuaris u ON c.usuari_id = u.id 
            LEFT JOIN usuaris cu ON c.cuiner_id = cu.id 
            WHERE c.estat != 'entregat' ORDER BY c.created_at ASC";
} else {
    // Els clients només veuen les seves pròpies comandes (històric)
    $sql = "SELECT c.*, cu.nom as cuiner_nom FROM comandes c 
            LEFT JOIN usuaris cu ON c.cuiner_id = cu.id 
            WHERE c.usuari_id = $user_id ORDER BY c.created_at DESC";
}

$comandes = $dbConnection->query($sql)->fetch_all(MYSQLI_ASSOC);

/**
 * TRADUCCIÓ D'ESTATS
 * Mapa per mostrar els valors de la base de dades d'una forma més elegant
 */
$estats_bonics = [
    'pendent' => 'Pendent d\'acceptar',
    'en_preparacio' => 'En preparació',
    'llest' => 'Llest per recollir',
    'entregat' => 'Entregat'
];

/**
 * Obté la llista de plats d'una comanda específica
 */
function getPlats($db, $id) {
    return $db->query("SELECT d.*, p.nom FROM detalls_comanda d 
                       JOIN plats p ON d.plat_id = p.id WHERE d.comanda_id = $id")->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8"><title>Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php require_once __DIR__ . '/../views/partials/header.php'; ?>
    <div class="container">
        <h1>Benvingut/da, <?= htmlspecialchars($_SESSION['username']) ?></h1>
        <?php require_once __DIR__ . '/../views/partials/show_messages.php'; ?>

        <?php if ($rol === 'client'): ?>
            <a href="menjar.php" class="btn btn-primary" style="width:auto">+ Nova Comanda</a>
        <?php endif; ?>

        <h2>Les Comandes</h2>
        <?php foreach ($comandes as $c): ?>
            <div class="card">
                <div style="display:flex; justify-content:space-between">
                    <strong>#<?= $c['id'] ?> - <?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></strong>
                    <span class="status-pill status-<?= $c['estat'] ?>"><?= $estats_bonics[$c['estat']] ?></span>
                </div>
                
                <p>Total: <?= number_format($c['total'], 2) ?> € | 
                   Cuiner: <?= $c['cuiner_nom'] ?? 'Pendent' ?></p>
                
                <ul>
                    <?php foreach (getPlats($dbConnection, $c['id']) as $p): ?>
                        <li><?= $p['quantitat'] ?>x <?= htmlspecialchars($p['nom']) ?> 
                            <?= $p['es_vega'] ? '(V)' : '' ?> <?= $p['sense_gluten'] ? '(SG)' : '' ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if ($rol === 'cuiner'): ?>
                    <form method="POST">
                        <input type="hidden" name="comanda_id" value="<?= $c['id'] ?>">
                        <?php if ($c['estat'] === 'pendent'): ?>
                            <button name="accio" value="comencar" class="btn btn-primary">Cuinar</button>
                        <?php elseif ($c['estat'] === 'en_preparacio' && $c['cuiner_id'] == $user_id): ?>
                            <button name="accio" value="enllestir" class="btn btn-success">Enllestir</button>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
