<?php
require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {    
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        Logger::logAuth('LOGIN_FAILED', $username ?: 'empty', ['reason' => 'Empty credentials']);
        header("Location: ../login?error=empty");
        exit;
    }

    // Rate limiting: 5 attempts per 5 minutes
    $rateLimitKey = 'login_attempts_' . $_SERVER['REMOTE_ADDR'];
    if (!isset($_SESSION[$rateLimitKey])) {
        $_SESSION[$rateLimitKey] = ['count' => 0, 'time' => time()];
    }
    
    $attempts = $_SESSION[$rateLimitKey];
    if (time() - $attempts['time'] > 300) {
        $_SESSION[$rateLimitKey] = ['count' => 0, 'time' => time()];
    } elseif ($attempts['count'] >= 5) {
        Logger::logAuth('LOGIN_BLOCKED', $username, ['reason' => 'Rate limit exceeded']);
        header("Location: ../login?error=rate_limit");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            unset($_SESSION[$rateLimitKey]);
            
            session_regenerate_id(true); 
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['login_time'] = time();

            Logger::logAuth('LOGIN_SUCCESS', $username, [
                'user_id' => $user['id'],
                'role' => $user['role']
            ]);

            header("Location: ../home");
            exit;
        } else {
            $_SESSION[$rateLimitKey]['count']++;
            
            Logger::logAuth('LOGIN_FAILED', $username, ['reason' => 'Invalid credentials']);
            header("Location: ../login?error=invalid");
            exit;
        }

    } catch (PDOException $e) {
        Logger::error("Login database error", ['username' => $username, 'error' => $e->getMessage()]);
        header("Location: ../login?error=db");
        exit;
    }
} else {
    header("Location: ../login");
    exit;
}
?>