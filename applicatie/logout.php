<?php
session_start();

// Vernietig alle sessievariabelen
$_SESSION = [];

// Verwijder de sessiecookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Vernietig de sessie
session_destroy();

// Redirect naar de inlogpagina of een andere gewenste pagina
header('Location: inloggen.php');
exit();
?>