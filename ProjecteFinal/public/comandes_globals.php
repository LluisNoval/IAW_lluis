<?php
// public/comandes_globals.php - Llista global de comandes per a admin i cuiners
session_start();
require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/flash_messages.php';

// Només admin i cuiner
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'cuiner' && $_SESSION['rol'] !== 'admin')) {
    header("Location: dashboard.php"); exit;
}

// Mapa d'estats per mostrar-los bé
$estats_bonics = [
    'pendent' => 'Pendent d\'acceptar',
    'en_preparacio' => 'En preparació',
    'llest' => 'Llest per recollir',
    'entregat' => 'Entregat'
];

// Consulta de totes les comandes
$sql = "SELECT c.*, u.nom as client_nom, cu.nom as cuiner_nom 
        FROM comandes c JOIN usuaris u ON c.usuari_id = u.id 
        LEFT JOIN usuaris cu ON c.cuiner_id = cu.id ORDER BY c.created_at DESC";
$comandes = $dbConnection->query($sql)->fetch_all(MYSQLI_ASSOC);

// Funció per treure els plats
function getPlats($db, $id) {
    return $db->query("SELECT d.*, p.nom FROM detalls_comanda d 
                       JOIN plats p ON d.plat_id = p.id WHERE d.id = $id")->fetch_all(MYSQLI_ASSOC); // Corregit plat_id per nom
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8"><title>Comandes Globals</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php require_once __DIR__ . '/../views/partials/header.php'; ?>
    <div class="container">
        <h1>Llista Global de Comandes</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Data</th><th>Client</th><th>Estat</th><th>Cuiner</th><th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comandes as $c): ?>
                    <tr>
                        <td>#<?= $c['id'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
                        <td><?= htmlspecialchars($c['client_nom']) ?></td>
                        <td><span class="status-pill status-<?= $c['estat'] ?>"><?= $estats_bonics[$c['estat']] ?></span></td>
                        <td><?= $c['cuiner_nom'] ?? '<em>Cap</em>' ?></td>
                        <td><strong><?= number_format($c['total'], 2) ?> €</strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>