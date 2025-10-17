<?php
// Obtenir l'hora del server
$hora = date('G'); // 'G' retorna l'hora en format 0-23

// Mostrar l'hora del server
$horaCompleta = date('H:i:s');

// Text segons hora
if ($hora >= 5 && $hora < 14) {
    $text = "Bon dia";
}
elseif ($hora >= 14 && $hora < 19) {
    $text = "Bona tarda";
}
else {
    $tex = "Bona nit";
}

// Mostrar el resultat
echo "<h1>$text</h1>";
echo "<p>Hora del servidor: $horaCompleta</p>";
?>