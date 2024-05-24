<?php

    session_start();

    include '../../session_admin.php';

    include '../../conn.php';

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        if (isset($_POST['admin'])) {
            $id = $_POST['admin'];

            $conn->execute_query('UPDATE users set admin = 1 WHERE id = ?', [$id]);
        }

        if (isset($_POST['delete'])) {
            $id = $_POST['delete'];

            if ($id != $_SESSION['id']) {
                $conn->execute_query('DELETE FROM orders WHERE id_user = ?', [$id]);
                $conn->execute_query('DELETE FROM users WHERE id = ?', [$id]);
            }
        }

    }

    $users = $conn->execute_query('SELECT id, name, email, admin, image_location FROM users');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <link rel="stylesheet" href="userList.module.css">
</head>
<body>
    <div class="user-container">
        <button class="back-button" onclick="redirectToHomepage()">
            <img src="back_arrow.png" alt="Back" class="back_arrow" />
        </button>
        <header class="user-header">
            <h1>User List</h1>
        </header>
        <div class="user-table-container">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Admin?</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                        while ($user = $users->fetch_assoc()) {

                            $is_admin = $user['admin'] === 1 ? 'Yes' : 'No';

                            if ($user['id'] == $_SESSION['id']) {
                                echo '
                                    <tr>
                                        <td><img src="'.$user['image_location'].'" alt="User Photo" class="user-photo"></td>
                                        <td>'.$user['name'].'</td>
                                        <td>'.$is_admin.'</td>
                                        <td>'.$user['email'].'</td>
                                        <td>

                                        </td>
                                    </tr>
                                ';
                            } else {
                                echo '
                                    <tr>
                                        <td><img src="'.$user['image_location'].'" alt="User Photo" class="user-photo"></td>
                                        <td>'.$user['name'].'</td>
                                        <td>'.$is_admin.'</td>
                                        <td>'.$user['email'].'</td>
                                        <td>
                                            <form action="" method="POST">
                                                <input hidden type="text" name="delete" id="delete" value="'.$user['id'].'" />
                                                <button class="user-action-button remove-user">Remove</button>
                                            </form>

                                            <form action="" method="POST">
                                                <input hidden type="text" name="admin" id="admin" value="'.$user['id'].'" />
                                                <button class="user-action-button make-admin">Make Admin</button>
                                            </form>
                                        </td>
                                    </tr>
                                ';
                            }

                        }

                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        function redirectToHomepage() {
            window.location.href = '../../home/';
        }
    </script>
</body>
</html>
