<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Destrueix totes les variables de sessió.
$_SESSION = array();

// Si s'utilitzen cookies de sessió, també s'han d'esborrar.
// Nota: Això destruirà la sessió, i no només les dades de sessió!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalment, destrueix la sessió.
session_destroy();

// Comprova si hi ha un URL de redirecció.
$redirect_to = $_GET['redirect_to'] ?? 'login.php';
// Asegura que la redirecció sigui a una pàgina del mateix projecte per evitar Open Redirect.
$allowed_redirects = ['login.php', 'register.php', 'items.php', 'dashboard.php', 'index.php']; // Afegeix les teves pàgines permeses
if (!in_array($redirect_to, $allowed_redirects)) {
    $redirect_to = 'login.php'; // Redirigeix a login.php per defecte si l'URL no és segura.
}


// Redirecció final.
header("Location: " . $redirect_to);
exit();
?>