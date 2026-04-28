<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
        define('ALLOW_ACCESS', true);

    include($_SERVER["DOCUMENT_ROOT"] . "/information_system/website/inc/dbconnect.php");

    if (isset($_POST['rarities']) || isset($rarities)) {
        if (!isset($rarities))
            $rarities = explode(',', htmlspecialchars($_POST['rarities']));

        $rarity_placeholders = (count($rarities) > 0 ? '?' : '') . (count($rarities) > 1 ? str_repeat(', ?', count($rarities) - 1) : '');
        

        if (count($rarities) > 0 && $rarities[0] != ''){
            $stmt = "SELECT * FROM (SELECT * FROM items WHERE item_rarity IN ($rarity_placeholders)) items ORDER BY item_sell_value DESC LIMIT 10";
            $sql = $dbconnect -> prepare($stmt);
            $sql -> bind_param(str_repeat('s', count($rarities)), ...$rarities);
        }
        else {
            $stmt = "SELECT * FROM items ORDER BY item_sell_value DESC LIMIT 10";
            $sql = $dbconnect -> prepare($stmt);
        }
        $sql -> execute();
        $result = $sql -> get_result();

        ?>
<table class="frutiger-tile" id="items-table-high-value">
    <tr>
        <th style="text-align: center">Item<br>Id</th>
        <th>Name</th>
        <th style="text-align: center">Rarity</th>
        <th style="text-align: center">Sell Value<br>(Gold Coins)</th>
        <th style="text-align: center">Total Times<br>Collected</th>
        <th style="text-align: center">Total Times<br>Sold</th>

    </tr>
    <!-- Getting and adding item entries -->
    <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr id="item_row_<?php echo $row['item_id'] ?>">
                <!-- Displaying details -->
                <td class="item-id" style="text-align: center"><?php echo $row['item_id'] ?></td>
                <td class="item-name item-display"><?php echo $row['item_name'] ?></td>
                <td class="rarity item-display"><span onclick="SelectRarityFilter('<?php echo $row['item_rarity'] ?>')" class="frutiger-tile <?php echo $row['item_rarity'] ?>"><?php echo $row['item_rarity'] ?></span></td>
                <td class="item-sell-value item-display" style="text-align: center"><?php echo $row['item_sell_value'] ?></td>
                <td class="item-total-times-collected item-display" style="text-align: center"><?php echo $row['item_total_times_collected'] ?></td>
                <td class="item-total-times-sold item-display" style="text-align: center"><?php echo $row['item_total_times_sold'] ?></td>
            </tr>
            <?php }
        }
    ?>
</table>
        <?php
    }

?>