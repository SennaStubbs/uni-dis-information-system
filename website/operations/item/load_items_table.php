<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        define('ALLOW_ACCESS', true);
    }

    include($_SERVER["DOCUMENT_ROOT"] . "/information_system/website/inc/dbconnect.php");

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Need a user account to view
    if(!isset($_SESSION['user_id'])) {
        echo 'no valid session';
    }

    include($_SERVER["DOCUMENT_ROOT"] . "/information_system/website/operations/user/get_user.php");

    if (!isset($table_limit_result)) {
        $rarities = explode(',', isset($_COOKIE['items_table_rarities']) ? htmlspecialchars($_COOKIE['items_table_rarities']) : "");
        $num_of_rows = isset($_COOKIE['items_table_num_of_rows']) ? filter_var($_COOKIE['items_table_num_of_rows'], FILTER_SANITIZE_NUMBER_INT) : 25;

        $rarity_placeholders = (count($rarities) > 0 ? '?' : '') . (count($rarities) > 1 ? str_repeat(', ?', count($rarities) - 1) : '');
        
        if (count($rarities) > 0 && $rarities[0] != ''){
            $sql_parameters = $rarities;

            // Get total number of items
            $total_sql = "SELECT COUNT(*) AS count FROM (SELECT * FROM items WHERE item_rarity IN ($rarity_placeholders)) items ";
            $stmt = $dbconnect -> prepare($total_sql);
            $stmt -> bind_param(str_repeat('s', count($rarities)), ...$rarities);

            $table_sql = "SELECT * FROM items WHERE item_rarity IN ($rarity_placeholders) LIMIT ? OFFSET ?";
        }
        else {
            $sql_parameters = [];

            // Get total number of items
            $total_sql = "SELECT COUNT(*) AS count FROM items";
            $stmt = $dbconnect -> prepare($total_sql);

            $table_sql = "SELECT * FROM items LIMIT ? OFFSET ?";
        }

        // Execute total sql
        $stmt -> execute();
        $result = $stmt -> get_result();
        $total_items = mysqli_fetch_assoc($result)['count'];

        // Pagination
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $current_page = max(isset($_POST['page']) ? (int)$_POST['page'] : 1, 1);
        } else {
            $current_page = max(isset($_GET['page']) ? (int)$_GET['page'] : 1, 1);
        }

        $offset = ($current_page - 1) * $num_of_rows;
        $total_pages = ceil($total_items / $num_of_rows);

        array_push($sql_parameters, $num_of_rows);
        array_push($sql_parameters, $offset);

        // Execute limited table sql
        $stmt = $dbconnect -> prepare($table_sql);
        $stmt -> bind_param(str_repeat('s', count($sql_parameters)), ...$sql_parameters);
        $stmt -> execute();
        $table_limit_result = $stmt -> get_result();
    }
    
    // Send back HTML
    if ($_SERVER['REQUEST_METHOD'] == 'POST' || !isset($initialise) || $initialise == false) { ?>
<table class="frutiger-tile" id="items-table">
    <tr>
        <th style="text-align: center">Item<br>Id</th>
        <th>Name</th>
        <th style="text-align: center">Rarity</th>
        <th style="text-align: center">Sell Value<br>(Gold Coins)</th>
        <th style="text-align: center">Total Times<br>Collected</th>
        <th style="text-align: center">Total Times<br>Sold</th>
        <?php if ($user_access_level == -1): ?>
        <th style="text-align: center">Actions</th>
        <?php endif ?>
    </tr>
    <!-- Getting and adding item entries -->
    <?php
        if (mysqli_num_rows($table_limit_result) > 0) {
            while ($row = mysqli_fetch_assoc($table_limit_result)) { ?>
            <tr id="item_row_<?php echo $row['item_id'] ?>">
                
                <!-- Displaying details -->
                <td class="item-id" style="text-align: center"><?php echo $row['item_id'] ?></td>
                <td class="item-name item-display"><?php echo $row['item_name'] ?></td>
                <td class="rarity item-display"><span class="frutiger-tile <?php echo $row['item_rarity'] ?>"><?php echo $row['item_rarity'] ?></span></td>
                <td class="item-sell-value item-display" style="text-align: center"><?php echo $row['item_sell_value'] ?></td>
                <td class="item-total-times-collected item-display" style="text-align: center"><?php echo $row['item_total_times_collected'] ?></td>
                <td class="item-total-times-sold item-display" style="text-align: center"><?php echo $row['item_total_times_sold'] ?></td>

                <!-- Form for editing -->
                <form class="edit" id="edit_item_<?php echo $row['item_id'] ?>"></form>
                <!-- Edit inputs -->
                <td class="item-id hidden" style="text-align: center"><input value="<?php echo $row['item_id'] ?>" name="item_id" form="edit_item_<?php echo $row['item_id'] ?>"></td>
                <td class="item-name edit-input hidden">
                    <input value="<?php echo $row['item_name'] ?>" name="item_name" form="edit_item_<?php echo $row['item_id'] ?>">
                </td>
                <td class="rarity edit-input hidden">
                    <select name="item_rarity" form="edit_item_<?php echo $row['item_id'] ?>">
                        <option value="Common" <?php if ($row['item_rarity'] == 'Common'): ?>selected<?php endif ?>>Common</option>
                        <option value="Uncommon" <?php if ($row['item_rarity'] == 'Uncommon'): ?>selected<?php endif ?>>Uncommon</option>
                        <option value="Rare" <?php if ($row['item_rarity'] == 'Rare'): ?>selected<?php endif ?>>Rare</option>
                        <option value="Epic" <?php if ($row['item_rarity'] == 'Epic'): ?>selected<?php endif ?>>Epic</option>
                        <option value="Legendary" <?php if ($row['item_rarity'] == 'Legendary'): ?>selected<?php endif ?>>Legendary</option>
                        <option value="Mythic" <?php if ($row['item_rarity'] == 'Mythic'): ?>selected<?php endif ?>>Mythic</option>
                        <option value="Exotic" <?php if ($row['item_rarity'] == 'Exotic'): ?>selected<?php endif ?>>Exotic</option>
                        <option value="Unreal" <?php if ($row['item_rarity'] == 'Unreal'): ?>selected<?php endif ?>>Unreal</option>
                    </select>
                </td>
                <td class="item-sell-value edit-input hidden" style="text-align: center">
                    <input value="<?php echo $row['item_sell_value'] ?>" name="item_sell_value" form="edit_item_<?php echo $row['item_id'] ?>" style="text-align: center;">
                </td>
                <td class="item-total-times-collected edit-input hidden" style="text-align: center">
                    <input value="<?php echo $row['item_total_times_collected'] ?>" name="item_total_times_collected" form="edit_item_<?php echo $row['item_id'] ?>" style="text-align: center;">
                </td>
                <td class="item-total-times-sold edit-input hidden" style="text-align: center">
                    <input value="<?php echo $row['item_total_times_sold'] ?>" name="item_total_times_sold" form="edit_item_<?php echo $row['item_id'] ?>" style="text-align: center;">
                </td>

                <!-- Admin actions -->
                <?php if ($user_access_level == -1): ?>
                <!-- Editing and deleting row -->
                <td class="admin-actions item-display" style="text-align: center">
                    <button style="background-image: url('images/icons/edit.png')" title="Edit Item"
                        onclick="EditItem(<?php echo $row['item_id'] ?>, {
                            'name': '<?php echo $row['item_name'] ?>',
                            'rarity': '<?php echo $row['item_rarity'] ?>',
                            'sell_value': <?php echo $row['item_sell_value'] ?>,
                            'times_collected': <?php echo $row['item_total_times_collected'] ?>,
                            'times_sold': <?php echo $row['item_total_times_sold'] ?>
                        })">
                    </button>
                    <button style="background-image: url('images/icons/delete.png')" title="Delete Item"
                        onclick="ConfirmationPopup('Are you sure?', 'This will permanently delete Item ID <?php echo $row['item_id'] ?> (\'<?php echo $row['item_name'] ?>\') from the database.', 'delete_item', {'itemId': <?php echo $row['item_id'] ?>})">
                    </button>
                </td>
                <!-- Confirming or canceling edits -->
                    <td class="admin-actions edit-input hidden" style="text-align: center">
                    <button style="background-image: url('images/icons/confirm.png')" title="Submit Edits"
                        onclick="ConfirmationPopup('Are you sure?', 'This will overwrite the existing data for Item ID <?php echo $row['item_id'] ?>.', 'edit_item', {'itemId': <?php echo $row['item_id'] ?>})">
                    </button>
                    <button style="background-image: url('images/icons/cancel.png')" title="Cancel Edits"
                        onclick="ConfirmationPopup('Are you sure?', 'This will revert any changes made to Item Id <?php echo $row['item_id'] ?>.', 'cancel_edit', {'itemId': <?php echo $row['item_id'] ?>})">
                    </button>
                </td>
                <?php endif ?>
            </tr>
            <?php }
        }
    ?>
</table>
    <?php }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        echo '!!!pagination: {
            "total_pages": ' . $total_pages . ',
            "current_items": ' . mysqli_num_rows($table_limit_result) . ',
            "total_items": ' . $total_items . ',
            "offset": ' . $offset . '
        }';
    }

    $initialise = false;
?>