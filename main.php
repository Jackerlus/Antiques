<?php
include_once 'config.php';
//error_reporting(0);
session_start();
$connect = mysqli_connect(db_host, db_user, db_pass, db_name);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
mysqli_set_charset($connect, db_charset);

function checkActive($connect, $redirect = 'index.php')
{
    if (isset($_COOKIE['remember']) && !empty($_COOKIE['remember']) && !isset($_SESSION['active'])) {
        $sql = $connect->prepare('SELECT ID, Username, Permissions FROM SystemUser WHERE Remember = ?');
        $sql->bind_param('s', $_COOKIE['remember']);
        $sql->execute();
        $sql->store_result();
        if ($sql->num_rows > 0) {
            $sql->bind_result($id, $username, $permissions);
            $sql->fetch();
            $sql->close();
            session_regenerate_id();
            $_SESSION['active'] = TRUE;
            $_SESSION['id'] = $id;
            $_SESSION['name'] = $username;
            $_SESSION['permissions'] = $permissions;
        } else {
            header('Location: ' . $redirect);
            exit;
        }
    } else if (!isset($_SESSION['active'])) {
        // If the user is not logged in redirect to the login page.
        header('Location: ' . $redirect);
        exit;
    }
}

function checkLogins($connect, $refresh = TRUE) {
    $time = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    if ($refresh) {
        $sql = $connect->prepare('INSERT INTO Logins (IPAddress, `Date`) VALUES (?, ?) ON DUPLICATE KEY UPDATE AttemptsLeft = AttemptsLeft - 1, `Date` = VALUES(`Date`)');
        $sql->bind_param('ss', $ip, $time);
        $sql->execute();
        $sql->close();
    }
    $sql = $connect->prepare('SELECT * FROM Logins WHERE IPAddress = ?');
    $sql->bind_param('s', $ip);
    $sql->execute();
    $logins = bindAll($sql);
    $sql->close();
    if ($logins) {
        $expiry = date('Y-m-d H:i:s', strtotime('+12 hours', strtotime($logins['Date'])));
        if ($time > $expiry) {
            $sql = $connect->prepare('DELETE FROM Logins WHERE IPAddress = ?');
            $sql->bind_param('s', $ip);
            $sql->execute();
            $sql->close();
            $logins = array();
        }
    }
    return $logins;
}

function sanitiseOutput($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function bindAll($stmt) {
    $meta = $stmt->result_metadata();
    $fields = array();
    $fieldRefs = array();
    while ($field = $meta->fetch_field())
    {
        $fields[$field->name] = "";
        $fieldRefs[] = &$fields[$field->name];
    }

    call_user_func_array(array($stmt, 'bind_result'), $fieldRefs);
    $stmt->store_result();
    //var_dump($fields);
    return $fields;
}

function fetchRowAssoc($stmt, &$fields) {
    if ($stmt->fetch()) {
        return $fields;
    }
    return false;
}
