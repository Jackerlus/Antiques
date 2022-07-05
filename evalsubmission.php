<?php
include 'main.php';

checkActive($connect);

$sql = $connect->prepare("INSERT INTO Requests (Title, Comments, UserID, FileName, ContactPref, SubmitDate) VALUES (?, ?, ?, ?, ?, CURDATE())");
$filetypes = ['jpeg', 'png', 'jpg'];
$dir = 'uploads/';
$filename = basename($_FILES['image']['name']);
$savepath = $dir . $filename;
$filetype = pathinfo($savepath, PATHINFO_EXTENSION);
$date = date("Y-m-d");
if ($_POST['contactpref'] == "Phone") {
    $contactpref = "Phone";
} else {
    $contactpref = "Email";
}

if (isset($_FILES['image'])) {
    echo "Form submitted successfully. ";
    if (in_array($filetype, $filetypes)) {
        echo "File type valid. ";
        if (move_uploaded_file($_FILES['image']['tmp_name'], $savepath)) {
            echo "File uploaded successfully. ";
            echo $_SESSION['id'] . ". ";
            echo $filename . " ";
            $sql->bind_param('ssis',$_POST['title'], $_POST['comments'], $_SESSION['id'], $filename, $contactpref);
            echo $_POST['title'];
            $sql->execute();
            $sql->close();
        } else {
            echo "File transfer error. ";
        }
    } else {
        echo "Invalid file type error. ";
    }
} else {
    echo "Form submission error. ";
}


