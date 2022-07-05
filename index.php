<?php
include 'main.php';
if (isset($_SESSION['active'])) {
    header('Location: home.php');
    exit;
}
if (isset($_COOKIE['remember']) && !empty($_COOKIE['remember'])) {
    $sql = $connect->prepare('SELECT ID, Username, Permissions FROM SystemUser WHERE Remember = ?');
    $sql->bind_param('s', $_COOKIE['remember']);
    $sql->execute();
    $sql->store_result();
    if ($sql->num_rows > 0) {
        // Found a match
        $sql->bind_result($id, $username, $permissions);
        $sql->fetch();
        $sql->close();
        session_regenerate_id();
        $_SESSION['active'] = TRUE;
        $_SESSION['name'] = $username;
        $_SESSION['id'] = $id;
        $_SESSION['permissions'] = $permissions;
        header('Location: home.php');
        exit;
    }
}

$_SESSION['token'] = md5(uniqid(rand(), true));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="login">
    <img src="banner.png" id="banner" />
    <h1>Login</h1>
    <div class="links">
        <a href="index.php" class="active">Login</a>
        <a href="register.html">Register</a>
    </div>
    <form action="logincheck.php" method="post">
        <input type="text" name="username" placeholder="Username" id="username" required>
        <br />
        <input type="password" name="password" placeholder="Password" id="password" required>
        <br />
        <input type="checkbox" name="remember">Remember me
        <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
        <div class="msg"></div>
        <input type="submit" value="Login">
        <a href="forgotpassword.php">Reset password</a>
    </form>
</div>
<script>
    document.querySelector(".login form").onsubmit = function (event) {
        event.preventDefault();
        var form_data = new FormData(document.querySelector(".login form"));
        var xhr = new XMLHttpRequest();
        xhr.open("POST", document.querySelector(".login form").action, true);
        xhr.onload = function () {
            if (this.responseText.toLowerCase().indexOf("success") !== -1) {
                window.location.href = "2faconfirm.php";
            } else {
                document.querySelector(".msg").innerHTML = this.responseText;
            }
        };
        xhr.send(form_data);
    };
</script>
</body>
</html>
