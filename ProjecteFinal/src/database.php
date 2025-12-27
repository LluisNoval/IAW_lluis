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
 * @return mysqli L'objecte de connexió.
 */
function getMysqliConnection(): mysqli {
    // Connexió a la base de dades.
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_error) {
        // En un entorn de producció, això s'hauria de registrar en un log.
        // Per ara, aturem l'execució i mostrem l'error.
        die("Error de connexió a la base de dades: " . $mysqli->connect_error);
    }
    
    // Assegura que la connexió utilitzi UTF-8.
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