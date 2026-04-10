<!-- ALL IMAGE ASSETS OBTAINED FROM https://frutigeraeroarchive.org/ -->

<?php
	define('ALLOW_ACCESS', true);

	// Need a user account to view
	if(!isset($_COOKIE['user'])) {
		header('location: /chris_blue/index.php');
	}
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    include ("../chris_blue/inc/dbconnect.php");
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/styles.css">
        <link rel="stylesheet" type="text/css" href="css/themes.css">
    </head>

	<body>
		<?php include("../chris_blue/inc/navigation.php"); ?>

        <div class="background"></div>
        <main>
            <div class="frutiger-tile user-title">
                Hello, <span><?php echo $user_display_name ?></span>!
            </div>
            <div class="user-options frutiger-glossy">
                <a class="frutiger-tile" href="dashboard.php" draggable="false">
                    Dashboard
                </a>
                <a class="frutiger-tile" href="items_table.php" draggable="false">
                    View Items
                </a>
                <?php if ($user_access_level == -1): ?>
                <a class="frutiger-tile" href="user_management.php" draggable="false">
                    User Management
                </a>
                <?php endif ?>
            </div>
        </main>

		<!-- Link JavaScript -->
        <script src="js/main.js"></script>
    </body>
</html>
