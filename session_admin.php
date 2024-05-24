<?php

  if (!$_SESSION) {
    header('Location: /login/');
    return;
  }

  if ($_SESSION['role'] <= 0) {
    header('Location: /login/');
    return;
  }

  if (!isset($_SESSION['id'])) {
    header('Location: /login/');
    return;
  }
?>
