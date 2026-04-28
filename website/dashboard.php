<?php
	define('ALLOW_ACCESS', true);

    if (session_status() === PHP_SESSION_NONE) {
		session_start();
    }

    // Need a user account to view
    if(!isset($_SESSION['user_id'])) {
        header('location: index');
    }

    include ("inc/dbconnect.php");

    $rarities = isset($_COOKIE['dashboard_rarities']) ? explode(',', htmlspecialchars($_COOKIE['dashboard_rarities'], ENT_QUOTES, 'UTF-8')) : array();

    $rarityOrder = ['Common', 'Uncommon', 'Rare', 'Epic', 'Legendary', 'Mythic', 'Exotic', 'Unreal'];
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/styles.css">
        <link rel="stylesheet" type="text/css" href="css/themes.css">

        <script>
            var selectedRarities = [<?php if (count($rarities) > 0) { if ($rarities[0] != '') { $i = 0; foreach ($rarities as $rarity) { echo '"' . $rarity . '"' . ($i + 1 < count($rarities) ? ',' : ''); $i++; } } } ?>];
        </script>
    </head>
    <body>
		<?php include($_SERVER["DOCUMENT_ROOT"] . "/information_system/website/inc/navigation.php"); ?>

        <div class="background"></div>

        <!-- Tooltips -->
        <div class="chart-tooltip hidden frutiger-tile" id="chart-tooltip">
            <h1></h1>
            <p></p>
        </div>

        <!-- Main -->
        <main>

            <?php
                // Draw chart functions
                function DrawPieChart($chartId, $sqlTotalQuery, $sqlRarityQuery, $dbconnect, $rarityOrder, $shadow = false) {
                    // Chart variables
                    $chartSize = 300;
                    $chartMidpoint = $chartSize / 2;

                    // Total value
                    $stmt = $dbconnect -> prepare($sqlTotalQuery);
                    $stmt -> execute();
                    $result = $stmt -> get_result();
                    
                    $total = mysqli_fetch_assoc($result)['value'];
                    $totalValue = 0;

                    // Get individual rarity average
                    $rarityInfo = [];

                    foreach($rarityOrder as $rarity) {
                        $stmt = $dbconnect -> prepare($sqlRarityQuery);
                        $stmt -> bind_param('s', $rarity);
                        $stmt -> execute();
                        $result = $stmt -> get_result();

                        
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $value = ceil($row['value']);

                                // Determine colour(s)
                                switch ($rarity) {
                                    case "Common":
                                        $colours = ['#d3d3d3', '#a7a7a7', '#ffffff'];
                                        break;
                                    case "Uncommon":
                                        $colours = ['#92df5f', '#4c8844', '#daffc2'];
                                        break;
                                    case "Rare":
                                        $colours = ['#56c7e9', '#59799e', '#d2f5ff'];
                                        break;
                                    case "Epic":
                                        $colours = ['#bb29c9', '#634077', '#fac7ff'];
                                        break;
                                    case "Legendary":
                                        $colours = ['#f79d26', '#aa502d', '#ffe6c6'];
                                        break;
                                    case "Mythic":
                                        $colours = ['#f7d514', '#c59939', '#fff5bc'];
                                        break;
                                    case "Exotic":
                                        $colours = ['#ec1f1f', '#c42e67', '#ffbaba'];
                                        break;
                                    case "Unreal":
                                        $colours = ['#ff6aeb', '#c74591', '#ffc0f7'];
                                        break;
                                    default:
                                        $colours = ['#f5f5f5', '#b9b9b9', '#ffffff'];
                                }

                                // Add to array of all averages
                                array_push($rarityInfo, [$rarity, $value, $colours]);
                                // Add up total average
                                $totalValue += $value;
                            }
                        }
                    }

                    // Offset for rotating each section to their correct orientation
                    $percentOffset = 0;
                    // Definitions (for gradients, etc)
                    $definitions = '';

                    foreach($rarityInfo as $rarityInfo) {
                        $portion_rarity = $rarityInfo[0];
                        $portion_value = $rarityInfo[1];
                        $portion_colours = $rarityInfo[2];

                        $portion_percent = $portion_value / $totalValue;

                        // Calculate portion end point on circle perimeter
                        $portion_cx = $chartMidpoint + ($chartMidpoint * sin(2 * pi() * $portion_percent));
                        $portion_cy = $chartMidpoint + ($chartMidpoint * -cos(2 * pi() * $portion_percent));                                        

                        // Point between start and end points
                        $portion_endMidX = (($portion_cx - $chartMidpoint) / 2) + $chartMidpoint;
                        $portion_endMidY = $portion_cy / 2;
                        
                        ?>
                    <circle r="150" cx="150" cy="150"
                        <?php if($shadow !== true) { ?>
                        fill="url(#<?php echo $portion_rarity ?>)"

                        data-end-mid-x="<?php echo $portion_endMidX ?>" data-end-mid-y="<?php echo $portion_endMidY ?>"
                        data-rarity="<?php echo $portion_rarity ?>" data-value="<?php echo $portion_value ?>"
                        data-percent="<?php echo $portion_percent ?>" data-selected="false"

                        onmouseover="Pie_HoverSection(event, this, `<?php echo $portion_rarity ?>`, <?php echo $portion_value ?>)"
                        onmousemove="MoveTooltip(event, this)"
                        onmouseleave="Pie_ResetSection(event, this); HideTooltip(event, this)"
                        onclick="SelectRarityFilter(``, this)"

                        id="<?php echo $chartId ?>-<?php echo $portion_rarity ?>"
                        <?php } else { ?>
                        fill="rgba(0, 0, 0, 0.5)"
                        id="<?php echo $chartId ?>-<?php echo $portion_rarity ?>-shadow" class="shadow"
                        <?php } ?>
                            style="
                            clip-path: polygon(
                                <?php echo $chartMidpoint ?>px 0px, <?php // Start point ?>
                                <?php echo $chartSize ?>px 0px, <?php // Top-right ?>

                                <?php echo $chartSize ?>px <?php echo $chartSize ?>px, <?php // Bottom-right ?>

                                <?php echo ($portion_percent > 0.75 ? '0px ' . $chartSize . 'px, ' : '') // Bottom-left if applicable ?>

                                <?php echo ($portion_cx >= $chartMidpoint ? $chartSize : 0) ?>px
                                <?php echo ($portion_cy >= $chartMidpoint ? $chartSize : 0) ?>px,

                                <?php echo ($portion_percent > 0.75 ? '0px 0px, ' : '') // Top-left if applicable ?>

                                <?php echo $portion_cx ?>px <?php echo $portion_cy ?>px, <?php // End point ?>
                                50% 50%
                            );
                            
                            transform-origin: <?php echo $chartMidpoint ?>px <?php echo $chartMidpoint ?>px;
                            transform: Rotate(<?php echo 360 * ($percentOffset) ?>deg) <?php echo ($shadow == true ? 'scale(1.02);' : '') ?>">
                        </circle>
                        <?php 

                        // Definitions
                        if ($shadow !== true)
                        {
                            $definitions = $definitions . 
                            '<radialGradient id="' . $portion_rarity . '" cx="50%" cy="50%" r="55%" fx="50%" fy="50%">
                                <stop offset="0%" stop-color="' . $portion_colours[0] . '" />
                                <stop offset="50%" stop-color="' . $portion_colours[1] . '" />
                                <stop offset="100%" stop-color="' . $portion_colours[2] . '" />
                            </radialGradient>';
                        }
                            

                        $percentOffset += $portion_percent;
                    }

                    if ($shadow !== true) {
                        echo '<defs>
                            ' . $definitions . '?>
                        </defs>';
                    }

                    return $total;
                }

                function DrawBarChart($chartId, $sqlQuery, $dbconnect, $rarityOrder) { ?>
                    <div class="bar-names">
                        <?php
                            foreach($rarityOrder as $rarity) { ?>
                        <h1 data-rarity="<?php echo $rarity ?>" onclick="SelectRarityFilter(``, this)"><?php echo $rarity ?></h1>
                            <?php }
                        ?>
                    </div>
                    <div class="bars">
                        <?php
                            $rows = [];
                            $totalValue = 0;
                            $highestValue = 0;
                            foreach($rarityOrder as $rarity) {
                                $stmt = $dbconnect -> prepare($sqlQuery);
                                $stmt->bind_param('s', $rarity);
                                $stmt -> execute();
                                $result = $stmt -> get_result();

                                
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) { 
                                        array_push($rows, array(
                                            "rarity" => $rarity,
                                            "sum" => $row['sum']
                                        ));
                                        $totalValue += $row['sum'];
                                        if ($row['sum'] > $highestValue) {
                                            $highestValue = $row['sum'];
                                        }
                                    }
                                }
                            }

                            // Find incremental value
                            $divisor = 1;
                            $incrementalValue = 0;
                            do {
                                if ($highestValue % $divisor >= $highestValue || $divisor > 10000) {
                                    $incrementalValue = (ceil($highestValue / ($divisor / 10)) * ($divisor / 10)) / 10;
                                }
                                else {
                                    $divisor *= 10;
                                }
                            } while ($incrementalValue == 0);
                            
                            foreach($rows as $row) { ?>
                            <div class="bar frutiger-tile <?php echo $row['rarity'] ?>"
                                id="<?php echo $chartId ?>-<?php echo $row['rarity'] ?>"
                                style="width: <?php echo (($row['sum'] / ($incrementalValue * 10)) * 100) . '%' ?>"
                                data-rarity="<?php echo $row['rarity'] ?>"

                                onmouseover="Bar_HoverSection(event, this, '<?php echo $row['rarity'] ?>', <?php echo $row['sum'] ?>)"
                                onmousemove="MoveTooltip(event, this)"
                                onmouseleave="HideTooltip(event, this)"
                                onclick="SelectRarityFilter(``, this)"
                            ></div>
                            <?php }
                        ?>
                    </div>
                    <div class="bottom-axis">
                        <div class="bar-names">
                            <h1 style="font-weight: normal">Times Sold</h1>
                        </div>
                        <div class="counters">
                            <h2></h2>
                            <?php 

                                for ($i = 1; $i <= 10; $i++) { ?>
                            <h2><?php echo $incrementalValue * $i ?></h2>
                                <?php }
                            ?>
                        </div>
                    </div>
                <?php }
            ?>

            <div class="dashboard frutiger-glossy">
                <!-- Filters -->
                <div id="filters" class="frutiger-tile filters">
					<h1>Filter Rarity</h1>
                    <div class="rarity-list">
                        <?php foreach ($rarityOrder as $rarity) { ?>
                        <button id="button-filter-<?php echo $rarity ?>" onclick="SelectRarityFilter(`<?php echo $rarity ?>`)" class="frutiger-tile rarity <?php echo $rarity ?>">
                            <?php echo $rarity ?>
                        </button>
                        <?php } ?>
                    </div>
				</div>

                <!-- Total number of items -->
                <div class="chart-container frutiger-tile">
                    <h1>Total Number of Items</h1>
                    <div class="pie-chart-container">
                        <svg class="pie-chart" id="pie-total-items" data-operation="count" data-value-prefix="Total" data-value-type="Items">
                            <?php
                                $total = DrawPieChart(
                                    "pie-total-items",
                                    "SELECT COUNT(item_id) AS 'value' FROM items",
                                    "SELECT COUNT(item_id) AS 'value' FROM (SELECT item_id FROM items WHERE item_rarity = ?) items",
                                    $dbconnect,
                                    $rarityOrder
                                );
                            ?>
                        </svg>
                        <!-- Shadows of each section -->
                        <svg class="pie-chart shadow" id="pie-total-items-shadow">
                            <?php
                                DrawPieChart(
                                    "pie-total-items",
                                    "SELECT COUNT(item_id) AS 'value' FROM items",
                                    "SELECT COUNT(item_id) AS 'value' FROM (SELECT item_id FROM items WHERE item_rarity = ?) items",
                                    $dbconnect,
                                    $rarityOrder,
                                    true
                                );
                            ?>
                        </svg>
                    </div>
                    <h2>Total<span class="from-selection hidden"> (from selection)</span>: <span class="value" id="pie-total-items-total"><?php echo $total ?> Items</span></h2>
                </div>

                <!-- Average sell value of items by rarity -->
                <div class="chart-container frutiger-tile">
                    <h1>Average Sell Value By Rarity</h1>
                    <div class="pie-chart-container">
                        <svg class="pie-chart" id="pie-average-sell-value" data-operation="average" data-value-prefix="Average" data-value-type="Gold Coins">
                            <?php
                                $average = DrawPieChart(
                                    "pie-average-sell-value",
                                    "SELECT Avg(item_sell_value) AS 'value' FROM items",
                                    "SELECT Avg(item_sell_value) AS 'value' FROM (SELECT item_sell_value FROM items WHERE item_rarity = ?) items",
                                    $dbconnect,
                                    $rarityOrder
                                );
                            ?>
                        </svg>
                        <!-- Shadows of each section -->
                        <svg class="pie-chart shadow" id="pie-average-sell-value-shadow">
                            <?php
                                DrawPieChart(
                                    "pie-average-sell-value",
                                    "SELECT Avg(item_sell_value) AS 'value' FROM items",
                                    "SELECT Avg(item_sell_value) AS 'value' FROM (SELECT item_sell_value FROM items WHERE item_rarity = ?) items",
                                    $dbconnect,
                                    $rarityOrder,
                                    true
                                );
                            ?>
                        </svg>
                    </div>
                    <h2>Average<span class="from-selection hidden"> (from selection)</span>: <span class="value" id="pie-average-sell-value-total"><?php echo $average ?> Gold Coins</span></h2>
                </div>

                <!-- Top 10 highest valued items -->
                 <div class="chart-container frutiger-tile">
                    <h1>Top 10 Items With The Highest Sell Value</h1>
                    <?php include('operations/dashboard/fetch_high_value_items.php') ?>
                 </div>

                <!-- Total times collected -->
                <div class="chart-container frutiger-tile">
                    <h1>Total Times Collected</h1>
                    <div class="bar-chart" id="bar-times-collected" data-operation="sum_collected" data-value-prefix="Total" data-value-type="Times">
                        <?php DrawBarChart('bar-times-collected', "SELECT SUM(item_total_times_collected) AS 'sum' FROM items WHERE item_rarity = ?", $dbconnect, $rarityOrder); ?>
                    </div>
                    <h2>Total<span class="from-selection hidden"> (from selection)</span>: <span class="value" id="bar-times-collected-counter"><?php echo $totalValue ?> Times</span></h2>
                </div>

                <!-- Total times sold -->
                <div class="chart-container frutiger-tile">
                    <h1>Total Times Sold</h1>
                    <div class="bar-chart" id="bar-times-sold" data-operation="sum_sold" data-value-prefix="Total" data-value-type="Times">
                        <?php DrawBarChart('bar-times-sold', "SELECT SUM(item_total_times_sold) AS 'sum' FROM items WHERE item_rarity = ?", $dbconnect, $rarityOrder); ?>
                    </div>
                    <h2>Total<span class="from-selection hidden"> (from selection)</span>: <span class="value" id="bar-times-sold-counter"><?php echo $totalValue ?> Times</span></h2>
                </div>
            </div>
        </main>

        <!-- Script for charts -->
        <script>
            var rarityOrder = [
                <?php $i = 1; foreach($rarityOrder as $rarity) {
                    echo "'" . $rarity . "'" . ($i < count($rarityOrder) ? ', ' : '');
                    $i++;
                } ?>];
        </script>

        <!-- Link JavaScript -->
        <script src="js/main.js"></script>
        <script src="js/dashboard.js"></script>
    </body>
</html>