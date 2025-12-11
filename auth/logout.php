<?php
require_once '../config.php';

$username = $_SESSION['username'] ?? 'unknown';
$userId = $_SESSION['user_id'] ?? null;

Logger::logAuth('LOGOUT', $username, ['user_id' => $userId]);

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

header("Location: ../login");
exit;
?>