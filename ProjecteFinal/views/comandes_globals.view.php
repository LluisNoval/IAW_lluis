<?php
// views/comandes_globals.view.php
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Comandes Globals</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .global-container { padding: 20px; }
        .orders-table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        .orders-table th, .orders-table td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        .orders-table th { background-color: #2c3e50; color: white; }
        .status-pill { padding: 4px 8px; border-radius: 12px; font-size: 0.85em; font-weight: bold; }
        .status-pendent { background: #f39c12; color: white; }
        .status-en_preparacio { background: #3498db; color: white; }
        .status-llest { background: #27ae60; color: white; }
        .items-list { font-size: 0.9em; list-style: none; padding: 0; }
        .items-list li { margin-bottom: 3px; }
        .dietary-info { color: #e67e22; font-size: 0.8em; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/partials/header.php'; ?>
    
    <div class="global-container">
        <h1>Llista Global de Comandes</h1>
        
        <table class="orders-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data i Hora</th>
                    <th>Client</th>
                    <th>Plats Demanats</th>
                    <th>Total</th>
                    <th>Estat</th>
                    <th>Assignada a</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($totes_les_comandes as $c):
                    // This is a comment inside the loop
                ?>
                    <tr>
                        <td>#<?php echo $c['id']; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($c['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($c['client_nom']); ?></td>
                        <td>
                            <ul class="items-list">
                                <?php 
                                $detalls = getDetallsComanda($dbConnection, $c['id']);
                                foreach ($detalls as $d): 
                                ?>
                                    <li>
                                        <strong><?php echo $d['quantitat']; ?>x</strong> <?php echo htmlspecialchars($d['plat_nom']); ?>
                                        <?php if ($d['es_vega'] || $d['sense_gluten']): ?>
                                            <span class="dietary-info">(<?php echo $d['es_vega'] ? 'Vegà' : ''; ?> <?php echo $d['sense_gluten'] ? 'S.G.' : ''; ?>)</span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td><strong><?php echo number_format($c['total'], 2); ?> €</strong></td>
                        <td>
                            <span class="status-pill status-<?php echo $c['estat']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $c['estat'])); ?>
                            </span>
                        </td>
                        <td>
                            <?php echo $c['cuiner_nom'] ? htmlspecialchars($c['cuiner_nom']) : '<em>Pendent d\'assignar</em>'; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
