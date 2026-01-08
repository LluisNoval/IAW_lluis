<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Controlador de login.
require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/flash_messages.php';

// Si l'usuari ja ha iniciat sessió, el redirigim a logout per forçar un tancament de sessió abans de veure el login.
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: logout.php?redirect_to=" . urlencode("login.php"));
    exit();
}

// Comprova l'enviament del formulari.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_identifier = $_POST['login_identifier'] ?? '';
    $contrasenya = $_POST['password'] ?? '';

    if (empty($login_identifier) || empty($contrasenya)) {
        set_flash_message('error', 'Has d\'introduir usuari/correu i contrasenya.');
    } else {
        // Validació de caràcters permesos
        $es_email = str_contains($login_identifier, '@');
        $valid = false;
        if ($es_email) {
            // Si sembla un email, el validem com a tal
            if (filter_var($login_identifier, FILTER_VALIDATE_EMAIL)) {
                $valid = true;
            }
        } else {
            // Si no, el validem com a nom d'usuari
            if (preg_match('/^[a-zA-Z0-9_]+$/', $login_identifier)) {
                $valid = true;
            }
        }

        if (!$valid) {
            set_flash_message('error', 'El format de l\'usuari o correu no és vàlid.');
        } else {
            // Validació amb la base de dades.
            // La funció retorna un array amb dades de l'usuari si l'autenticació és correcta.
            $dades_usuari = verificarUsuari($dbConnection, $login_identifier, $contrasenya);
            
            if ($dades_usuari) {
                // Inicia la sessió de l'usuari.
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $dades_usuari['id']; // Add user_id to session
                $_SESSION['username'] = $dades_usuari['nom'];
                $_SESSION['rol'] = $dades_usuari['rol'];

                // Redirecció al dashboard.
                header("Location: dashboard.php");
                exit();
            } else {
                // Missatge d'error.
                set_flash_message('error', 'L\'usuari/correu o la contrasenya són incorrectes.');
            }
        }
    }
}

// Carrega la vista del login.
require_once __DIR__ . '/../views/login.view.php';
