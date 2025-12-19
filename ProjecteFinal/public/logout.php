<?php
// Tanca la sessió.
session_start();
session_destroy();

// Redirecció a l'inici.
header("Location: ../index.php");
exit();
?>