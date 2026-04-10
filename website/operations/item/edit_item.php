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
    if (isset($_POST['item_name'])) {
        $item_name = htmlspecialchars($_POST['item_name'], ENT_QUOTES, 'UTF-8');
    }
    if (isset($_POST['item_rarity'])) {
        $item_rarity = htmlspecialchars($_POST['item_rarity'], ENT_QUOTES, 'UTF-8');

        $rarityOrder = ['Common', 'Uncommon', 'Rare', 'Epic', 'Legendary', 'Mythic', 'Exotic', 'Unreal'];
        if (!in_array($item_rarity, $rarityOrder)) {
            $item_rarity = false;
        }
    }
    if (isset($_POST['item_sell_value'])) {
        $item_sell_value = filter_var($_POST['item_sell_value'], FILTER_SANITIZE_NUMBER_INT);
    }
    if (isset($_POST['item_total_times_collected'])) {
        $item_total_times_collected = filter_var($_POST['item_total_times_collected'], FILTER_SANITIZE_NUMBER_INT);
    }
    if (isset($_POST['item_total_times_sold'])) {
        $item_total_times_sold = filter_var($_POST['item_total_times_sold'], FILTER_SANITIZE_NUMBER_INT);
    }

    if ($item_id && $item_name && $item_rarity && $item_sell_value) {
        $sql = "UPDATE items SET item_name = ?, item_rarity = ?, item_sell_value = ?, item_total_times_collected = ?, item_total_times_sold = ? WHERE item_id = ?";
        $stmt = $dbconnect -> prepare($sql);
        $stmt -> bind_param('ssiiii', $item_name, $item_rarity, $item_sell_value, $item_total_times_collected, $item_total_times_sold, $item_id);
        $stmt -> execute();
        $result = $stmt -> get_result();
    }
    else {
        echo 'error';
    }
?>