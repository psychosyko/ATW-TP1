<?php

  if ($_SERVER["REQUEST_METHOD"] === "GET") {
    session_start();

    include '../../session_admin.php';

    include '../../conn.php';

    if (isset($_GET['id'])) {
      $id = $_GET['id'];

      $result = $conn->execute_query("SELECT u.name, u.email, u.admin FROM orders o LEFT JOIN users u ON o.id_user = u.id WHERE o.id_concert = ?", [$id]);
      $users = array();

      while ($user = $result->fetch_assoc()) { array_push($users, $user); }

      echo json_encode($users);
    }
  }
?>
