<!DOCTYPE html>
<html>
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

        include ("inc/dbconnect.php");
        include("operations/user/get_user.php");

        // Need a user account with the correct access level to view
        if($user_access_level != -1) {
            header('location: index.php');
            exit();
        }

        // Getting session values
        $num_of_rows = isset($_SESSION['num_of_rows']) ? filter_var($_SESSION['num_of_rows'], FILTER_SANITIZE_NUMBER_INT) : 25;


        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['num_of_rows'])) {
                $num_of_rows = filter_var($_POST['num_of_rows'], FILTER_SANITIZE_NUMBER_INT);
                $_SESSION['num_of_rows'] = $num_of_rows;
            }
        }

        // Get total number of users
        $sql = "SELECT COUNT(*) as count FROM users";
        $stmt = $dbconnect -> prepare($sql);
        $stmt -> execute();
        $result = $stmt -> get_result();
        $total_users = mysqli_fetch_assoc($result)['count'];

        // Pagination
        $current_page = max(isset($_GET['page']) ? (int)$_GET['page'] : 1, 1);
        $offset = ($current_page - 1) * $num_of_rows;
        $total_pages = ceil($total_users / $num_of_rows);

        // Prepared statement
        $table_sql = "SELECT * FROM users LIMIT ? OFFSET ?";
        $stmt = $dbconnect -> prepare($table_sql);
        $stmt -> bind_param("ii", $num_of_rows, $offset);
        $stmt -> execute();
        $table_limit_result = $stmt -> get_result();
    ?>

    <head>
        <link rel="stylesheet" type="text/css" href="css/styles.css">
        <link rel="stylesheet" type="text/css" href="css/themes.css">
    </head>

    <body>
		<?php include("inc/navigation.php"); ?>

        <div class="background"></div>

        <main>
            <div class="table">
				<div class="frutiger-tile pagination">
					<div class="pagination-button" style="justify-content: right">
						<a <?php if ($current_page > 1) { echo 'href="?page=' . $current_page - 1 . '"'; } ?> class="frutiger-tile <?php if ($current_page <= 1) { echo 'disabled'; } ?>" draggable="false">PREVIOUS PAGE</a>
					</div>
					<h1>Showing users <span class="num"><?php echo 1 + $offset; ?> - <?php echo mysqli_num_rows($table_limit_result) + $offset; ?></span> of <span class="num"><?php echo $total_users ?></span>
						<span class="page">Page <?php echo $current_page; ?> of <?php echo $total_pages ?></span>
					</h1>
					<div class="pagination-button" style="justify-content: left">
						<a <?php if ($current_page < $total_pages) { echo 'href="?page=' . $current_page + 1 . '"'; } ?> class="frutiger-tile <?php if ($current_page >= $total_pages) { echo 'disabled'; } ?>" draggable="false">NEXT PAGE</a>
					</div>
				</div>
				<table class="frutiger-tile">
					<tr>
						<th style="text-align: center">User<br>Id</th>
						<th>Username</th>
						<th style="text-align: center">User (Hashed) Password</th>
						<th style="text-align: center">User Display Name</th>
                        <th style="text-align: center">User Access Level</th>
						<?php if ($user_access_level == -1): ?>
						<th style="text-align: center">Actions</th>
						<?php endif ?>
					</tr>
					<!-- Getting and adding item entries -->
					<?php
						if (mysqli_num_rows($table_limit_result) > 0) {
							while ($row = mysqli_fetch_assoc($table_limit_result)) { ?>
							<tr>
								<td style="text-align: center"><?php echo $row['user_id'] ?></td>
								<td><?php echo $row['user_username'] ?></td>
								<td><?php echo $row['user_password'] ?></td>
                                <td><?php echo $row['user_display_name'] ?></td>
								<td style="text-align: center"><?php echo $row['user_access_level'] ?></td>
								<?php if ($user_access_level == -1): ?>
								<td style="text-align: center"></td>
								<?php endif ?>
							</tr>
							<?php }
						}
					?>
				</table>
			</div>
            <div class="charts">
				<div class="frutiger-tile stats">
					<h1>Rows per page: <form action="user_management.php" method="post" id="rows" style="display: inline">
							<select name="num_of_rows" form="rows" onchange="this.form.submit()" >
								<option value="25" <?php if ($num_of_rows == 25): ?>selected<?php endif ?>>25</option>
								<option value="50" <?php if ($num_of_rows == 50): ?>selected<?php endif ?>>50</option>
								<option value="75" <?php if ($num_of_rows == 75): ?>selected<?php endif ?>>75</option>
								<option value="100" <?php if ($num_of_rows == 100): ?>selected<?php endif ?>>100</option>
								<option value="250" <?php if ($num_of_rows == 250): ?>selected<?php endif ?>>250</option>
								<option value="500" <?php if ($num_of_rows == 500): ?>selected<?php endif ?>>500</option>
								<option value="1000" <?php if ($num_of_rows == 1000): ?>selected<?php endif ?>>1000</option>
							</select>
						</form>
					</h1>
				</div>
			</div>
        </main>


        <!-- Link JavaScript -->
        <script src="js/main.js"></script>
    </body>
</html>
