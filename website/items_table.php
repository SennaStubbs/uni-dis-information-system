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

    include("inc/dbconnect.php");

	include('operations/item/load_items_table.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/styles.css">
        <link rel="stylesheet" type="text/css" href="css/themes.css">
    </head>

    <!-- <body class="<?php echo $theme ?>"> -->
	<body>
		<?php include("inc/navigation.php"); ?>

        <div class="background"></div>
        <main>
			<div class="table">
				<div class="frutiger-tile pagination">
					<div class="pagination-button" style="justify-content: right">
						<a <?php if ($current_page > 1) { echo 'href="?page=' . $current_page - 1 . '"'; } ?> class="frutiger-tile <?php if ($current_page <= 1) { echo 'disabled'; } ?>" draggable="false">PREVIOUS PAGE</a>
					</div>
					<h1>Showing items <span class="num"><?php echo 1 + $offset; ?> - <?php echo mysqli_num_rows($table_limit_result) + $offset; ?></span> of <span class="num"><?php echo $total_items ?></span>
						<span class="page">Page <?php echo $current_page; ?> of <?php echo $total_pages ?></span>
					</h1>
					<div class="pagination-button" style="justify-content: left">
						<a <?php if ($current_page < $total_pages) { echo 'href="?page=' . $current_page + 1 . '"'; } ?> class="frutiger-tile <?php if ($current_page >= $total_pages) { echo 'disabled'; } ?>" draggable="false">NEXT PAGE</a>
					</div>
				</div>
				<table class="frutiger-tile">
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
								<form class="edit" method="post" action="javascript:Post_EditItem(<?php echo $row['item_id'] ?>)" id="edit_item_<?php echo $row['item_id'] ?>"></form>
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
									<form method="post" action="javascript:Post_DeleteItem(<?php echo $row['item_id'] ?>)" id="delete_item_<?php echo $row['item_id'] ?>">
										<input name="item_id" value="<?php echo $row['item_id'] ?>">
									</form>
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
			</div>
			<div class="charts">
				<!-- Pagination settings -->
				<div class="frutiger-tile stats">
					<h1>Rows per page: <form action="items_table.php" method="post" id="rows" style="display: inline">
							<select name="num_of_rows" form="rows" onchange="this.form.submit()" >
								<option value="25" <?php if ($num_of_rows == 25): ?>selected<?php endif ?>>25</option>
								<option value="50" <?php if ($num_of_rows == 50): ?>selected<?php endif ?>>50</option>
								<option value="75" <?php if ($num_of_rows == 75): ?>selected<?php endif ?>>75</option>
								<option value="100" <?php if ($num_of_rows == 100): ?>selected<?php endif ?>>100</option>
								<option value="250" <?php if ($num_of_rows == 250): ?>selected<?php endif ?>>250</option>
								<option value="500" <?php if ($num_of_rows == 500): ?>selected<?php endif ?>>500</option>
								<!-- <option value="1000" <?php if ($num_of_rows == 1000): ?>selected<?php endif ?>>1000</option> -->
							</select>
						</form>
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
				<div class="frutiger-tile filters">
					<h1>Filter Rarity</h1>
					<form action="items_table.php" method="post" class="rarity-list">
						<?php
							$rarities_sql = "SELECT DISTINCT item_rarity FROM items";
							$rarities_stmt = $dbconnect -> prepare($rarities_sql);
							$rarities_stmt -> execute();
							$rarities_result = $rarities_stmt -> get_result();
							if (mysqli_num_rows($rarities_result) > 0) {
								$rarityOrder = ['Common', 'Uncommon', 'Rare', 'Epic', 'Legendary', 'Mythic', 'Exotic', 'Unreal'];

								while ($row = mysqli_fetch_assoc($rarities_result)) {
									if (in_array($row['item_rarity'], $rarityOrder)) { ?>
										<button name="rarity" value="<?php echo $row['item_rarity']; ?>" style="order: <?php echo array_search($row['item_rarity'], $rarityOrder) ?>" class="frutiger-tile rarity <?php echo $row['item_rarity']; ?> <?php if(!in_array($row['item_rarity'], $rarities)) echo "not-selected"; ?>"><?php echo $row['item_rarity']; ?></button>
									<?php }
								}
							}
						?>
					</form>
				</div>
			</div>
        </main>

		<div class="confirmation-popup hidden" id="popup">
			<div class="popup-box frutiger-glossy">
				<p class="question">{question}</p>
				<p class="message">{message}</p>
				<div class="buttons">
					<button class="frutiger-tile continue" onclick="">Continue</button>
					<button class="frutiger-tile cancel">Go Back</button>
				</div>
			</div>
		</div>

		<!-- Link JavaScript -->
        <script src="js/main.js"></script>
        <script src="js/items_table.js"></script>
		<?php if ($user_access_level == -1): ?>
        <script src="js/item_editor.js"></script>
		<?php endif ?>
    </body>
</html>






