<!DOCTYPE html>
<html>
    <?php
        define('ALLOW_ACCESS', true);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Redirect user if logged in
        if(isset($_SESSION['user_id'])) {
            header('location: user');
            exit();
        }
    
    include($_SERVER["DOCUMENT_ROOT"] . "/information_system/website/inc/dbconnect.php");
    ?>

    <head>
        <link rel="stylesheet" type="text/css" href="css/main.css">
        <link rel="stylesheet" type="text/css" href="css/log-in.css">
        <link rel="stylesheet" type="text/css" href="css/themes.css">
    </head>

    <body>
		<?php include($_SERVER["DOCUMENT_ROOT"] . "/information_system/website/inc/navigation.php"); ?>

        <div class="background"></div>
        <main>
			<div class="login frutiger-tile">
                <form id="log-in">
                    <label><span>Username</span>
                        <input type="text" name="username" class="frutiger-inset-tile" required>
                    </label>
                    <label><span>Password</span>
                        <input type="password" name="password" class="frutiger-inset-tile" required>
                    </label>
                    <p id="error-message" class="error hidden"></p>
                    <button type="button" class="frutiger-tile login-button" form="log-in" onclick="LogIn(event)">Log In</button>
                </form>
            </div>
        </main>


        <!-- Link JavaScript -->
        <script src="js/main.js"></script>
        <script src="js/log-in.js"></script>
    </body>
</html>
