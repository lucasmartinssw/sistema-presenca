<?php
// Start the session at the very beginning of the script

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
    // Consulta o usuário pelo e-mail
    // Selecionamos email, password_hash e user_type para verificação
    $stmt = $pdo->prepare("SELECT id_user, email, password_hash, user_type FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário existe, a senha está correta E o tipo de usuário enviado corresponde ao do banco
    if ($user &&
        $user['email'] === $email &&
        $user['user_type'] === $tipoUsuarioInput // Verifica se o tipo de usuário enviado corresponde ao do banco
    ) {
        // Login bem-sucedido
        // Armazene apenas informações essenciais do usuário logado na sessão
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['tipoUsuario'] = $user['user_type']; // Sempre pegue o tipo de usuário do banco

        if ($user['user_type'] === 'administrator') {
            echo "<script>window.location.href='../sistema-presenca/adm/index.html';</script>";
            exit;
        } else {
            echo "<center>Usuário correto!<br><a href='inicial.php'>Ir para home</a></center>";
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