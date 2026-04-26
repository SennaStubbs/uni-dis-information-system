<?php
    // Cannot be directly accessed
    if (!defined('ALLOW_ACCESS')) {
        exit('No direct script access allowed');
    }

    include("operations/user/get_user.php");
?>

<nav class="frutiger-menu">
    <div class="container">
        <div class="theme">
            <label>Theme
                <select id="theme">
                    <option value="red">Red</option>
                    <option value="orange">Orange</option>
                    <option value="yellow">Yellow</option>
                    <option value="green" selected>Green</option>
                    <option value="blue">Blue</option>
                    <option value="white">White</option>
                    <option value="black">Black</option>
                    <option value="pug">Pug</option>
                </select>
            </label>
        </div>
        <?php if (isset($_SESSION['user_id'])): ?>
        <a href="user.php" class="user frutiger-tile" draggable="false">Logged in as <span><?php echo $user_display_name ?></span></a>
        <form action="/information_system/website/operations/user/logout.php" method="post" id="logout">
            <button class="logout frutiger-tile" form="logout">
                <?php if ($_SESSION['user_id'] == 3): ?>
                <img src="/information_system/website/images/icons/chris_blue.jpg" id="chris_blue" draggable="false">
                <?php else: ?>
                <img src="/information_system/website/images/icons/gameux_212-9.png" draggable="false">
                <?php endif ?>
            </button>
        </form>
        <?php endif ?>
    </div>
</nav>