<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        define('ALLOW_ACCESS', true);
    }

    include($_SERVER["DOCUMENT_ROOT"] . "/information_system/website/inc/dbconnect.php");

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Need a user account to view
    if(!isset($_SESSION['user_id'])) {
        echo 'no valid session';
    }

    include($_SERVER["DOCUMENT_ROOT"] . "/information_system/website/operations/user/get_user.php");

    // Need an administrator account to perform
    if($user_access_level != -1) {
        echo 'cannot perform';
    }

    if (!isset($table_result)) {
        $num_of_rows = isset($_COOKIE['users_table_num_of_rows']) ? filter_var($_COOKIE['users_table_num_of_rows'], FILTER_SANITIZE_NUMBER_INT) : 25;
        
        // Get total number of users
        $total_sql = "SELECT COUNT(*) AS count FROM users";
        $stmt = $dbconnect -> prepare($total_sql);
        $stmt -> execute();
        $result = $stmt -> get_result();
        $total_users = mysqli_fetch_assoc($result)['count'];


        // Pagination
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $current_page = max(isset($_POST['page']) ? (int)$_POST['page'] : 1, 1);
        } else {
            $current_page = max(isset($_GET['page']) ? (int)$_GET['page'] : 1, 1);
        }

        $offset = ($current_page - 1) * $num_of_rows;
        $total_pages = ceil($total_users / $num_of_rows);

        // Execute
        $table_sql = "SELECT * FROM users LIMIT ? OFFSET ?";
        $stmt = $dbconnect -> prepare($table_sql);
        $stmt -> bind_param('ii', $num_of_rows, $offset);
        $stmt -> execute();
        $table_result = $stmt -> get_result();
    }
    
    // Send back HTML
    if ($_SERVER['REQUEST_METHOD'] == 'POST' || !isset($initialise) || $initialise == false) { ?>
<table class="frutiger-tile" id="users-table">
    <tr>
        <th style="text-align: center">User<br>Id</th>
        <th>Username</th>
        <th style="text-align: center">User Password</th>
        <th style="text-align: center">User Display Name</th>
        <th style="text-align: center">User Access Level</th>
        <?php if ($user_access_level == -1): ?>
        <th style="text-align: center">Actions</th>
        <?php endif ?>
    </tr>
    <!-- Getting and adding user entries -->
    <?php
        if (mysqli_num_rows($table_result) > 0) {
            while ($row = mysqli_fetch_assoc($table_result)) { ?>
    <tr id="user_row_<?php echo $row['user_id'] ?>">
        <td style="text-align: center"><?php echo $row['user_id'] ?></td>
        <td class="user-username user-display"><?php echo $row['user_username'] ?></td>
        <td class="user-password user-display"><?php echo $row['user_password'] ?></td>
        <td class="user-display-name user-display"><?php echo $row['user_display_name'] ?></td>
        <td class="user-access-level user-display" style="text-align: center"><?php echo $row['user_access_level'] ?></td>

        <!-- Form for editing -->
        <form class="edit" id="edit_user_<?php echo $row['user_id'] ?>"></form>
        <!-- Edit inputs -->
        <td class="user-id hidden" style="text-align: center">
            <input value="<?php echo $row['user_id'] ?>" name="user_id" form="edit_user_<?php echo $row['user_id'] ?>" required>
        </td>
        <td class="user-username edit-input hidden">
            <input class="frutiger-inset-tile" value="<?php echo $row['user_username'] ?>" name="user_username" form="edit_user_<?php echo $row['user_id'] ?>" required>
        </td>
        <td class="user-password edit-input hidden">
            <input class="frutiger-inset-tile" value="<?php echo $row['user_password'] ?>" name="user_password" form="edit_user_<?php echo $row['user_id'] ?>">
        </td>
        <td class="user-display-name edit-input hidden">
            <input class="frutiger-inset-tile" value="<?php echo $row['user_display_name'] ?>" name="user_display_name" form="edit_user_<?php echo $row['user_id'] ?>" required>
        </td>
        <td class="user-access-level edit-input hidden">
            <input class="frutiger-inset-tile" value="<?php echo $row['user_access_level'] ?>" name="user_access_level" form="edit_user_<?php echo $row['user_id'] ?>" type="number" style="text-align: center" required>
        </td>

        <!-- Admin actions -->
        <?php if ($user_access_level == -1): ?>
        <!-- Editing and deleting row -->
        <td class="admin-actions user-display" style="text-align: center">
            <button style="background-image: url('images/icons/edit.png')" title="Edit User"
                onclick="EditUser(<?php echo $row['user_id'] ?>, {
                    'username': '<?php echo $row['user_username'] ?>',
                    'password': '<?php echo $row['user_password'] ?>',
                    'display_name': '<?php echo $row['user_display_name'] ?>',
                    'access_level': <?php echo $row['user_access_level'] ?>,
                })">
            </button>
            <button style="background-image: url('images/icons/delete.png')" title="Delete User"
                onclick="ConfirmationPopup('Are you sure?', 'This will permanently delete User ID <?php echo $row['user_id'] ?> (\'<?php echo $row['user_username'] ?>\') from the database.', 'delete_user', {'userId': <?php echo $row['user_id'] ?>})">
            </button>
        </td>
        <!-- Confirming or canceling edits -->
            <td class="admin-actions edit-input hidden" style="text-align: center">
            <button style="background-image: url('images/icons/confirm.png')" title="Submit Edits"
                onclick="ConfirmationPopup('Are you sure?', 'This will overwrite the existing data for User ID <?php echo $row['user_id'] ?>.', 'edit_user', {'userId': <?php echo $row['user_id'] ?>})">
            </button>
            <button style="background-image: url('images/icons/cancel.png')" title="Cancel Edits"
                onclick="ConfirmationPopup('Are you sure?', 'This will revert any changes made to User Id <?php echo $row['user_id'] ?>.', 'cancel_edit_user', {'userId': <?php echo $row['user_id'] ?>})">
            </button>
        </td>
        <?php endif ?>
    </tr>
            <?php }
        }
    ?>
</table>
    <?php }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        echo '!!!pagination: {
            "total_pages": ' . $total_pages . ',
            "current_users": ' . mysqli_num_rows($table_result) . ',
            "total_users": ' . $total_users . ',
            "offset": ' . $offset . '
        }';
    }

    $initialise = false;
?>