<?php

session_start();

include '../session.php';
include '../conn.php';

$id_user = $_SESSION['id'];

$concerts = $conn->execute_query("SELECT c.*, t.name as type FROM orders o LEFT JOIN concerts c ON o.id_concert = c.id LEFT JOIN types t ON t.id = c.id_type WHERE o.id_user = ?", [$_SESSION['id']]);

$events = $conn->execute_query('
SELECT concerts.id, concerts.name, concerts.date, concerts.time, concerts.location 
FROM concerts 
INNER JOIN orders ON concerts.id = orders.id_concert 
WHERE orders.id_user = ?', [$id_user])->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latest Reservations</title>
    <link rel="stylesheet" href="reservation.module.css">
</head>

<body>
    <div class="reservation-container">
        <header class="reservation-header">
            <button class="back-button" onclick="redirectToHomepage()">
                <img src="back_arrow.png" alt="Back" />
            </button>
            <h1>Latest reservations</h1>
        </header>
        <div class="reservation-list">

            <?php
                while ($concert = $concerts->fetch_assoc()) {
                    echo '
                        <div class="reservation-item">
                            <img src="'.$concert['image_location'].'" alt="Concert Banner" class="concert-banner">
                            <div class="reservation-info">
                                <h2>'.$concert['name'].'</h2>
                                <p>'.$concert['location'].'</p>
                                <p>'.$concert['date'].' | '.$concert['time'].'</p>
                                <span>$'.$concert['price'].'</span>
                            </div>
                            <button class="genre-button">'.$concert['type'].'</button>
                            <form action="unregister.php" method="POST" class="container">
                                <input type="hidden" name="id_concert" value="'.$concert['id'].'">
                                <button type="submit" class="unregister-button">Withdraw Registration</button>
                            </form>
                        </div>
                    ';
                }
            ?>
        </div>
    </div>
    <script>
        function redirectToHomepage() {
            window.location.href = '../home/';
        }
    </script>
</body>

</html>
