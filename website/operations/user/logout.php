<?php
    setcookie("user", '', time() - (60), "/", NULL, NULL, true);
    header('location: /chris_blue/index.php');
?>