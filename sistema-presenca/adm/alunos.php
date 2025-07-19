<?php
$pagina_atual = 'alunos';
include '../../backend/verifica.php'; // Inclui o arquivo de verificação de sessão/autenticação
include '../../backend/conect.php'; // Inclui o arquivo de conexão com o banco de dados

// Query para buscar todas as turmas para o campo 'Curso' no modal
try {
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
  <link rel="icon" type="image/png" href="images/favicon.png" />
  <title>Alunos - Instituto Federal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="d-flex">
   <nav class="sidebar d-flex flex-column p-3">
  <div class="d-flex align-items-center mb-4">
    <img src="../images/iflogov2.jpg" alt="Logo IF" width="40" class="me-2 rounded">
    <span class="fs-5 text-white">Painel IF</span>
  </div>
  <?php include 'menu.php'; ?>
</nav>
    <div class="flex-grow-1">
      <header class="topbar d-flex justify-content-between align-items-center p-3 shadow-sm">
        <h5 class="mb-0">Gerenciar Alunos</h5>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCadastroAluno">Cadastrar Novo Aluno</button>
      </header>
      <main class="p-4">
        <div class="card">
          <div class="card-body overflow-auto">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Nome</th>
                  <th>Matrícula</th>
                  <th>Curso</th>
                  <th>Responsável</th>
                  <th>Número do Responsável</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Ana Paula</td>
                  <td>2023123</td>
                  <td>Informática</td>
                  <td>Maria Paula</td>
                  <td>(34) 91234-5678</td>
                  <td>
                    <button class="btn btn-sm btn-primary">Editar</button>
                    <button class="btn btn-sm btn-danger">Excluir</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>

<!-- Modal para cadastro de novo aluno -->
  <div class="modal fade" id="modalCadastroAluno" tabindex="-1" aria-labelledby="modalCadastroAlunoLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCadastroAlunoLabel">Cadastrar Novo Aluno</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="../../backend/cadastrar_aluno.php" method="POST">
            <div class="mb-3">
              <label for="nomeAluno" class="form-label">Nome Completo</label>
              <input type="text" class="form-control" id="nomeAluno" name="nomeAluno" required>
            </div>
            <div class="mb-3">
              <label for="matriculaAluno" class="form-label">Matrícula</label>
              <input type="text" class="form-control" id="matriculaAluno" name="matriculaAluno" required>
            </div>
            <div class="mb-3">
                <label for="cursoAluno" class="form-label">Curso</label>
                <select class="form-select" id="cursoAluno" name="cursoAluno" required>
                    <option value="">Selecione o Curso</option>
                    <?php foreach ($turmas as $turma): ?>
                        <option value="<?= htmlspecialchars($turma['class_course']) ?>"><?= htmlspecialchars($turma['class_course']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
             <div class="mb-3">
              <label for="responsavelAluno" class="form-label">Nome do Responsável</label>
              <input type="text" class="form-control" id="responsavelAluno" name="responsavelAluno">
            </div>
            <div class="mb-3">
              <label for="telefoneResponsavel" class="form-label">Telefone do Responsável</label>
              <input type="text" class="form-control" id="telefoneResponsavel" name="telefoneResponsavel" placeholder="(XX) XXXXX-XXXX">
            </div>
            <button type="submit" class="btn btn-success">Salvar Aluno</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>