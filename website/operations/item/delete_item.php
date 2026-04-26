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

    if (isset($_POST['item_id'])) {
        $item_id = filter_var($_POST['item_id'], FILTER_SANITIZE_NUMBER_INT);
        
        $sql = "DELETE FROM items WHERE item_id = ?";
        $stmt = $dbconnect -> prepare($sql);
        $stmt -> bind_param('i', $item_id);
        $stmt -> execute();
        $result = $stmt -> get_result();

        echo 'success';
    }
    else {
        echo 'error';
    }
?>