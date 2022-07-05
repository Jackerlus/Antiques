<?php
include 'main.php';
$sql = $connect->prepare('SELECT SystemUser.ID, SystemUser.Forename, SystemUser.Surname, SystemUser.Email, SystemUser.Username,
       SystemUser.Phone, Requests.ContactPref, Requests.ID, Requests.Title, Requests.Comments, Requests.FileName, Requests.SubmitDate FROM SystemUser
       INNER JOIN Requests ON SystemUser.ID = Requests.UserID');
$sql->execute();
$sql->store_result();
$sql->bind_result($userID, $forename, $surname, $email, $username, $phone, $contactpref, $requestID, $title, $comments, $filename, $date);
?>
<head>
    <title>Admin Page</title>
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
<h2>Evaluation Listings</h2>
        <table id="listings">
            <thead>
            <tr>
                <td class="listing-column-title">Listing ID</td>
                <td class="listing-column-title">Forename</td>
                <td class="listing-column-title">Surname</td>
                <td class="listing-column-title">Email</td>
                <td class="listing-column-title">Phone</td>
                <td class="listing-column-title">Contact Preference</td>
                <td class="listing-column-title">Title</td>
                <td class="listing-column-title">Comments</td>
                <td class="listing-column-title">Date Submitted</td>
                <td class="listing-column-title">Image</td>
            </tr>
            </thead>
            <tbody>
            <?php if ($sql->num_rows == 0): ?>
                <tr>
                    <td colspan="8" style="text-align:center;">No evaluation requests found.</td>
                </tr>
            <?php else: ?>
                <?php while ($sql->fetch()): ?>
                    <tr>
                        <td><?= sanitiseOutput($requestID) ?></td>
                        <td><?= sanitiseOutput($forename); ?></td>
                        <td><?= sanitiseOutput($surname); ?></td>
                        <td><?= sanitiseOutput($email); ?></td>
                        <td><?= sanitiseOutput($phone); ?></td>
                        <td><?= sanitiseOutput($contactpref); ?></td>
                        <td><?= sanitiseOutput($title); ?></td>
                        <td><?= sanitiseOutput($comments); ?></td>
                        <td><?= sanitiseOutput($date) ?></td>
                        <td><img src="uploads/<?= $filename ?>" alt="Evaluation request listing image" class="eval-image"</td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
</body>
<?php
$sql->close();
?>