<?php

  if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    session_start();

    include '../../session_admin.php';

    include '../../conn.php';

    if (isset($_GET['id'])) {
      $id = $_GET['id'];

      $count = $conn->execute_query("SELECT count(*) as count FROM orders WHERE id_concert = ?", [$id])->fetch_assoc();

      if ($count['count'] > 0) {
        http_response_code(400);
        echo "There are already bookings associated with this event";
        return;
      }

      $conn->execute_query('DELETE FROM concerts WHERE id = ?', [$id]);
      http_response_code(200);
    }
  }
?>
