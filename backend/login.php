<?php
// Start the session at the very beginning of the script

session_start();
include 'conect.php';

// Verifica se todos os campos necessários foram enviados
if (
    isset($_POST['email']) && !empty($_POST['email']) &&
    isset($_POST['senha']) && !empty($_POST['senha']) &&
    isset($_POST['tipoUsuario']) && !empty($_POST['tipoUsuario'])
    ) {
        $email = $_POST['email'];
        $password = $_POST['senha'];
        $tipoUsuarioInput = $_POST['tipoUsuario']; // Renamed to avoid confusion with database value

        // Consulta o usuário pelo e-mail, senha e tipo de usuário diretamente
        $stmt = $pdo->prepare("SELECT id_user, email, user_type FROM users WHERE email = :email AND password_hash = :password AND user_type = :user_type");
        $stmt->execute([
            'email' => $email,
            'password' => $password,
            'user_type' => $tipoUsuarioInput
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Login bem-sucedido
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['tipoUsuario'] = $user['user_type'];

            if ($user['user_type'] === 'administrator') {
                echo "<script>window.location.href='../sistema-presenca/adm/index.php';</script>";
                exit;
            } else {
                // Se for professor ou aluno, redireciona para a página correta
                echo "<script>window.location.href='../sistema-presenca/professor/index.php';</script>";
                exit;
            }
        } else {
            // Usuário, senha ou tipo de usuário incorretos
            echo "<center>Usuário, senha ou tipo de usuário incorretos!</center>";
        }
} else {
    // Campos não enviados
    echo "<center>Por favor, forneça o e-mail, senha e tipo de usuário.</center>";
}
?>