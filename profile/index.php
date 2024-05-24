<?php
    session_start();

    include '../session.php';

    include '../conn.php';

    $message = null;

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $username = $_POST['name'];
        $email = $_POST['email'];

        $conn->execute_query('UPDATE users set name = ?, email = ? WHERE id = ?', [
            $username,
            $email,
            $_SESSION['id']
        ]);

        if ($_POST['password'] != "") {

            if ($_POST['password'] == $_POST['confirm-password']) {
                $password =  password_hash($_POST["password"], PASSWORD_DEFAULT);

                $conn->execute_query('UPDATE users set password = ? WHERE id = ?', [
                    $password,
                    $_SESSION['id']
                ]);

                $message  = "Password alterada com sucesso";

            } else {
                $message = "Passwords tem de ser iguais";
            }
        }

        if ($_FILES['image']['size'] > 0) {
            $uploaddir = '../uploads/users/';
            $uploadfile = $uploaddir . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile);

            $conn->execute_query('UPDATE users set image_location = ? WHERE id = ?', [
                '/uploads/users/' . basename($_FILES['image']['name']),
                $_SESSION['id']
            ]);
        }
    }

    $user = $conn->execute_query('SELECT * FROM users WHERE id = ?', [$_SESSION['id']])->fetch_assoc();
    $orders = $conn->execute_query('SELECT COUNT(*) as count FROM orders WHERE id_user = ?', [$_SESSION['id']])->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="profile.module.css">
</head>
<body>
    <div class="profile-container">
        <header class="profile-header">
            <button class="back-button" id="back">
                <img src="back_arrow.png" alt="Back" />
            </button>
            <div class="user-details">
                <div class="name-photo">
                    <img src="<?php echo $user['image_location']; ?>" alt="User Profile" class="profile-pic">
                    <h1><?php echo $user['name'] ?></h1>
                </div>
                <div class="reservation-link">
                    <i class="icon">&#128197; <?php echo $orders['count']; ?> Reservation/s</i>
                </div>
            </div>
        </header>
            <?php
                if ($message != null) {
                    echo $message; 
                }
            ?>
        <form class="profile-form" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" placeholder="Name" value="<?php echo $user['name'] ?>">
            </div>
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" placeholder="E-mail" value="<?php echo $user['email'] ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Password">
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm password</label>
                <input type="password" name="confirm-password" id="confirm-password" placeholder="Confirm password">
            </div>
            <div class="upload-btn-wrapper">
                <label>Profile Picture</label>
                <label for="image" class="btn">Load an image</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" class="save-button">Save changes</button>
        </form>
    </div>
</body>
</html>


<script>
    document.getElementById('back').addEventListener("click", (e) => {
        window.location.href = "/home/";
    })
</script>
