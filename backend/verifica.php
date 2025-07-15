<?php
session_start();
if((!isset ($_SESSION['email']) == true) and (!isset ($_SESSION['tipoUsuario']) == true))
{
  unset($_SESSION['email']);
  unset($_SESSION['tipoUsuario']);
  header('Location: ../login.html');
  }
?>