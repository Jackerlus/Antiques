<?php
include 'main.php';
include 'securimage/securimage.php';

$securimage = new Securimage();

if (mysqli_connect_errno()) {
    exit('Database Connection Error: ' . mysqli_connect_error());
}
if (!isset($_POST['username'], $_POST['password'], $_POST['cpassword'], $_POST['email'])) {
    exit('Please finish the registration process.');
}
if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
    exit('Please finish the registration process.');
}
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    exit('Invalid email address.');
}
if (!preg_match('/^[a-zA-Z0-9]+$/', $_POST['username'])) {
    exit('Invalid username.');
}
if (preg_match("^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d$@$!%*?&]{8,}", $_POST['password'])) {
    exit('Password must be minimum 8 characters in length with at least one number, one lowercase letter, and one uppercase letter.');
}
if ($_POST['cpassword'] != $_POST['password']) {
    exit('Passwords do not match!');
}
if ($securimage->check($_POST['captcha_code']) == false) {
  exit("The CAPTCHA code entered was incorrect. Please try again.");
}



$sql = $connect->prepare('SELECT ID, Password FROM SystemUser WHERE Username = ? OR Email = ?');
$sql->bind_param('ss', $_POST['username'], $_POST['email']);
$sql->execute();
$sql->store_result();
if ($sql->num_rows > 0) {
    echo 'Username or email already exists.';
} else {
    $sql->close();
    $sql = $connect->prepare("INSERT INTO SystemUser (Forename, Surname, Username, Password, Email, Phone) VALUES (?, ?, ?, ?, ?, ?)");
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $sql->bind_param('ssssss',$_POST['forename'], $_POST['surname'], $_POST['username'], $password, $_POST['email'], $_POST['phone']);
    $sql->execute();
    $sql->close();
    echo "<br>You can now proceed to <a href='index.php'>log in</a>.";
}
