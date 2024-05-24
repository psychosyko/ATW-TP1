<?php

    session_start();
    $error = null;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        include '../conn.php';

        $name = $_POST["name"];
        $email = $_POST["email"];
        $password =  password_hash($_POST["password"], PASSWORD_DEFAULT);

        $count_user = $conn->execute_query('SELECT id FROM users WHERE email = ?', [$email]);


        if ($count_user->num_rows > 0) {
            $error = "Already exist account width this email!";
        } else {

            $uploaddir = '../uploads/users/';
            $uploadfile = $uploaddir . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile);

            $conn->execute_query('INSERT INTO users (name, email, password, image_location) VALUES(?,?,?,?)', [
                $name,
                $email,
                $password,
                '/uploads/users/' . basename($_FILES['image']['name'])
            ]);

            header('Location: /login/');
        }

    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <link rel="stylesheet" href="register.module.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="logo.png" alt="Logo">
        </div>
        <div class="login-form">
            <?php
                if ($error != null) {
                    echo $error; 
                }
            ?>
            <h1>Let's register</h1>
            <form action="" method="POST" enctype="multipart/form-data">
                <label>Name</label>
                <input type="text" id="name" name="name" placeholder="Name" required>
                <label>E-mail</label>
                <input type="email" id="email" name="email" placeholder="E-mail" required>
                <label>Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <div class="upload-btn-wrapper">
                    <label>Profile Picture</label>
                    <label for="image" class="btn">Load an image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
                <button type="submit">Sign up</button>
            </form>
            <p class="register">Already have an account? <a href="/login/">Log in</a></p>
        </div>
    </div>
    <script>
        function toggleAdmin(choice) {
            console.log("Admin: " + choice);
        }
    </script>
</body>
</html>
