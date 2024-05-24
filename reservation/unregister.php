<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include '../conn.php';

    $id_user = $_SESSION['id'];
    $id_concert = $_POST['id_concert'];

    $conn->execute_query('DELETE FROM orders WHERE id_user = ? AND id_concert = ?', [$id_user, $id_concert]);

    header('Location: /reservation/'); 
    exit();
}
?>
