<?php
// views/partials/show_messages.php

// Aquesta funció ha d'estar disponible, així que la requerim si cal.
// El controlador principal ja l'hauria d'haver inclòs.
if (function_exists('get_flash_messages')) {
    $messages = get_flash_messages();

    if (!empty($messages)) {
        foreach ($messages as $msg) {
            $type = htmlspecialchars($msg['type']);
            $message = htmlspecialchars($msg['message']);
            
            // Defineix el color de fons basat en el tipus de missatge
            $color = 'grey'; // Color per defecte
            if ($type === 'error') {
                $color = '#f8d7da'; // Vermell clar per a errors
            } elseif ($type === 'success') {
                $color = '#d4edda'; // Verd clar per a èxits
            }

            // Mostra el missatge
            echo "<div class='flash-message' style='padding: 10px; margin-bottom: 15px; border-radius: 5px; background-color: {$color}; text-align: center;'>";
            echo $message;
            echo "</div>";
        }
    }
}
