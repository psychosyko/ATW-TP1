<?php

  session_start();

  if (!$_SESSION) {
    header('Location: /login/');
  } else {
    header('Location: /home/');
  }




?>
