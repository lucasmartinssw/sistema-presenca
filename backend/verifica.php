<?php
session_start();
if((!isset ($_SESSION['email']) == true) )
{
  unset($_SESSION['email']);
  header('location:login.php');
  }
?>