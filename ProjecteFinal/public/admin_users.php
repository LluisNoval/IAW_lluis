<?php
/**
 * FITXER: public/admin_users.php
 * DESCRIPCIÓ: Pàgina de gestió d'usuaris exclusiva per a l'administrador.
 * Permet visualitzar tots els usuaris i canviar-los el rol.
 */
session_status() == PHP_SESSION_NONE ? session_start() : null;

require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/flash_messages.php';

/**
 * SEGURETAT
 * Només l'administrador pot entrar. Si no, el fem fora al dashboard.
 */
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    set_flash_message('error', 'No tens permisos per accedir a la gestió d\'usuaris.');
    header("Location: dashboard.php");
    exit();
}

/**
 * ACTUALITZACIÓ DE ROL
 * Processa el formulari quan es canvia el rol d'un usuari
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $target_user_id = intval($_POST['user_id']);
    $new_role_id = intval($_POST['new_role_id']);

    // Mesura de seguretat: L'admin no pot treure's el seu propi rol d'admin
    if ($target_user_id === $_SESSION['user_id'] && $new_role_id != 3) {
        set_flash_message('error', 'No pots treure el teu propi rol d\'administrador per seguretat.');
    } else {
        $stmt = $dbConnection->prepare("UPDATE usuaris SET role_id = ? WHERE id = ?");
        $stmt->bind_param('ii', $new_role_id, $target_user_id);
        
        if ($stmt->execute()) {
            set_flash_message('success', 'Rol de l\'usuari actualitzat amb èxit.');
        } else {
            set_flash_message('error', 'Hi ha hagut un error al canviar el rol.');
        }
    }
    header("Location: admin_users.php");
    exit();
}

/**
 * CONSULTA D'USUARIS
 * Obtenim tots els usuaris amb el nom del seu rol
 */
$query_users = "SELECT u.id, u.nom, u.email, u.role_id, r.name as role_name 
                FROM usuaris u 
                JOIN roles r ON u.role_id = r.id 
                ORDER BY u.id ASC";
$res_users = $dbConnection->query($query_users);
$usuaris = $res_users->fetch_all(MYSQLI_ASSOC);

/**
 * LLISTA DE ROLS
 * Obtenim tots els rols disponibles per omplir el menú desplegable (Select)
 */
$res_roles = $dbConnection->query("SELECT id, name FROM roles");
$tots_els_rols = $res_roles->fetch_all(MYSQLI_ASSOC);

require_once __DIR__ . '/../views/admin_users.view.php';
?>
