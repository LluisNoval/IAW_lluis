<?php
/**
 * FITXER: src/database.php
 * DESCRIPCIÓ: Gestiona la connexió a la base de dades MySQL, la creació de taules automàtica
 * i les funcions principals de gestió d'usuaris (registre i verificació).
 */

const DB_HOST = 'localhost';
const DB_NAME = 'projectefinal';
const DB_USER = 'root';
const DB_PASS = '';

/**
 * Estableix la connexió i prepara l'entorn de la base de dades.
 */
function getMysqliConnection(): mysqli {
    // Connectem al servidor MySQL sense especificar base de dades primer
    $mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS);
    if ($mysqli->connect_error) die("Error de connexió: " . $mysqli->connect_error);

    // Creem la base de dades si és el primer cop que s'executa
    $mysqli->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $mysqli->select_db(DB_NAME);
    $mysqli->set_charset('utf8mb4');

    // Comprovem si la taula 'usuaris' existeix. Si no, executem l'script SQL complet.
    $check = $mysqli->query("SHOW TABLES LIKE 'usuaris'");
    if ($check->num_rows == 0) {
        $sql = file_get_contents(__DIR__ . '/../database.sql');
        $mysqli->multi_query($sql);
        // Aquest bucle és necessari per netejar els resultats de multi_query
        while ($mysqli->next_result()) { if (!$mysqli->more_results()) break; }
    }
    
    return $mysqli;
}

/**
 * Crea els usuaris per defecte si no existeixen a la base de dades.
 */
function ensureUsers(mysqli $db): void {
    $pass_admin = password_hash('admin1234', PASSWORD_DEFAULT);
    $pass_cuiner = password_hash('cuiner1234', PASSWORD_DEFAULT);

    // Inserim l'administrador principal
    $db->query("INSERT IGNORE INTO usuaris (nom, email, password_hash, role_id) 
                VALUES ('admin', 'admin@gmail.com', '$pass_admin', 3)");
    
    // Inserim els cuiners de prova
    $db->query("INSERT IGNORE INTO usuaris (nom, email, password_hash, role_id) 
                VALUES ('Ferran', 'ferran@restaurant.com', '$pass_cuiner', 2)");
    $db->query("INSERT IGNORE INTO usuaris (nom, email, password_hash, role_id) 
                VALUES ('Carme', 'carme@restaurant.com', '$pass_cuiner', 2)");
}

// Inicialització global de la connexió
$dbConnection = getMysqliConnection();
ensureUsers($dbConnection);

/**
 * Registra un usuari nou (per defecte rol client, ID 1).
 */
function registrarUsuari($db, $nom, $email, $pass): bool {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO usuaris (nom, email, password_hash, role_id) VALUES (?, ?, ?, 1)");
    $stmt->bind_param('sss', $nom, $email, $hash);
    return $stmt->execute();
}

/**
 * Comprova les credencials del login.
 * Retorna l'array de dades de l'usuari si és correcte, o false si no.
 */
function verificarUsuari($db, $login, $pass) {
    $stmt = $db->prepare("SELECT u.id, u.nom, u.password_hash, r.name as rol 
                          FROM usuaris u JOIN roles r ON u.role_id = r.id 
                          WHERE LOWER(u.nom) = LOWER(?) OR LOWER(u.email) = LOWER(?)");
    $stmt->bind_param('ss', $login, $login);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // Verificació xifrada de la contrasenya
    if ($user && password_verify($pass, $user['password_hash'])) {
        return $user;
    }
    return false;
}
?>
