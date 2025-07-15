<?php
session_start();
if((!isset ($_SESSION['email']) == true) and (!isset ($_SESSION['tipoUsuario']) == true))
{
  unset($_SESSION['clogin']);
  unset($_SESSION['csenha']);
  header('Location: ../login.html');
  }
?>