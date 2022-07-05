<?php
include 'main.php';
checkActive($connect);

if (isset($_POST['2faemail'])) {
    $sql = $connect->prepare('SELECT 2FACode FROM SystemUser WHERE ID = ?');
    $sql->bind_param('i', $_SESSION['id']);
    $sql->execute();
    $sql->store_result();
    $sql->bind_result($code);
    $sql->fetch();
    $sql->close();
    if (!($_POST['2faemail'] == $code)) {
        session_destroy();
        if (isset($_COOKIE['remember'])) {
            unset($_COOKIE['remember']);
            setcookie('remember', '', time() - 3600);
        }
        exit('Error: incorrect verification code. Login denied. ');
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Home Page</title>
        <link href="style.css" rel="stylesheet" type="text/css">
    </head>
    <body class="home">
        <img class=banner src="banner.png" />
        <nav class="navtop">
            <div>
                <h1>Lovejoy's Antique Evaluation</h1>
                <a href="home.php">Home</a>
                <a href="profile.php">Profile</a>
                <?php if ($_SESSION['permissions'] == 'admin'): ?>
                    <a href="admin.php" target="_blank">Admin</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            </div>
        </nav>
        <h3>Evaluate Your Antique</h3>
        <form action="evalsubmission.php" id="evalsubmission" method="post" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Title" id="title" required>
            <br/>
            <input type="text" name="comments" placeholder="Comments" id="comments" required>
            <br/>
            <div>
                <label>Choose a contact preference: </label>
                <select form="evalsubmission" name="contactpref" id="contactpref">
                    <option value="Phone">Phone</option>
                    <option value="Email">Email</option>
                </select>
            </div>
            <br/>
            <input type="file" name="image" id="image" required>
            <br/>
            <input type="submit" id="submit" value="Submit">
            <div class="msg"></div>
        </form>
    </body>
</html>
