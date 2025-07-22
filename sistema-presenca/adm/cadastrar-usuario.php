<?php
// Processamento do formulário
$msg = "";
$classe = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $pagina_atual = 'cadastrar-usuario.php';
    include '../../backend/verifica.php'; // Inclui o arquivo de verificação de sessão/autenticação
    include '../../backend/conect.php'; // Inclui o arquivo de conexão com o banco de dados


  $nome = $_POST['nome'] ?? '';
  $email = $_POST['email'] ?? '';
  $senha = $_POST['senha'] ?? '';
  $confirmar = $_POST['confirmar'] ?? '';
  $tipo = $_POST['tipoUsuario'] ?? '';

  if (empty($nome) || empty($email) || empty($senha) || empty($confirmar) || empty($tipo)) {
    $msg = "⚠️ Preencha todos os campos.";
    $classe = "warning";
  } elseif ($senha !== $confirmar) {
    $msg = "⚠️ As senhas não coincidem.";
    $classe = "warning";
  } else {
    try {
      $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

      // Verifica se o e-mail já existe
      $verifica = $pdo->prepare("SELECT id_user FROM users WHERE email = :email");
      $verifica->execute(['email' => $email]);

      if ($verifica->rowCount() > 0) {
        $msg = "❌ E-mail já cadastrado.";
        $classe = "danger";
      } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, user_type) VALUES (:nome, :email, :senha, :tipo)");
        $stmt->execute([
          'nome' => $nome,
          'email' => $email,
          'senha' => $senhaHash,
          'tipo' => $tipo
        ]);
        $msg = "✅ Usuário cadastrado com sucesso.";
        $classe = "success";
        header("Location: index.php"); // Redireciona após o cadastro
      }
    } catch (Exception $e) {
      $msg = "❌ Erro ao cadastrar usuário.";
      $classe = "danger";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <title>Cadastrar Usuário - Instituto Federal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="style.css">
</head>
<body class="bg-image d-flex justify-content-center align-items-center vh-100">

  <div class="card p-4 login-card shadow-lg" style="max-width: 500px; width: 100%;">
    <h3 class="text-center mb-3 text-secondary d-flex align-items-center justify-content-center gap-2">
      <img src="../images/iflogov2.jpg" alt="Logo IF" width="40" height="40" />
      Cadastrar Novo Usuário
    </h3>

    <?php if (!empty($msg)): ?>
      <div class="alert alert-<?= $classe ?>" role="alert"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label for="nome" class="form-label text-secondary">Nome Completo</label>
        <input type="text" class="form-control" id="nome" name="nome" required />
      </div>

      <div class="mb-3">
        <label for="email" class="form-label text-secondary">E-mail institucional</label>
        <input type="email" class="form-control" id="email" name="email" required />
      </div>

      <div class="mb-3">
        <label for="senha" class="form-label text-secondary">Senha</label>
        <input type="password" class="form-control" id="senha" name="senha" required />
      </div>

      <div class="mb-3">
        <label for="confirmar" class="form-label text-secondary">Confirmar Senha</label>
        <input type="password" class="form-control" id="confirmar" name="confirmar" required />
      </div>

      <div class="mb-3">
        <label class="form-label text-secondary">Tipo de Usuário</label>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="tipoUsuario" id="admin" value="administrator" required />
          <label class="form-check-label text-secondary" for="admin">Administrador</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="tipoUsuario" id="professor" value="teacher" required />
          <label class="form-check-label text-secondary" for="professor">Professor</label>
        </div>
      </div>

      <div class="d-grid mt-3">
        <button type="submit" class="btn btn-success">Cadastrar</button>
      </div>
    </form>
  </div>

</body>
</html>
