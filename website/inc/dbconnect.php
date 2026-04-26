<?php
    // Cannot be directly accessed
    if (!defined('ALLOW_ACCESS')) {
        exit('No direct script access allowed');
    }

    $database_hostname = "localhost";
    $database_username = "root";
    $database_password = "";
    $database_name = "php_information_system";

    $dbconnect = mysqli_connect($database_hostname, $database_username, $database_password, $database_name);

    if (!$dbconnect) {
        die( "Unable to connect to the Database. Please try again later.");

    };
?>
