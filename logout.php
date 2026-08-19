
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// DESTROY CURRENT SESSION

$_SESSION = [];

// Remove session cookie if it exists
if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session
session_destroy();

// REDIRECT TO LOGIN
header("Location: login.php");
exit();

?>
