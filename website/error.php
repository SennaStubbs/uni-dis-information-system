<?php
	define('ALLOW_ACCESS', true);
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
            <div class="error-details frutiger-glossy">
                <div class="text">
                    <h1>Sorry, there was an issue reaching this page!</h1>
                    <p>This page either does not exist or is currently inaccessible.</p>
                    <a class="frutiger-tile" onclick="history.back()">Return to previous page</a>
                </div>
                <img src="/chris_blue/images/icons/dfrgui_141-17.png" draggable="false">
            </div>
        </main>

        <!-- Link JavaScript -->
        <script src="js/main.js"></script>
    </body>
</html>