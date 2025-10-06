<?php
// forgot_password.php — Request password reset form
session_start();
require_once 'api/db.php';
$error = $msg = '';
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time()+3600);
        $pdo->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)')->execute([$user['id'],$token,$expires]);
        $reset_link = (isset($_SERVER['HTTPS'])?'https':'http'). '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";
        require_once 'api/mail_utils.php';
        send_reset_email($email, $reset_link);
        $msg = 'If your email is registered, a reset link has been sent.';
    } else {
        $msg = 'If your email is registered, a reset link has been sent.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - FlimyHeaven</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="container" style="max-width:440px;margin:3em auto;padding:2em;background:#f6fafd;border-radius:14px;box-shadow:0 2px 12px #124e6617;">
        <h2 style="color:#124E66;">Forgot Password?</h2>
        <a href="index.php" style="display:inline-block;transition:all 0.2s;">
        <span class="logo" style="color:#124E66;transition:all 0.2s;">Home</span>
        </a>
        <form method="post" style="margin-top:2em;">
            <label>Email Address</label><br>
            <input type="email" name="email" required style="width:100%;padding:0.7em;margin:0.6em 0 1em 0;border-radius:6px;border:1px solid #b7c6d0;">
            <button type="submit" class="btn" style="width:100%;background:#124E66;color:#fff;">Send Reset Link</button>
        </form>
        <?php if($msg): ?><p style="margin-top:1.5em;color:#124E66;background:#eaf2fa;padding:0.9em 1em;border-radius:7px;"> <?= $msg ?> </p><?php endif; ?>
    </div>
</body>
</html>
