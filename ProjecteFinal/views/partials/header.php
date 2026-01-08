<?php
// views/partials/header.php

// Assegura que la sessió estigui iniciada a totes les pàgines
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header style="width: 100%; box-sizing: border-box; background-color: #f2f2f2; padding: 10px 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <a href="../index.php" style="text-decoration: none;">
            <h1 style="margin: 0; font-size: 1.8rem; color: var(--primary); font-weight: 800; cursor: pointer;">CUINA</h1>
        </a>
    </div>
    <div>
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <span>
                Benvingut/da, 
                <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> 
                (Rol: <?php echo htmlspecialchars($_SESSION['rol']); ?>)
            </span>
            <a href="dashboard.php" style="margin-left: 15px;">Dashboard</a>
            <?php if ($_SESSION['rol'] === 'admin'): ?>
                <a href="admin_users.php" style="margin-left: 15px;">Usuaris</a>
                <a href="admin_plats.php" style="margin-left: 15px;">Gestió Plats</a>
            <?php endif; ?>
            <?php if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'cuiner'): ?>
                <a href="comandes_globals.php" style="margin-left: 15px;">Comandes Globals</a>
                <a href="ingredients_resum.php" style="margin-left: 15px;">Ingredients</a>
            <?php endif; ?>
            <?php if ($_SESSION['rol'] === 'client'): ?>
                <a href="menjar.php" style="margin-left: 15px;">Menú</a>
                <a href="comandes_globals.php" style="margin-left: 15px;">El meu Historial</a>
            <?php endif; ?>
            <a href="logout.php" style="margin-left: 15px;">Tancar Sessió</a>
        <?php else: ?>
            <span>Benvingut/da a la plataforma</span>
        <?php endif; ?>
    </div>
</header>
