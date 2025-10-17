<?php
echo "<h2>Calcul IVA:</h2>";
//Comprova que el preu i el IVA tenen un valor
    if (isset($_POST['preu']) && isset($_POST['iva'])) {
        //Agafar dades de HTML
        $preu = $_POST['preu'];
        $iva = $_POST['iva'];
        // Calcular IVA
        $preuAmbIva = $preu + ($preu * $iva / 100);

        echo "Preu sense IVA: $preu €<br>";
        echo "Tipus d'IVA: $iva €<br>";
        echo "Preu amb IVA: $preuAmbIva €";
    } else {
        echo "No has possat preu.";
    }
?>

