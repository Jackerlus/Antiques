<?php
include 'main.php';

if (!isset($_POST['token']) || $_POST['token'] != $_SESSION['token']) {
    exit('Error: invalid token.');
}

$logins = checkLogins($connect, FALSE);

if ($logins && $logins['AttemptsLeft'] <= 0) {
    exit('Login temporarily disabled - too many failed attempts. Login will be re-enabled 12 hours from the point it was disabled.');
}

if (!isset($_POST['username'], $_POST['password'])) {
    $logins = checkLogins($connect);
    exit('Error: please fill both required fields.');
}

$sql = $connect->prepare('SELECT ID, Password, Remember, Permissions FROM SystemUser WHERE Username = ?');
$sql->bind_param('s', $_POST['username']);
$sql->execute();
$sql->store_result();
if ($sql->num_rows > 0) {
    $sql->bind_result($id, $password, $remember, $permissions);
    $sql->fetch();
    $sql->close();
    if (password_verify($_POST['password'], $password)) {
            session_regenerate_id();
            $_SESSION['active'] = TRUE;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $id;
            $_SESSION['permissions'] = $permissions;
            if (isset($_POST['remember'])) {
                $cookieh = !empty($remember) ? $remember : password_hash($id . $_POST['username'] . 's3cr3tk3y', PASSWORD_DEFAULT);
                setcookie('remember', $cookieh, (int)(time() + 60 * 60 * 24 * 30));
                $sql = $connect->prepare('UPDATE SystemUser SET Remember = ? WHERE ID = ?');
                $sql->bind_param('si', $cookieh, $id);
                $sql->execute();
                $sql->close();
            }
            echo 'Success';
    } else {
        $login_attempts = checkLogins($connect, TRUE);
        echo 'Error: incorrect username or password. ' . $login_attempts['AttemptsLeft'] . ' attempts remaining.';
    }
} else {
    echo 'Error: incorrect username or password.';
}

