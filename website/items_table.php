<!-- ALL IMAGE ASSETS OBTAINED FROM https://frutigeraeroarchive.org/ -->

<?php
	define('ALLOW_ACCESS', true);

    if (session_status() === PHP_SESSION_NONE) {
		session_start();
    }

	// Need a user account to view
	if(!isset($_SESSION['user_id'])) {
		header('location: index.php');
		exit();
	}

    include($_SERVER["DOCUMENT_ROOT"] . "/information_system/website/inc/dbconnect.php");

	$initialise = true;
	include($_SERVER["DOCUMENT_ROOT"] . "/information_system/website/operations/item/load_items_table.php");

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

    <!-- <body class="<?php echo $theme ?>"> -->
	<body>
		<?php include($_SERVER["DOCUMENT_ROOT"] . "/information_system/website/inc/navigation.php"); ?>

        <div class="background"></div>
        <main>
			<div class="table">
				<!-- Pagination -->
				<div class="frutiger-tile pagination">
					<div class="pagination-button" style="justify-content: right">
						<button onclick="ChangePage(-1)" class="frutiger-tile" <?php if ($current_page <= 1) { echo 'disabled'; } ?> draggable="false">PREVIOUS PAGE</button>
					</div>
					<h1 id="pagination">Showing items <span class="num"><?php echo 1 + $offset; ?> - <?php echo mysqli_num_rows($table_limit_result) + $offset; ?></span> of <span class="num"><?php echo $total_items ?></span>
						<span class="page">Page <?php echo $current_page; ?> of <?php echo $total_pages ?></span>
					</h1>
					<div class="pagination-button" style="justify-content: left">
						<button onclick="ChangePage(1)" class="frutiger-tile" <?php if ($current_page >= $total_pages) { echo 'disabled'; } ?> draggable="false">NEXT PAGE</button>
					</div>
				</div>
				<!-- Items table -->
				<table class="frutiger-tile" id="items-table">

				</table>
			</div>
			<div class="charts">
				<!-- Pagination settings -->
				<div class="frutiger-tile stats">
					<h1>Rows per page: 
						<select name="num_of_rows" onchange="UpdateNumOfRows(event)" >
							<option value="25" <?php if ($num_of_rows == 25): ?>selected<?php endif ?>>25</option>
							<option value="50" <?php if ($num_of_rows == 50): ?>selected<?php endif ?>>50</option>
							<option value="75" <?php if ($num_of_rows == 75): ?>selected<?php endif ?>>75</option>
							<option value="100" <?php if ($num_of_rows == 100): ?>selected<?php endif ?>>100</option>
							<option value="250" <?php if ($num_of_rows == 250): ?>selected<?php endif ?>>250</option>
							<option value="500" <?php if ($num_of_rows == 500): ?>selected<?php endif ?>>500</option>
							<!-- <option value="1000" <?php if ($num_of_rows == 1000): ?>selected<?php endif ?>>1000</option> -->
						</select>
					</h1>
				</div>
				<!-- Adding item menu -->
				<div class="table-buttons">
					<button class="frutiger-tile" id="table-add-item" onclick="ShowAddItem()">
						Add Item
					</button>
					<div class="frutiger-tile hidden" id="add-item-container">
						<h1>Add Item</h1>
						<form action="/information_system/website/operations/item/add_item.php" onsubmit="ValidateAddItem()" method="post" id="add-item">
							<label>Item Name: <input name="item_name" data-default-value="" required /></label>
							<label>Item Description: <textarea name="item_description" data-default-value=""></textarea></label>
							<label>Item Rarity: <select name="item_rarity" data-default-value="Common" required>
								<option value="Common" selected>Common</option>
								<option value="Uncommon">Uncommon</option>
								<option value="Rare">Rare</option>
								<option value="Epic">Epic</option>
								<option value="Legendary">Legendary</option>
								<option value="Mythic">Mythic</option>
								<option value="Exotic">Exotic</option>
								<option value="Unreal">Unreal</option>
							</select></label>
							<label>Item Sell Value (Gold Coins): <input name="item_sell_value" type="number" data-default-value="0" required /></label>
							<label>Item Total Times Collected: <input name="item_total_times_collected" type="number" data-default-value="0" required /></label>
							<label>Item Total Times Sold: <input name="item_total_times_sold" type="number" data-default-value="0" required /></label>
						</form>
						<div class="buttons">
							<button class="frutiger-tile continue" type="submit" form="add-item">
								Add
							</button>
							<button class="frutiger-tile cancel" onclick="HideAddItem()">
								Cancel
							</button>
						</div>
					</div>
				</div>
				<!-- Item filters -->
				<div class="frutiger-tile filters" id="filters">
					<h1>Filter Rarity</h1>
					<div class="rarity-list">
                        <?php foreach ($rarityOrder as $rarity) { ?>
                        <button id="button-filter-<?php echo $rarity ?>" onclick="SelectRarityFilter(`<?php echo $rarity ?>`)" class="frutiger-tile rarity <?php echo $rarity ?> not-selected">
                            <?php echo $rarity ?>
                        </button>
                        <?php } ?>
                    </div>
				</div>
			</div>
        </main>

		<div class="confirmation-popup hidden" id="popup">
			<div class="popup-box frutiger-glossy">
				<p class="question"></p>
				<p class="message"></p>
				<div class="buttons">
					<button class="frutiger-tile continue">Continue</button>
					<button class="frutiger-tile cancel" onclick="ClosePopup()">Go Back</button>
				</div>
			</div>
		</div>

		<!-- Universal rarity order -->
		<script>
			var rarityOrder = [
				<?php $i = 1; foreach($rarityOrder as $rarity) {
					echo "'" . $rarity . "'" . ($i < count($rarityOrder) ? ', ' : '');
					$i++;
				} ?>];
		</script>

		<!-- Link JavaScript -->
        <script src="js/main.js"></script>
        <script src="js/items_table.js"></script>
		<?php if ($user_access_level == -1): ?>
        <script src="js/item_editor.js"></script>
		<?php endif ?>
    </body>
</html>






