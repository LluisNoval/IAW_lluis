<?php
// src/database.php

declare(strict_types=1);

// Configuració
const DB_HOST = 'localhost';
const DB_NAME = 'projectefinal';
const DB_USER = 'root';
const DB_PASS = '';

/**
 * Estableix la connexió amb MySQLi, creant la BD i la taula si és necessari.
 * @return mysqli L'objecte de connexió.
 */
function getMysqliConnection(): mysqli {
    // 1. Connexió inicial al servidor per crear la BD
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS);
    if ($mysqli->connect_error) {
        die("Error de connexió al servidor MySQL: " . $mysqli->connect_error);
    }

    // 2. Crear la base de dades si no existeix
    $mysqli->query(
        "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` 
         CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    );

    // Seleccionar la BD
    $mysqli->select_db(DB_NAME);

    // 3. Crear la taula si no existeix
    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS usuaris (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom_usuari VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            contrasenya VARCHAR(255) NOT NULL,
            data_registre TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB"
    );

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
 * @return string|false El nom d'usuari si és correcte, altrament false.
 */
function verificarUsuari(mysqli $dbConnection, string $login_identifier, string $contrasenya): string|false {
    $stmt = $dbConnection->prepare(
        "SELECT nom_usuari, contrasenya FROM usuaris WHERE nom_usuari = ? OR email = ? LIMIT 1"
    );
    $stmt->bind_param('ss', $login_identifier, $login_identifier);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        return false; // L'usuari/email no existeix
    }

    // Si la contrasenya és correcta, retorna el nom d'usuari.
    if (password_verify($contrasenya, $row['contrasenya'])) {
        return $row['nom_usuari'];
    }

    return false; // Contrasenya incorrecta
}
?>