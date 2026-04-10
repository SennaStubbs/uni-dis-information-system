<?php

    define('ALLOW_ACCESS', true);


    include ($_SERVER['DOCUMENT_ROOT'] . "/chris_blue/inc/dbconnect.php");

    if (isset($_POST['rarities'])) {
        $rarities = explode(',', htmlspecialchars($_POST['rarities'], ENT_QUOTES, "UTF-8"));

        $rarity_placeholders = (count($rarities) > 0 ? '?' : '') . (count($rarities) > 1 ? str_repeat(', ?', count($rarities) - 1) : '');
        

        if (count($rarities) > 0 && $rarities[0] != ''){
            $sql = "SELECT Avg(item_sell_value) AS avg_sell_value FROM (SELECT item_sell_value FROM items WHERE item_rarity IN ($rarity_placeholders)) items ";
            $stmt = $dbconnect -> prepare($sql);
            $stmt -> bind_param(str_repeat('s', count($rarities)), ...$rarities);
        }
        else {
            $sql = "SELECT Avg(item_sell_value) AS avg_sell_value FROM items";
            $stmt = $dbconnect -> prepare($sql);
        }
        $stmt -> execute();
        $result = $stmt -> get_result();

        echo ceil(mysqli_fetch_assoc($result)['avg_sell_value']);
    }
?>