<?php
    $_COOKIE['bleh'] = 'meow';

    if (isset($_POST['rarities'])) {
        $_SESSION['dashboard_rarities'] = explode(',', htmlspecialchars($_POST['rarities'], ENT_QUOTES, "UTF-8"));
        // if (!in_array($_POST['add_rarity'], $rarities))
        //     array_push($rarities, htmlspecialchars($_POST['add_rarity'], ENT_QUOTES, "UTF-8"));
    }
    // elseif (isset($_POST['remove_rarity'])) {
    //     if (in_array($_POST['add_rarity'], $rarities))
    //         unset($rarities[array_search($_POST['rarity'], $rarities)]);
    // }

    // $_SESSION['dashboard_rarities'] = $rarities;

    echo implode(',', $_SESSION['dashboard_rarities']);
?>