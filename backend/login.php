<?php
session_start();
include 'conect.php';

// Verifica se os campos foram enviados
if (
    isset($_POST['email']) && !empty($_POST['email']) &&
    isset($_POST['senha']) && !empty($_POST['senha']) &&
    isset($_POST['tipoUsuario']) && !empty($_POST['tipoUsuario'])
) {
    $email = $_POST['email'];
    $senhaDigitada = $_POST['senha'];
    $tipoUsuarioInput = $_POST['tipoUsuario'];

    // Busca o usuário pelo e-mail
    $stmt = $pdo->prepare("SELECT id_user, email, password_hash, user_type FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário existe, o tipo está certo e a senha confere com o hash
    if ($user && $user['user_type'] === $tipoUsuarioInput && password_verify($senhaDigitada, $user['password_hash'])) {
        // Login bem-sucedido
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['tipoUsuario'] = $user['user_type'];

        if ($user['user_type'] === 'administrator') {
            echo "<script>window.location.href='../sistema-presenca/adm/index.php';</script>";
            exit;
        } else {
            echo "<script>window.location.href='../sistema-presenca/professor/index.php';</script>";
            exit;
        }
    } else {
        // Dados incorretos
        header("Location: ../sistema-presenca/login.html?erro=1");
        exit;
    }
} else {
    // Campos faltando
    header("Location: ../sistema-presenca/login.html?erro=1");
    exit;
}
?>
