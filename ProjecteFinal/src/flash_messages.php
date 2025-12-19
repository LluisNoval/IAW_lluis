<?php
// src/flash_messages.php

/**
 * Guarda un missatge flash a la sessió.
 *
 * @param string $type El tipus de missatge (ex: 'error', 'success', 'warning').
 * @param string $message El text del missatge.
 */
function set_flash_message(string $type, string $message): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash_messages'][] = ['type' => $type, 'message' => $message];
}

/**
 * Obté tots els missatges flash i els elimina de la sessió.
 *
 * @return array La llista de missatges.
 */
function get_flash_messages(): array {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    
    return $messages;
}
