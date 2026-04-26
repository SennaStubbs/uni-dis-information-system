<?php
	define('ALLOW_ACCESS', true);

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    include($_SERVER["DOCUMENT_ROOT"] . "/information_system/website/inc/dbconnect.php");
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
            <div class="error-details frutiger-glossy">
                <div class="text">
                    <h1>Sorry, there was an issue reaching this page!</h1>
                    <p>This page either does not exist or is currently inaccessible.</p>
                    <a class="frutiger-tile" onclick="history.back()">Return to previous page</a>
                </div>
                <img src="/information_system/website/images/icons/dfrgui_141-17.png" draggable="false">
            </div>
        </main>

        <!-- Link JavaScript -->
        <script src="js/main.js"></script>
    </body>
</html>