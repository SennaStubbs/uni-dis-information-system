<?php
    define('ALLOW_ACCESS', false);

    include("../../inc/dbconnect.php");

    if (isset($_POST['item_name'])) {
        $item_name = htmlspecialchars($_POST['item_name'], ENT_QUOTES, 'UTF-8');
    }
    if (isset($_POST['item_description'])) {
        $item_description = htmlspecialchars($_POST['item_description'], ENT_QUOTES, 'UTF-8');
    }
    else {
        $item_description = '';
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

    // echo $item_name, $item_description, $item_rarity, $item_sell_value, $item_total_times_collected, $item_total_times_sold;

    if ($item_name && $item_rarity && $item_sell_value && $item_total_times_collected && $item_total_times_sold) {
        $sql = "INSERT INTO items (item_name, item_description, item_rarity, item_sell_value, item_total_times_collected, item_total_times_sold)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $dbconnect -> prepare($sql);
        $stmt -> bind_param('sssiii', $item_name, $item_description, $item_rarity, $item_sell_value, $item_total_times_collected, $item_total_times_sold);
        $stmt -> execute();
        $result = $stmt -> get_result();
    }
    else {
        // echo 'error';
    }

    
    header('location: /chris_blue/items_table.php');
?>