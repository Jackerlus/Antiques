<?php
include 'main.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

// Output message
$msg = '';
if (isset($_POST['email'])) {
    $sql = $connect->prepare('SELECT * FROM SystemUser WHERE Email = ?');
    $sql->bind_param('s', $_POST['email']);
    $sql->execute();
    $sql->store_result();
    if ($sql->num_rows > 0) {
        $sql->close();
        $resetid = uniqid();
        $sql = $connect->prepare('UPDATE SystemUser SET ResetID = ? WHERE Email = ?');
        $sql->bind_param('ss', $resetid, $_POST['email']);
        $sql->execute();
        $sql->close();

        $mail = new PHPMailer(true);

        $resetlink = 'http://users.sussex.ac.uk/~jstl20/G6077/Coursework/resetpassword.php?email=' . $_POST['email'] . '&code=' . $resetid;

        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls";
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 587;
        $mail->Username = 'lovejoycoursework@gmail.com';
        $mail->Password = 'lovejoycoursework1!';
        $mail->From = "lovejoycoursework@gmail.com";
        $mail->FromName = "Lovejoy's Antique Evaluation";
        $mail->Subject = "Lovejoy's Antiques Password Reset";
        $mail->Body = "<p>Please click the following link to reset your password: <a href=\"" . $resetlink . "\">" . $resetlink . "</a></p>";

        $sql = $connect->prepare('SELECT Email FROM SystemUser WHERE ID = ?');
        $sql->bind_param('i', $_SESSION['id']);
        $sql->execute();
        $sql->store_result();
        $sql->bind_result($codesendto);
        $sql->fetch();
        $sql->close();

        $mail->addAddress($_POST['email']); //Recipient name is optional

        $mail->isHTML(true);

        try {
            $mail->send();
        } catch (Exception $e) {
            echo "Error: " . $mail->ErrorInfo;
        }
        $msg = 'Reset password link has been sent to your email address.';
    } else {
        $msg = 'An account with this email address doesn\'t exist.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Forgot Password</title>
        <link href="style.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    <div class="login">
        <h1>Forgot Password</h1>
        <form action="forgotpassword.php" method="post">
            <label for="email">
                Enter email address:
            </label>
            <input type="email" name="email" placeholder="Your Email" id="email" required>
            <div class="msg"><?=$msg?></div>
            <input type="submit" value="Submit">
        </form>
    </div>
    </body>
</html>
