<?php
	define('ALLOW_ACCESS', true);

    if (session_status() === PHP_SESSION_NONE) {
		session_start();

        // Stop if already logged in
        if (isset($_SESSION['user_id'])) {
            echo "logged in";
            exit();
        }
		
		if (isset($_POST['username']) && isset($_POST['password']) &&
            trim(htmlspecialchars($_POST['username'])) != "" && trim(htmlspecialchars($_POST['password'])) != "") {
            $username = htmlspecialchars($_POST['username']);
            $password = htmlspecialchars($_POST['password']);
            if ($username && $password) {
                include("../../inc/dbconnect.php");

                $stmt = "SELECT * FROM users WHERE user_username = ? and user_password = ?";
                $sql = $dbconnect->prepare($stmt);
                $sql->bind_param('ss', $username, $password);
                $sql->execute();
                $result = $sql->get_result();

                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $_SESSION['user_id'] = $row['user_id'];

                    echo 'success';
                } else {
                    $error = true;
                }
            } else {
                $error = true;
            }
            
            
            if (isset($error)) {
                // Ambiguous invalid message
                echo "error:Invalid username or password.";
            }
        } else {
            if (!isset($_POST['email']) || trim(htmlspecialchars($_POST['email'])) == "")
                echo "error:No email submitted.";

            if (!isset($_POST['password']) || trim(htmlspecialchars($_POST['password'])) == "")
                echo "error:No password submitted.";
        }
    }
?>