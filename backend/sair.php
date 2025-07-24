<?php
session_start();

// Limpa todas as variáveis de sessão
session_unset();

// Destrói a sessão
session_destroy();

// Remove o cookie de sessão se existir
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redireciona para a página de login com parâmetro indicando logout
header('Location: ../sistema-presenca/login.html?erro=3');
exit();
?>