<?php
    define('ALLOW_ACCESS', true);

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    include ($_SERVER["DOCUMENT_ROOT"] . "/information_system/website/inc/dbconnect.php");
    include ($_SERVER['DOCUMENT_ROOT'] . "/information_system/website/operations/user/get_user.php");

    // Need a user account with the correct access level to view
    if($user_access_level != -1) {
        echo 'cannot perform';
        exit();
    }

    if (isset($_POST['user_username']) && isset($_POST['user_password']) && isset($_POST['user_display_name']) && isset($_POST['user_access_level'])) {
        $_user_username = htmlspecialchars($_POST['user_username'], ENT_QUOTES, 'UTF-8');
        $_user_password = htmlspecialchars($_POST['user_password'], ENT_QUOTES, 'UTF-8');
        $_user_display_name = htmlspecialchars($_POST['user_display_name'], ENT_QUOTES, 'UTF-8');
        $_user_access_level = filter_var((int)$_POST['user_access_level'], FILTER_SANITIZE_NUMBER_INT);
    }
    else {
        echo 'error';
        exit();
    }

    if (trim($_user_username) != '' && trim($_user_display_name) != '' && $_user_access_level !== false) {
        // Check if username already exists
        $stmt = "SELECT user_id FROM users WHERE user_username = ?";
        $sql = $dbconnect -> prepare($stmt);
        $sql -> bind_param('s', $_user_username);
        $sql -> execute();
        $result = $sql -> get_result();
        if (mysqli_num_rows($result) > 0) {
            echo 'duplicate username';
            exit();
        }

        // Add user
        $stmt = "INSERT INTO users (user_username, user_password, user_display_name, user_access_level)
                VALUES (?, ?, ?, ?)";
        $sql = $dbconnect -> prepare($stmt);
        $sql -> bind_param('sssi', $_user_username, $_user_password, $_user_display_name, $_user_access_level);
        $sql -> execute();
        $result = $sql -> get_result();

        echo 'success';
    }
    else {
        echo 'error';
    }
?>