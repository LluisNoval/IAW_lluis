<?php
// views/partials/header.php

// Assegura que la sessió estigui iniciada a totes les pàgines
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header style="width: 100%; box-sizing: border-box; background-color: #f2f2f2; padding: 10px 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 style="margin: 0; font-size: 1.5em;">ONI DB</h1>
    </div>
    <div>
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <span>
                Benvingut/da, 
                <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> 
                (Rol: <?php echo htmlspecialchars($_SESSION['rol']); ?>)
            </span>
            <a href="items.php" style="margin-left: 15px;">Ítems</a>
            <a href="logout.php" style="margin-left: 15px;">Tancar Sessió</a>
        <?php else: ?>
            <span>Benvingut/da a la plataforma</span>
        <?php endif; ?>
    </div>
</header>
