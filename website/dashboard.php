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
        <div class="chart-tooltip hidden frutiger-tile" id="pie-total-items-tooltip">
            <h1></h1>
            <p></p>
        </div>

        <!-- Main -->
        <main>

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

                <div class="chart-container frutiger-tile">
                    <h1>Total Number of Items</h1>
                    <div class="pie-chart-container">
                        <svg class="pie-chart" id="pie-total-items">
                            <?php
                                function DrawChart($sqlTotalQuery, $sqlRarityQuery, $dbconnect, $shadow = false) {
                                    // Chart variables
                                    $chartSize = 300;
                                    $chartMidpoint = $chartSize / 2;

                                    // Total value
                                    $sql = $sqlTotalQuery;
                                    $stmt = $dbconnect -> prepare($sql);
                                    $stmt -> execute();
                                    $result = $stmt -> get_result();
                                    
                                    $total = mysqli_fetch_assoc($result)['value'];
                                    $totalValue = 0;

                                    // Get individual rarity average
                                    $rarityOrder = ['Common', 'Uncommon', 'Rare', 'Epic', 'Legendary', 'Mythic', 'Exotic', 'Unreal'];

                                    $rarityInfo = [];

                                    foreach($rarityOrder as $rarity) {
                                        $sql = $sqlRarityQuery;
                                        $stmt = $dbconnect -> prepare($sql);
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
                                        

                                        
                                        echo '<circle r="150" cx="150" cy="150"' . ($shadow !== true ?
                                        'fill="url(#' . $portion_rarity . ')"
                                        data-end-mid-x="' . $portion_endMidX . '" data-end-mid-y="' . $portion_endMidY . '" data-rarity="' . $portion_rarity . '" data-value="' . $portion_value . '" data-percent="' . $portion_percent . '" data-selected="false"
                                        onmouseover="Pie_HoverSection(event, this, `' . $portion_rarity . '`, ' . $portion_value . ', `Items`)" onmousemove="Pie_MoveTooltip(event, this)" onmouseleave="Pie_ResetSection(event, this); Pie_HideTooltip(event, this)" onclick="SelectRarityFilter(``, this)"
                                        id="pie-total-items-' . $portion_rarity . '" style="' :
                                        'fill="rgba(0, 0, 0, 0.5)" id="pie-total-items-' . $portion_rarity . '-shadow" class="shadow"
                                        style="transform-origin: ' . $chartMidpoint . 'px ' . $chartMidpoint . 'px; ') .
                                            'clip-path: polygon('
                                                . $chartMidpoint . 'px 0px, ' // Start point
                                                . $chartSize . 'px 0px, ' // Top-right

                                                . $chartSize . 'px ' . $chartSize . 'px, ' // Bottom-right

                                                . ($portion_percent > 0.75 ? '0px ' . $chartSize . 'px, ' : '') // Bottom-left if applicable

                                                . ($portion_cx >= $chartMidpoint ? $chartSize : 0) . 'px '
                                                . ($portion_cy >= $chartMidpoint ? $chartSize : 0) . 'px, '

                                                . ($portion_percent > 0.75 ? '0px 0px, ' : '') // Top-left if applicable

                                                . $portion_cx . 'px ' . $portion_cy . 'px, ' // End point
                                            . '50% 50%);
                                            
                                            transform-origin: ' . $chartMidpoint . 'px ' . $chartMidpoint . 'px;
                                            transform: Rotate(' . 360 * ($percentOffset) . 'deg)' . ($shadow == true ? ' scale(1.02);' : '') . '">'
                                        . '</circle>';

                                        
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
                                // $total = DrawAverageChart("SELECT Avg(item_sell_value) AS 'value' FROM items", "SELECT Avg(item_sell_value) AS 'value' FROM (SELECT item_sell_value FROM items WHERE item_rarity = ?) items", $dbconnect);
                                $total = DrawChart("SELECT COUNT(item_id) AS 'value' FROM items", "SELECT COUNT(item_id) AS 'value' FROM (SELECT item_id FROM items WHERE item_rarity = ?) items", $dbconnect);
                            ?>
                        </svg>
                        <!-- Shadows of each section -->
                        <svg class="pie-chart shadow" id="pie-total-items-shadow">
                            <?php DrawChart("SELECT COUNT(item_id) AS 'value' FROM items", "SELECT COUNT(item_id) AS 'value' FROM (SELECT item_id FROM items WHERE item_rarity = ?) items", $dbconnect, true); ?>
                        </svg>
                    </div>
                    <h2>Total<span class="hidden"> (from selection)</span>: <span class="value" id="pie-total-items-total"><?php echo $total ?> Items</span></h2>
                </div>

                <!-- Script for charts -->
                <script>
                    var rarityOrder = [
                        <?php $i = 1; foreach($rarityOrder as $rarity) {
                            echo "'" . $rarity . "'" . ($i < count($rarityOrder) ? ', ' : '');
                            $i++;
                        } ?>];
                </script>
            </div>
            
        </main>

        <!-- Link JavaScript -->
        <script src="js/main.js"></script>
        <script src="js/dashboard.js"></script>
    </body>
</html>