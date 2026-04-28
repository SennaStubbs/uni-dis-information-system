<?php
    define('ALLOW_ACCESS', true);

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    include ($_SERVER['DOCUMENT_ROOT'] . "/information_system/website/inc/dbconnect.php");
    include ($_SERVER['DOCUMENT_ROOT'] . "/information_system/website/operations/user/get_user.php");

    // Need a user account with the correct access level to view
    if($user_access_level != -1) {
        echo 'cannot perform';
        exit();
    }

    if (isset($_POST['user_id'])) {
        $_user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT);
        // Cannot change the primary admin account
        if ($_user_id == 1) {
            echo 'cannot perform';
            exit();
        }
        
        $stmt = "DELETE FROM users WHERE user_id = ?";
        $sql = $dbconnect -> prepare($stmt);
        $sql -> bind_param('i', $_user_id);
        $sql -> execute();
        $result = $sql -> get_result();

        echo 'success';
    }
    else {
        echo 'error';
    }
?>