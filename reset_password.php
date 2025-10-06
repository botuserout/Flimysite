<?php
// reset_password.php — Set new password using token
session_start();
require_once 'api/db.php';
$error = $msg = '';
$token = $_GET['token'] ?? '';
if (!$token) { $error = 'Invalid link.'; }
else {
    $stmt = $pdo->prepare('SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()');
    $stmt->execute([$token]);
    $row = $stmt->fetch();
    if (!$row) { $error = 'Reset link expired or invalid.'; }
    else if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['password'])) {
        $password = $_POST['password'];
        if (strlen($password)<6) $error = 'Password must be at least 6 characters.';
        else {
            $hash = password_hash($password,PASSWORD_DEFAULT);
            $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')->execute([$hash, $row['user_id']]);
            $pdo->prepare('DELETE FROM password_resets WHERE user_id = ?')->execute([$row['user_id']]);
            $msg = 'Password reset successful! <a href="login.php">Login</a>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - FlimyHeaven</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="container" style="max-width:440px;margin:3em auto;padding:2em;background:#f6fafd;border-radius:14px;box-shadow:0 2px 12px #124e6617;">
        <h2 style="color:#124E66;">Reset Password</h2>
        <?php if($error): ?><p style="color:#c00;"> <?= $error ?> </p><?php endif; ?>
        <?php if($msg): ?><p style="color:green;"> <?= $msg ?> </p><?php else: ?>
        <form method="post" style="margin-top:2em;">
            <label>New Password</label><br>
            <input type="password" name="password" required style="width:100%;padding:0.7em;margin:0.6em 0 1em 0;border-radius:6px;border:1px solid #b7c6d0;">
            <button type="submit" class="btn" style="width:100%;background:#124E66;color:#fff;">Set Password</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
