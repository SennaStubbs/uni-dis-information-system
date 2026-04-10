<?php
    // Prepared statement
    if ($rarity_placeholders != "") {
        $sql = "SELECT Avg(item_sell_value) AS avg_sell_value FROM (SELECT item_sell_value FROM items WHERE item_rarity IN ($rarity_placeholders) LIMIT ? OFFSET ?) items";
    }
    else {
        $sql = "SELECT Avg(item_sell_value) AS avg_sell_value FROM (SELECT item_sell_value FROM items LIMIT ? OFFSET ?) items";
    }
    
    $stmt = $dbconnect -> prepare($sql);
    $stmt -> bind_param($table_paramtypes_limit, ...$table_bindparams_limit);
    $stmt -> execute();
    $result = $stmt -> get_result();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {?>
            <span><?php echo ceil($row['avg_sell_value']); ?> Gold Coins</span>
        <?php }
    }
?>