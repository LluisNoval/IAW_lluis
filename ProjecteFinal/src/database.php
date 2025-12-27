<?php
// src/database.php

declare(strict_types=1);

// Configuració
const DB_HOST = 'localhost';
const DB_NAME = 'projectefinal';
const DB_USER = 'root';
const DB_PASS = '';

/**
 * Estableix la connexió amb la base de dades MySQLi.
 * Si la base de dades o les taules no existeixen, les crea automàticament.
 * @return mysqli L'objecte de connexió.
 */
function getMysqliConnection(): mysqli {
    // 1. Connexió inicial al servidor.
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS);
    if ($mysqli->connect_error) {
        die("Error de connexió al servidor MySQL: " . $mysqli->connect_error);
    }

    // 2. Crear la base de dades si no existeix.
    $mysqli->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // 3. Seleccionar la base de dades.
    $mysqli->select_db(DB_NAME);

    // 4. Comprovar si la taula 'usuaris' existeix. Si no, executem el .sql sencer.
    $tables_exist = $mysqli->query("SHOW TABLES LIKE 'usuaris'")->num_rows > 0;

    if (!$tables_exist) {
        // La BD existeix però està buida, així que executem l'script per crear les taules.
        $sql_file = __DIR__ . '/../database.sql';
        if (!file_exists($sql_file)) {
            die("Error crític: No s'ha trobat el fitxer 'database.sql' per crear les taules.");
        }

        $sql_commands = file_get_contents($sql_file);
        
        if ($mysqli->multi_query($sql_commands)) {
            // Buidem els resultats de multi_query abans de continuar.
            while ($mysqli->more_results() && $mysqli->next_result()) {
                if ($result = $mysqli->store_result()) {
                    $result->free();
                }
            }
        } else {
            die("Error en executar el fitxer database.sql: " . $mysqli->error);
        }
    }
    
    // 5. Assegurar que la connexió utilitzi UTF-8 i retornar-la.
    $mysqli->set_charset('utf8mb4');
    return $mysqli;
}

// Connexió disponible per als fitxers que incloguin aquest
$dbConnection = getMysqliConnection();

/**
 * Registra un usuari nou. Retorna false si ja existeix o hi ha error.
 */
function registrarUsuari(mysqli $dbConnection, string $nom_usuari, string $email, string $contrasenya): bool {
    $hash = password_hash($contrasenya, PASSWORD_DEFAULT);

    $stmt = $dbConnection->prepare(
        "INSERT INTO usuaris (nom_usuari, email, contrasenya) VALUES (?, ?, ?)"
    );
    $stmt->bind_param('sss', $nom_usuari, $email, $hash);
    
    // Si l'execució falla, pot ser per un duplicat (errno 1062)
    if (!$stmt->execute()) {
        if ($dbConnection->errno === 1062) {
            return false; // Nom d'usuari o email duplicat
        }
        return false; // Un altre error
    }
    
    return true;
}

/**
 * Verifica credencials d'usuari (per nom d'usuari o per email).
 * @return array|false Un array amb nom_usuari i rol si és correcte, altrament false.
 */
function verificarUsuari(mysqli $dbConnection, string $login_identifier, string $contrasenya): array|false {
    $stmt = $dbConnection->prepare(
        "SELECT nom_usuari, contrasenya, rol FROM usuaris WHERE nom_usuari = ? OR email = ? LIMIT 1"
    );
    $stmt->bind_param('ss', $login_identifier, $login_identifier);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        return false; // L'usuari/email no existeix
    }

    // Si la contrasenya és correcta, retorna el nom d'usuari i el seu rol.
    if (password_verify($contrasenya, $row['contrasenya'])) {
        return [
            'nom_usuari' => $row['nom_usuari'],
            'rol' => $row['rol']
        ];
    }

    return false; // Contrasenya incorrecta
}
?>