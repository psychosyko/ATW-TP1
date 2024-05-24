<?php

    session_start();
    $error = null;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        include '../conn.php';

        $email = $_POST["email"];
        $password = $_POST["password"];

        $result = $conn->execute_query('SELECT email, password, name, admin, id FROM users WHERE email = ?', [$email]);

        if ($result->num_rows === 0) {
            $error = "User doesn't exist!";
        } else {

            $user = null;

            while ($row = $result->fetch_assoc()) {
				$user = $row;
            }

			$isValid = password_verify($password, $user['password']);

            if ($isValid == false) {
                $error = "Invalid password!";
            } else {
                $_SESSION['email'] = $user['email'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['admin'];
                $_SESSION['id'] = $user['id'];
                header('Location: /home/');
            }
        }

    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="login.module.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="logo.png" alt="Guitar Player">
        </div>
        <div class="login-form">
            <?php
                if ($error != null) {
                    echo $error; 
                }
            ?>
            <h1>Log in</h1>
            <form action="" method="POST">
                <input type="email" id="email" name="email" placeholder="Email" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <button type="submit">Log in</button>
            </form>
            <p class="register">Don't have an account? <a href="/register/">Register</a></p>
        </div>
    </div>
</body>
</html>
