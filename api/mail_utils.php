<?php
// Send password reset email (dummy for localhost)
function send_reset_email($email, $reset_link) {
    // In production, use mail() or an SMTP library
    // For localhost/dev, just display the link (simulate email)
    echo '<div style="background:#fff3cd;padding:1em;border:1px solid #ffeeba;margin:2em 0;">';
    echo "Password reset link for $email: <a href='$reset_link'>$reset_link</a> (valid for 1 hour)";
    echo '</div>';
}
?>
