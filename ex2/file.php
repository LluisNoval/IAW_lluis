<?php
// Agafa dades form
$quantitat = floatval($_POST['quantitat'] ?? 0);
$tipus = $_POST['tipus'] ?? 'eurusd';

// Valors de conversio
$eur_usd = 1.08;
$usd_eur = 0.93;

// Conversio
if ($tipus == 'eurusd') {
    $resultat = $quantitat * $eur_usd;
    echo "$quantitat € són $resultat $";
}
else {
    $resultat = $quantitat * $usd_eur;
    echo "$quantitat $ són $resultat €";
}

// Tronar al form
echo '<br><a href="index.html">Tornar</a>';
?>
