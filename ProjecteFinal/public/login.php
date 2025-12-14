<?php
session_start(); //Inicia sessió

// Comprovem si el formulari s'ha enviat
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Agafem les dades del formulari
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Usuari i contrasenya correctes (exemple fix)
    $usuari_correcte = "admin";
    $contrasenya_correcta = "1234";

    // Comprovació
    if ($username === $usuari_correcte && $password === $contrasenya_correcta) {
        // Si són correctes, desm l'usuari a la sessió i redirigim
        $_SESSION['loggedin'] = true;   // IMPORTANT per al control d'accés
        $_SESSION['username'] = $username;

        header("Location: dashboard.php");
        exit();
    } else {
        // Si són incorrectes, mostrem un missatge
        echo "<p style='color:red;'>Usuari o contrasenya incorrectes!</p>";
    }
}

require_once __DIR__ . '/../views/login.view.php';
?>
