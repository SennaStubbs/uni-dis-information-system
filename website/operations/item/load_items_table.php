<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        define('ALLOW_ACCESS', true);

        include("../../inc/dbconnect.php");
    }
    else {
        include("inc/dbconnect.php");
    }

	$rarities = explode(',', isset($_COOKIE['items_table_rarities']) ? htmlspecialchars($_COOKIE['items_table_rarities']) : "");
    $num_of_rows = isset($_COOKIE['items_table_num_of_rows']) ? filter_var($_SESSION['items_table_num_of_rows'], FILTER_SANITIZE_NUMBER_INT) : 25;

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
    $current_page = max(isset($_GET['page']) ? (int)$_GET['page'] : 1, 1);
    $offset = ($current_page - 1) * $num_of_rows;
    $total_pages = ceil($total_items / $num_of_rows);

    array_push($sql_parameters, $num_of_rows);
    array_push($sql_parameters, $offset);

    // Execute limited table sql
    $stmt = $dbconnect -> prepare($table_sql);
    $stmt -> bind_param(str_repeat('s', count($sql_parameters)), ...$sql_parameters);
    $stmt -> execute();
    $table_limit_result = $stmt -> get_result();
    
?>