<?php
header('Content-Type: application/json');
include '../../backend/verifica.php';
include '../../backend/conect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';
    $tipo = $_POST['tipoUsuario'] ?? '';

    // Validações
    if (empty($nome) || empty($email) || empty($senha) || empty($confirmar) || empty($tipo)) {
        echo json_encode(['success' => false, 'message' => '⚠️ Preencha todos os campos.', 'type' => 'warning']);
        exit;
    }

    if ($senha !== $confirmar) {
        echo json_encode(['success' => false, 'message' => '⚠️ As senhas não coincidem.', 'type' => 'warning']);
        exit;
    }

    $pdo->beginTransaction();

    try {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        // Verificar se usuário já existe
        $verifica = $pdo->prepare("SELECT id_user FROM users WHERE email = :email OR username = :nome");
        $verifica->execute(['email' => $email, 'nome' => $nome]);

        if ($verifica->rowCount() > 0) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => '❌ E-mail ou Nome de usuário já cadastrado.', 'type' => 'danger']);
            exit;
        }

        // Inserir usuário
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, user_type) VALUES (:nome, :email, :senha, :tipo)");
        $stmt->execute([
            'nome' => $nome,
            'email' => $email,
            'senha' => $senhaHash,
            'tipo' => $tipo
        ]);

        // Se for professor, inserir na tabela teachers
        if ($tipo === 'teacher') {
            $id_user_criado = $pdo->lastInsertId();
            $stmtTeacher = $pdo->prepare("INSERT INTO teachers (id_user) VALUES (:id_user)");
            $stmtTeacher->execute(['id_user' => $id_user_criado]);
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => '✅ Usuário cadastrado com sucesso.', 'type' => 'success']);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => '❌ Erro ao cadastrar usuário.', 'type' => 'danger']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido.', 'type' => 'danger']);
}
?>
