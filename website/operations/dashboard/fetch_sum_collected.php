<?php
    define('ALLOW_ACCESS', true);

    include($_SERVER["DOCUMENT_ROOT"] . "/information_system/website/inc/dbconnect.php");

    if (isset($_POST['rarities'])) {
        $rarities = explode(',', htmlspecialchars($_POST['rarities']));

        $rarity_placeholders = (count($rarities) > 0 ? '?' : '') . (count($rarities) > 1 ? str_repeat(', ?', count($rarities) - 1) : '');
        

        if (count($rarities) > 0 && $rarities[0] != ''){
            $sql = "SELECT SUM(item_total_times_collected) AS 'sum' FROM (SELECT * FROM items WHERE item_rarity IN ($rarity_placeholders)) items ";
            $stmt = $dbconnect -> prepare($sql);
            $stmt -> bind_param(str_repeat('s', count($rarities)), ...$rarities);
        }
        else {
            $sql = "SELECT SUM(item_total_times_collected) AS 'sum' FROM items";
            $stmt = $dbconnect -> prepare($sql);
        }
        $stmt -> execute();
        $result = $stmt -> get_result();

        echo mysqli_fetch_assoc($result)['sum'];
    }
?>