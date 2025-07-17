<?php
$pagina_atual = 'index';
include '../../backend/verifica.php'; // Inclui o arquivo de verificação de sessão/autenticação
include '../../backend/conect.php'; // Inclui o arquivo de conexão com o banco de dados

// Query para buscar todas as turmas
try {
    // A consulta não precisa mudar, pois estamos apenas não exibindo o ID
    $stmt = $pdo->query("SELECT id_class, class_name, class_course, class_year FROM Classes ORDER BY class_name ASC");
    $turmas = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Erro ao carregar turmas: " . $e->getMessage();
    $turmas = []; // Garante que $turmas seja um array vazio em caso de erro
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Instituto Federal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="d-flex">
   <nav class="sidebar d-flex flex-column p-3">
  <div class="d-flex align-items-center mb-4">
    <img src="../images/iflogov2.jpg" alt="Logo IF" width="40" class="me-2">
    <span class="fs-5 text-white">Painel IF</span>
  </div>
  <?php include 'menu.php'; ?>
</nav>
    <div class="flex-grow-1">
      <header class="topbar d-flex justify-content-between align-items-center p-3 shadow-sm">
        <h5 class="mb-0">Dashboard</h5>
        <span class="text-muted">Usuário logado</span>
      </header>
      <main class="p-4">
        <div class="card">
          <div class="card-body">
            <h6 class="card-title">Bem-vindo</h6>
            <p class="card-text">Selecione uma opção no menu lateral.</p>
          </div>
        </div>
      </main>
    </div>
  </div>
</body>
</html>