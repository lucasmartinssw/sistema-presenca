<?php
$pagina_atual = 'materias';
include '../../backend/verifica.php'; // Inclui o arquivo de verificação de sessão/autenticação
include '../../backend/conect.php'; // Inclui o arquivo de conexão com o banco de dados

// Query para buscar todas as turmas para o campo 'Turma' no modal
try {
    $stmt = $pdo->query("SELECT id_class, class_name FROM Classes ORDER BY class_name ASC");
    $turmas = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Erro ao carregar turmas: " . $e->getMessage();
    $turmas = []; // Garante que $turmas seja um array vazio em caso de erro
}

// Query para buscar todos os professores para o campo 'Professor Responsável' no modal
try {
    $stmt = $pdo->query("SELECT id_teacher, full_name FROM Teachers ORDER BY full_name ASC");
    $professores = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Erro ao carregar professores: " . $e->getMessage();
    $professores = []; // Garante que $professores seja um array vazio em caso de erro
}

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Matérias - Instituto Federal</title>
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
        <h5 class="mb-0">Gerenciar Matérias</h5>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCadastroMateria">Cadastrar Nova Matéria</button>
      </header>
      <main class="p-4">
        <div class="card">
          <div class="card-body overflow-auto">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Nome da Matéria</th>
                  <th>Turma</th>
                  <th>Professor Responsável</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Português</td>
                  <td>Turma A</td>
                  <td>João Silva</td>
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

  <!-- Modal para cadastro de novo materia -->
  <div class="modal fade" id="modalCadastroMateria" tabindex="-1" aria-labelledby="modalCadastroMateriaLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCadastroMateriaLabel">Cadastrar Nova Matéria</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="../../backend/cadastrar_materia.php" method="POST">
            <div class="mb-3">
              <label for="nomeMateria" class="form-label">Nome da Matéria</label>
              <input type="text" class="form-control" id="nomeMateria" name="nomeMateria" required>
            </div>
            <div class="mb-3">
                <label for="turmaMateria" class="form-label">Turma</label>
                <select class="form-select" id="turmaMateria" name="turmaMateria" required>
                    <option value="">Selecione a Turma</option>
                    <?php foreach ($turmas as $turma): ?>
                        <option value="<?= htmlspecialchars($turma['id_class']) ?>"><?= htmlspecialchars($turma['class_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="professorMateria" class="form-label">Professor Responsável</label>
                <select class="form-select" id="professorMateria" name="professorMateria" required>
                    <option value="">Selecione o Professor</option>
                    <?php foreach ($professores as $professor): ?>
                        <option value="<?= htmlspecialchars($professor['id_teacher']) ?>"><?= htmlspecialchars($professor['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Salvar Matéria</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>