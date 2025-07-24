<?php
session_start();

// Verifica se as variáveis de sessão existem e não estão vazias
if (!isset($_SESSION['email']) || empty($_SESSION['email']) || 
    !isset($_SESSION['tipoUsuario']) || empty($_SESSION['tipoUsuario'])) {
    
    // Limpa todas as variáveis de sessão
    session_unset();
    session_destroy();
    
    // Redireciona para a página de login
    header('Location: ../../sistema-presenca/login.html');
    exit();
}

// Verificação adicional para administradores nas páginas administrativas
$current_path = $_SERVER['REQUEST_URI'];
if (strpos($current_path, '/adm/') !== false && $_SESSION['tipoUsuario'] !== 'administrator') {
    // Se não for administrador tentando acessar área administrativa
    session_unset();
    session_destroy();
    header('Location: ../../sistema-presenca/login.html?erro=2');
    exit();
}
?>