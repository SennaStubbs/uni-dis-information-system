<?php
    // Cannot be directly accessed
    if (!defined('ALLOW_ACCESS')) {
        exit('No direct script access allowed');
    }

    include('../chris_blue/inc/dbconnect.php');

    if(isset($_COOKIE['user'])) {
        $username = htmlspecialchars($_COOKIE['user'], ENT_QUOTES, 'UTF-8');

        $user_sql = "SELECT * FROM users WHERE user_username = ?";
        $user_stmt = $dbconnect->prepare($user_sql);
        $user_stmt->bind_param('s', $username);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        if (mysqli_num_rows($user_result) > 0) {
            while($row = mysqli_fetch_assoc($user_result)) {
                $user_display_name = $row['user_display_name'];
                $user_access_level = $row['user_access_level'];
            }
        }
    }
?>