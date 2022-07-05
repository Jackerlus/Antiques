<?php
include 'main.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

$mail = new PHPMailer(true);

$mail->IsSMTP();
$mail->SMTPAuth = true;
$mail->SMTPSecure = "tls";
$mail->Host = "smtp.gmail.com";
$mail->Port = 587;
$mail->Username = 'lovejoycoursework@gmail.com';
$mail->Password = 'lovejoycoursework1!';
$mail->From = "lovejoycoursework@gmail.com";
$mail->FromName = "Lovejoy's Antique Evaluation";
$mail->Subject = "Lovejoy's Antiques Verification Code";

$sql = $connect->prepare('SELECT Email FROM SystemUser WHERE ID = ?');
$sql->bind_param('i', $_SESSION['id']);
$sql->execute();
$sql->store_result();
$sql->bind_result($codesendto);
$sql->fetch();
$sql->close();

$mail->addAddress($codesendto); //Recipient name is optional

$mail->isHTML(true);

$twofactorcode = substr(str_shuffle("0123456789"), 0, 6);
$mail->Body = "<p>$twofactorcode</p>";
$mail->AltBody = "<p>$twofactorcode</p>";

$sql = $connect->prepare('UPDATE SystemUser SET 2FACode = ? WHERE ID = ?');
$sql->bind_param('ss', $twofactorcode, $_SESSION['id']);
$sql->execute();
$sql->close();

try {
    $mail->send();
} catch (Exception $e) {
    echo "Error: " . $mail->ErrorInfo;
}
?>

<html lang="en">
    <head>
        <title>Login</title>
        <link href="style.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    <div class="2fa">
        <img src="banner.png" id="banner" />
        <h1>Login</h1>
        <div class="links">
            <a href="index.php" class="active">Login</a>
            <a href="register.html">Register</a>
        </div>
                    <form action="home.php" method="post" id="">
                        <label for="2faemail">Please enter your verification code: </label>
                        <input type="text" name="2faemail" id="2faemail" placeholder="Code" required>
                        <input type="submit" value="Submit">
                    </form>
        </form>
    </div>
    </body>
</html>
