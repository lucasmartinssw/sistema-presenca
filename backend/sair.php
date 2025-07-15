<?php
  session_start();
  session_destroy();
  header('Location: ../sistema-presenca/login.html');
?>