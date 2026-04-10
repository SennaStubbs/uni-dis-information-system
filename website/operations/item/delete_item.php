<?php
    define('ALLOW_ACCESS', true);

    include ($_SERVER['DOCUMENT_ROOT'] . "/chris_blue/inc/dbconnect.php");
    include ($_SERVER['DOCUMENT_ROOT'] . "/chris_blue/operations/user/get_user.php");

    // Need a user account with the correct access level to view
    if($user_access_level != -1) {
        header('location: /chris_blue/index.php');
        exit();
    }

    if (isset($_POST['item_id'])) {
        $item_id = filter_var($_POST['item_id'], FILTER_SANITIZE_NUMBER_INT);
    }

    if ($item_id) {
        $sql = "DELETE FROM items WHERE item_id = ?";
        $stmt = $dbconnect -> prepare($sql);
        $stmt -> bind_param('i', $item_id);
        $stmt -> execute();
        $result = $stmt -> get_result();
    }
    else {
        echo 'error';
    }
?>