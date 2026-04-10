<!DOCTYPE html>
<html>
    <?php
        define('ALLOW_ACCESS', true);

        // Redirect user if logged in
        if(isset($_COOKIE['user'])) {
            header('location: /chris_blue/user.php');
            exit();
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        include("../chris_blue/inc/dbconnect.php");

        // Log in variables
        $invalid_credentials = false;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Logging in
            // Checks for username, then checks for the hashed password later
            if(isset($_POST['username']) && isset($_POST['password'])) {
                $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
                $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

                // Prepared statement
                $login_sql = "SELECT * FROM users WHERE user_username = ? AND user_password = ?";
                $login_stmt = $dbconnect->prepare($login_sql);
                $login_stmt->bind_param('ss', $username, $password);
                $login_stmt->execute();
                $login_result = $login_stmt->get_result();
                if (mysqli_num_rows($login_result) > 0) {
                    while($row = mysqli_fetch_assoc($login_result)) {
                        setcookie("user", $row["user_username"], time() + ((60 * 60 * 24) * 1), "/", NULL, NULL, true);
                        header('location: /chris_blue/user.php');
                        // $hashed_password = $row['password_hash'];

                        // // Check if inputted password matches the stored password hash
                        // if (password_verify($password, $hashed_password)) {
                            // Cookies
                        // Expiry equation = (seconds in a day) * number of days

                        // header("Location: items_table.php");
                        // }
                        // else {
                        // 	// Ambiguous invalid message
                        // 	echo "<p>Enter a valid username and password</p>";
                        // }
                    }
                }
                else
                {
                    $invalid_credentials = true;
                }
            }
            else {
                $username = FALSE;
                $password = FALSE;
            }
        }
    ?>

    <head>
        <link rel="stylesheet" type="text/css" href="css/styles.css">
        <link rel="stylesheet" type="text/css" href="css/themes.css">
    </head>

    <!-- <body class="<?php echo $theme ?>"> -->
    <body>
		<?php include("../chris_blue/inc/navigation.php"); ?>

        <div class="background"></div>
        <main>
			<div class="login frutiger-tile">
                <form action="index.php" method="post">
                    <label><span>Username</span>
                        <input type="text" name="username" class="frutiger-inset-tile" required>
                    </label>
                    <label><span>Password</span>
                        <input type="password" name="password" class="frutiger-inset-tile" required>
                    </label>
                    <?php if ($invalid_credentials == true): ?>
                    <p class="error">Invalid credentials entered.</p>
                    <?php endif ?>
                    <button type="submit" class="frutiger-tile login-button">Log In</button>
                </form>
            </div>
        </main>


        <!-- Link JavaScript -->
        <script src="js/main.js"></script>
    </body>
</html>
