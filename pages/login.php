<?php

$error_message = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'empty':
            $error_message = 'Username and password cannot be empty.';
            break;
        case 'invalid':
            $error_message = 'Invalid username or password.';
            break;
        case 'db':
            $error_message = 'There was a problem with the database. Please try again.';
            break;
        case 'rate_limit':
            $error_message = 'Too many login attempts. Please try again in 5 minutes.';
            break;
        default:
            $error_message = 'An error occurred. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <title>スマート・プレゼンテーション - ログイン</title>
    <link rel="icon" type="image/x-icon" href="assets/SR_logo_03_red.png">
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/login.css">
</head>
<body class="login-page">

    <div class="login-container">
        <h1>スマート・プレゼンテーション</h1>
        <p>Hokkaido Showroom</p>

        <form class="login-form" action="auth/login_process.php" method="POST">
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            
            <button type="submit" class="login-button">Login</button>
        
        </form>
        
        <?php
        if (!empty($error_message)) {
            echo '<div class="login-error">' . htmlspecialchars($error_message) . '</div>';
        }
        ?>
        
    </div>

</body>
</html>