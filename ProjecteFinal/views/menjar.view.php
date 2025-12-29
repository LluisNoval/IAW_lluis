<?php
// views/menjar.view.php
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú del Restaurant - La Nostra Cuina</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php require_once __DIR__ . '/partials/header.php'; ?>
    
    <div class="container">
        <header style="background: none; box-shadow: none; padding: 40px 0; text-align: center; flex-direction: column;">
            <h1 style="font-size: 3rem; margin-bottom: 10px;">La Nostra Cuina</h1>
            <p style="color: var(--grey); font-size: 1.2rem;">Escull els teus plats preferits i te'ls preparem al moment</p>
        </header>

        <?php require_once __DIR__ . '/partials/show_messages.php'; ?>

        <form action="menjar.php" method="POST">
            <div class="menu-grid">
                <?php foreach ($plats as $plat): ?>
                    <div class="menu-item">
                        <div class="menu-content">
                            <h3><?php echo htmlspecialchars($plat['nom']); ?></h3>
                            <p style="color: #666; font-size: 0.95rem; margin-bottom: 15px;">
                                <?php echo htmlspecialchars($plat['descripcio']); ?>
                            </p>
                            <div class="price"><?php echo number_format($plat['preu'], 2); ?> €</div>
                            
                            <div class="dietary-tags">
                                <label class="badge badge-vega">
                                    <input type="checkbox" name="items[<?php echo $plat['id']; ?>][vega]"> Vegà
                                </label>
                                <label class="badge badge-gluten">
                                    <input type="checkbox" name="items[<?php echo $plat['id']; ?>][sense_gluten]"> Sense Gluten
                                </label>
                            </div>

                            <div class="form-group" style="margin-bottom: 10px;">
                                <label style="font-size: 0.85rem;">Quantitat</label>
                                <input type="number" name="items[<?php echo $plat['id']; ?>][quantitat]" value="0" min="0" max="10" style="padding: 8px;">
                            </div>
                            
                            <input type="text" name="items[<?php echo $plat['id']; ?>][comentaris]" 
                                   placeholder="Alguna observació?" 
                                   class="form-group" style="padding: 8px; font-size: 0.85rem; border-style: dashed;">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="card" style="margin-top: 40px; border-left: 5px solid var(--secondary);">
                <h3 style="margin-top: 0;">Finalitza la teva comanda</h3>
                <div class="form-group">
                    <textarea name="notes_generals" placeholder="Vols dir-nos alguna cosa sobre el lliurament o la comanda en general?" rows="3"></textarea>
                </div>
                <div style="text-align: right;">
                    <button type="submit" name="realitzar_comanda" class="btn-primary" style="width: auto; padding: 15px 40px;">Enviar a Cuina</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>