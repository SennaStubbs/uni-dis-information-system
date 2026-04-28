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
    include($_SERVER["DOCUMENT_ROOT"] . "/information_system/website/operations/user/get_user.php");

    // Need a user account with the correct access level to view
    if($user_access_level != -1 && $user_access_level < 2) {
        header('location: index.php');
        exit();
    }

	$initialise = true;
	include($_SERVER["DOCUMENT_ROOT"] . "/information_system/website/operations/user/load_users_table.php");
?>

<!DOCTYPE html>
<html>
    

    <head>
        <link rel="stylesheet" type="text/css" href="css/styles.css">
        <link rel="stylesheet" type="text/css" href="css/themes.css">
    </head>

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
					<h1 id="pagination">Showing users <span class="num"><?php echo 1 + $offset; ?> - <?php echo mysqli_num_rows($table_result) + $offset; ?></span> of <span class="num"><?php echo $total_users ?></span>
						<span class="page">Page <?php echo $current_page; ?> of <?php echo $total_pages ?></span>
					</h1>
					<div class="pagination-button" style="justify-content: left">
						<button onclick="ChangePage(1)" class="frutiger-tile" <?php if ($current_page >= $total_pages) { echo 'disabled'; } ?> draggable="false">NEXT PAGE</button>
					</div>
				</div>
                <!-- Users table -->
				<table class="frutiger-tile" id="users-table">
					
				</table>
			</div>
            <div class="charts">
				<div class="frutiger-tile stats">
					<h1>Rows per page:
                        <select name="num_of_rows" onchange="UpdateNumOfRows(event)" >
                            <option value="25" <?php if ($num_of_rows == 25): ?>selected<?php endif ?>>25</option>
                            <option value="50" <?php if ($num_of_rows == 50): ?>selected<?php endif ?>>50</option>
                            <option value="75" <?php if ($num_of_rows == 75): ?>selected<?php endif ?>>75</option>
                            <option value="100" <?php if ($num_of_rows == 100): ?>selected<?php endif ?>>100</option>
                            <option value="250" <?php if ($num_of_rows == 250): ?>selected<?php endif ?>>250</option>
                            <option value="500" <?php if ($num_of_rows == 500): ?>selected<?php endif ?>>500</option>
                            <option value="1000" <?php if ($num_of_rows == 1000): ?>selected<?php endif ?>>1000</option>
                        </select>
					</h1>
				</div>
                <!-- Adding user menu -->
				<?php if ($user_access_level == -1): ?>
				<div class="table-buttons">
					<button class="frutiger-tile" id="table-add-user" onclick="ShowAddUser()">
						Add User
					</button>
					<div class="frutiger-tile add-container hidden" id="add-user-container">
						<h1>Add User</h1>
						<form id="add-user">
							<label>Username: <input form="add-user" class="frutiger-inset-tile" name="user_username" data-default-value="" required /></label>
							<label>Password: <input form="add-user" class="frutiger-inset-tile" name="user_password" data-default-value="" /></label>
							<label>Display Name: <input form="add-user" class="frutiger-inset-tile" name="user_display_name" data-default-value="" required /></label>
							<label>Access Level: <input form="add-user" class="frutiger-inset-tile" name="user_access_level" type="number" data-default-value="0" required /></label>
						</form>
						<div class="buttons">
							<button class="frutiger-tile continue" type="button" onclick="Post_AddUser()">
								Add
							</button>
							<button class="frutiger-tile cancel" onclick="HideAddUser()">
								Cancel
							</button>
						</div>
					</div>
				</div>
				<?php endif ?>
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

        <!-- Link JavaScript -->
        <script src="js/main.js"></script>
        <script src="js/user_management.js"></script>
        <script src="js/user_editor.js"></script>
    </body>
</html>
