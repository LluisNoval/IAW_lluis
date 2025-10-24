<?php
// Agafar estil triat
$estilMusica = $_POST['tipus'];

// Determinar el missatge segons l'estil
if ($estilMusica == "Noise") {
    $missatge = "T'agrada el $estilMusica";
} elseif ($estilMusica == "Blues Rock") {
    $missatge = "T'agrada el $estilMusica";
} elseif ($estilMusica == "African Blues") {
    $missatge = "T'agrada $estilMusica";
} elseif ($estilMusica == "New-age") {
    $missatge = "T'agrada el $estilMusica";
} elseif ($estilMusica == "Electro-disco") {
    $missatge = "T'agrada l'$estilMusica";
} elseif ($estilMusica == "Dubtronica") {
    $missatge = "T'agrada el $estilMusica";
} else {
    $missatge = "No t'agrada res";
}

// Mostrar el resultat
echo "<h1>La teva musica preferida es</h1>";
if ($estilMusica == "African Blues") {
    echo "<h2>L'$estilMusica</h2>";
} elseif ($estilMusica == "Electro-disco") {
    echo "<h2>L'$estilMusica</h2>";
} else {
    echo "<h2>El $estilMusica</h2>";
}
echo "<p>$missatge</p>";
echo "<br>";
echo '<a href="index.html">Tornar a l\'enquesta</a>';
?>