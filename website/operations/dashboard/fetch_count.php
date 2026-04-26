<?php
    define('ALLOW_ACCESS', true);

    include("../../inc/dbconnect.php");

    if (isset($_POST['rarities'])) {
        $rarities = explode(',', htmlspecialchars($_POST['rarities']));

        $rarity_placeholders = (count($rarities) > 0 ? '?' : '') . (count($rarities) > 1 ? str_repeat(', ?', count($rarities) - 1) : '');
        

        if (count($rarities) > 0 && $rarities[0] != ''){
            $sql = "SELECT COUNT(*) AS count FROM (SELECT * FROM items WHERE item_rarity IN ($rarity_placeholders)) items ";
            $stmt = $dbconnect -> prepare($sql);
            $stmt -> bind_param(str_repeat('s', count($rarities)), ...$rarities);
        }
        else {
            $sql = "SELECT COUNT(*) AS count FROM items";
            $stmt = $dbconnect -> prepare($sql);
        }
        $stmt -> execute();
        $result = $stmt -> get_result();

        echo mysqli_fetch_assoc($result)['count'];
    }
?>