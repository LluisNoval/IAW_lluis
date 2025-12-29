<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Data passada per assegurar-se que expira immediatament

// Controlador de registre.
require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/flash_messages.php';

// Si l'usuari ja ha iniciat sessió, el redirigim a logout per forçar un tancament de sessió abans de veure el registre.
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: logout.php?redirect_to=" . urlencode("register.php"));
    exit();
}

// Comprova si s'ha enviat el formulari.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_usuari = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $contrasenya = $_POST['password'] ?? '';
    $confirm_contrasenya = $_POST['confirm_password'] ?? '';

    // Validacions
    if (empty($nom_usuari) || empty($email) || empty($contrasenya)) {
        set_flash_message('error', 'Tots els camps són obligatoris.');
    } 
    // Validacions nom usuari.
    elseif (strlen($nom_usuari) < 3 || strlen($nom_usuari) > 20) {
        set_flash_message('error', 'El nom d\'usuari ha de tenir entre 3 i 20 caràcters.');
    }
    elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $nom_usuari)) {
        set_flash_message('error', 'El nom d\'usuari només pot contenir lletres, nombres i guions baixos.');
    }
    // Validar correu.
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash_message('error', 'El format del correu electrònic no és vàlid.');
    } 
    //Validar contrasenyes.
    elseif ($contrasenya !== $confirm_contrasenya) {
        set_flash_message('error', 'Les contrasenyes no coincideixen.');
    }
    elseif ($contrasenya == $nom_usuari or $contrasenya == $email) {
        set_flash_message('error', 'La contrasenya no pot ser el nom d\'usuari ni el correu.');
    }
    elseif (strlen($contrasenya) < 3 || strlen($contrasenya) > 20) {
        set_flash_message('error', 'La contrasenya ha de tenir entre 3 i 20 caràcters.');
    } 
    else {
        // Intentar registrar l'usuari.
        $exit = registrarUsuari($dbConnection, $nom_usuari, $email, $contrasenya);
        
        if ($exit) {
            // Èxit, redirigir al login amb un missatge.
            set_flash_message('success', 'Compte creat correctament! Ja pots iniciar sessió.');
            header("Location: login.php");
            exit();
        } else {
            // Error, probablement l'usuari o l'email ja existeixen.
            set_flash_message('error', 'Aquest nom d\'usuari o correu electrònic ja està en ús.');
        }
    }
}

// Carrega la vista del formulari.
require_once __DIR__ . '/../views/register.view.php';
?>