<?php
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
  <title>Turmas - Instituto Federal</title>
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
      <ul class="nav nav-pills flex-column">
        <li class="nav-item"><a href="index.html" class="nav-link text-white">Dashboard</a></li>
        <li class="nav-item"><a href="turmas.php" class="nav-link text-white active">Turmas</a></li>
        <li class="nav-item"><a href="professores.html" class="nav-link text-white">Professores</a></li>
        <li class="nav-item"><a href="alunos.html" class="nav-link text-white">Alunos</a></li>
        <li class="nav-item"><a href="materias.html" class="nav-link text-white">Matérias</a></li>
        <li class="nav-item"><a href="relatorios.html" class="nav-link text-white">Relatórios</a></li>
        <li class="nav-item mt-3"><a href="#" class="nav-link text-danger">Sair</a></li>
      </ul>
    </nav>
    <div class="flex-grow-1">
      <header class="topbar d-flex justify-content-between align-items-center p-3 shadow-sm">
        <h5 class="mb-0">Gerenciar Turmas</h5>
        <button class="btn btn-success">Cadastrar Nova Turma</button>
      </header>
      <main class="p-4">
        <div class="card">
          <div class="card-body">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Nome</th>
                  <th>Curso</th>
                  <th>Ano</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($turmas)): ?>
                    <tr>
                        <td colspan="4" class="text-center">Nenhuma turma cadastrada.</td> </tr>
                <?php else: ?>
                    <?php foreach ($turmas as $turma): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($turma['class_name']); ?></td>
                            <td><?php echo htmlspecialchars($turma['class_course']); ?></td>
                            <td><?php echo htmlspecialchars($turma['class_year']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary">Editar</button>
                                <button class="btn btn-sm btn-danger">Excluir</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>
</body>
</html>