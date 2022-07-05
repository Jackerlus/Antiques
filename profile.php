<?php
include 'main.php';
checkActive($connect);
$msg = '';
$statement = $connect->prepare('SELECT Password, Email, Permissions FROM SystemUser WHERE ID = ?');
$statement->bind_param('i', $_SESSION['id']);
$statement->execute();
$statement->bind_result($password, $email, $permissions);
$statement->fetch();
$statement->close();
if (isset($_POST['username'], $_POST['password'], $_POST['cpassword'], $_POST['email'])) {
    if (empty($_POST['username']) || empty($_POST['email'])) {
        $msg = 'The input fields must not be empty!';
    } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $msg = 'Please provide a valid email address!';
    } else if (!preg_match('/^[a-zA-Z0-9]+$/', $_POST['username'])) {
        $msg = 'Username must contain only letters and numbers!';
    } else if (!empty($_POST['password']) && (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5)) {
        $msg = 'Password must be between 5 and 20 characters long!';
    } else if ($_POST['cpassword'] != $_POST['password']) {
        $msg = 'Passwords do not match!';
    }
    if (empty($msg)) {
        $statement = $connect->prepare('SELECT * FROM SystemUser WHERE (Username = ? OR Email = ?) AND Username != ? AND Email != ?');
        $statement->bind_param('ssss', $_POST['username'], $_POST['email'], $_SESSION['name'], $email);
        $statement->execute();
        $statement->store_result();
        if ($statement->num_rows > 0) {
            $msg = 'Account already exists with that username and/or email!';
        } else {
            $statement->close();
            $statement = $connect->prepare('UPDATE SystemUser SET Username = ?, Password = ?, Email = ? WHERE ID = ?');
            $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $password;
            $statement->bind_param('sssi', $_POST['username'], $password, $_POST['email'], $_SESSION['id']);
            $statement->execute();
            $statement->close();
            // Update the session variables
            $_SESSION['name'] = $_POST['username'];
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Profile</title>
        <link href="style.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    <img class="banner" src="banner.png" alt="Banner showing antique sign"/>
    <nav>
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
        <div class>
            <h2>Profile Page</h2>
            <div class="block">
                <p>Your account details are below:</p>
                <table>
                    <tr>
                        <td>Username:</td>
                        <td><?= sanitiseOutput($_SESSION['name']) ?></td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td><?= sanitiseOutput($email) ?></td>
                    </tr>
                    <tr>
                        <td>Permissions:</td>
                        <td><?= sanitiseOutput($permissions) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>
