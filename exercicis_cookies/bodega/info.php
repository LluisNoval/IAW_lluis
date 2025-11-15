<?php
// cookies
$majoredat = $_COOKIE["majoredat"] ?? "si";
$idioma = $_COOKIE["idioma"] ?? "ca";
$moneda = $_COOKIE["moneda"] ?? "eur";

// Traduccions
$traduccions = [
    "ca" => [
        "menor" => "No et podem vendre alcohol si ets menor d'edat.",
        "titol" => "Productes disponibles",
        "vi1" => 'Vi "Les Terrasses"',
        "vi2" => "Priorat Selecció"
    ],
    "es" => [
        "menor" => "No podemos venderte alcohol si eres menor de edad.",
        "titol" => "Productos disponibles",
        "vi1" => 'Vino "Les Terrasses"',
        "vi2" => "Priorat Selección"
    ],
    "en" => [
        "menor" => "We cannot sell you alcohol if you are underage.",
        "titol" => "Available products",
        "vi1" => '"Les Terrasses" wine',
        "vi2" => "Priorat Selection"
    ]
];

// Preu base de "Les Terrasses" segons condicions
$preuTerrasses = ($idioma === "ca" && $moneda === "eur") ? 39 : 50;

// Preus base en euros
$preus = ["terrasses" => $preuTerrasses, "priorat" => 29];

// Conversió de moneda
$conversio = ["eur" => 1, "gbp" => 0.86, "usd" => 1.10];
$simbols = ["eur" => "€", "gbp" => "£", "usd" => "$"];

// Convertir preus
function formatarPreu($preu, $moneda, $conversio, $simbols) {
    $preuFinal = $preu * $conversio[$moneda];
    $simbol = $simbols[$moneda];
    
    return ($moneda === "eur") 
        ? number_format($preuFinal, 2) . " $simbol"
        : "$simbol" . number_format($preuFinal, 2);
}

$t = $traduccions[$idioma]; // Accés ràpid a traduccions
?>
<!DOCTYPE html>
<html lang="<?=$idioma?>">
<head>
    <meta charset="UTF-8">
    <title>Informació</title>
</head>
<body>

<?php if ($majoredat === "no"): ?>
    <h2><?=$t["menor"]?></h2>
<?php else: ?>
    <h1><?=$t["titol"]?></h1>
    <ul>
        <li>
            <b><?=$t["vi1"]?></b> — 
            <?=formatarPreu($preus["terrasses"], $moneda, $conversio, $simbols)?>
        </li>
        <li>
            <b><?=$t["vi2"]?></b> — 
            <?=formatarPreu($preus["priorat"], $moneda, $conversio, $simbols)?>
        </li>
    </ul>
<?php endif; ?>

</body>
</html>