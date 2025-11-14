<?php
$cookie_name = "visites";
$textDescompte = "hola";

//Valor cookie
if(isset($_COOKIE[$cookie_name])) {
    $visites = $_COOKIE[$cookie_name] + 1;
}
else {
    $visites = 1;
}

//Guardi cookie durant 1 any
setcookie($cookie_name, $visites, time() + (365 * 24 *60 *60))
prova;

// Mostra missatge segons el nombre de visites
if ($visites >= 10) {
    $textDescompte = "Oferta exclusiva sols per a tu! Utilitza el codi <b>BOTIGA50</b> per obtenir un 50% de descompte en les teves primeres compres a la botiga.";
} elseif ($visites >= 5) {
    $textDescompte = "Oferta exclusiva! Utilitza el codi <b>BOTIGA20</b> per obtenir un 20% de descompte en les teves primeres compres a la botiga.";
}
?>

<!DOCTYPE html>
<html lang="ca">
<body>
    <h1>Tenda</h1>
    <h3>Amb compador de visites!!</h3>
    <p>Has visitat la tenda <?=$visites?> vegades.</p>
    <p><?=$textDescompte?></p>
    <button>
        <p>
            Aplica descompte
        </p>
    </button>
    <br>
    <br>
    <button>
        <p>
            Compra
        </p>
    </button>
</body>
</html>