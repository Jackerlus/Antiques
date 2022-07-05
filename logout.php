<?php
session_start();
session_destroy();
if (isset($_COOKIE['remember'])) {
    unset($_COOKIE['remember']);
    setcookie('remember', '', time() - 3600);
}
// Redirect to the login page:
header('Location: index.php');
?>
