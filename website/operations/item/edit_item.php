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

    if (isset($_POST['item_id']) && isset($_POST['item_name']) && isset($_POST['item_rarity']) && isset($_POST['item_sell_value']) && isset($_POST['item_total_times_collected']) && isset($_POST['item_total_times_sold'])) {
        $item_id = filter_var($_POST['item_id'], FILTER_SANITIZE_NUMBER_INT);
        $item_name = htmlspecialchars($_POST['item_name'], ENT_QUOTES, 'UTF-8');

        $item_rarity = htmlspecialchars($_POST['item_rarity'], ENT_QUOTES, 'UTF-8');
        $rarityOrder = ['Common', 'Uncommon', 'Rare', 'Epic', 'Legendary', 'Mythic', 'Exotic', 'Unreal'];
        if (!in_array($item_rarity, $rarityOrder)) {
            $item_rarity = false;
        }

        $item_sell_value = filter_var($_POST['item_sell_value'], FILTER_SANITIZE_NUMBER_INT);
        $item_total_times_collected = filter_var($_POST['item_total_times_collected'], FILTER_SANITIZE_NUMBER_INT);
        $item_total_times_sold = filter_var($_POST['item_total_times_sold'], FILTER_SANITIZE_NUMBER_INT);
    }

    if ($item_id && $item_name && $item_rarity && $item_sell_value && $item_total_times_collected && $item_total_times_sold) {
        $sql = "UPDATE items SET item_name = ?, item_rarity = ?, item_sell_value = ?, item_total_times_collected = ?, item_total_times_sold = ? WHERE item_id = ?";
        $stmt = $dbconnect -> prepare($sql);
        $stmt -> bind_param('ssiiii', $item_name, $item_rarity, $item_sell_value, $item_total_times_collected, $item_total_times_sold, $item_id);
        $stmt -> execute();
        $result = $stmt -> get_result();

        echo 'success';
    }
    else {
        echo 'error';
    }
?>