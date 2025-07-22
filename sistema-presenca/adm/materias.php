<?php
$pagina_atual = 'materias';
include '../../backend/verifica.php'; // Inclui o arquivo de verificação de sessão/autenticação
include '../../backend/conect.php'; // Inclui o arquivo de conexão com o banco de dados

// Query para buscar todas as turmas para o campo 'Turma' nos modais
try {
    $stmt = $pdo->query("SELECT id_class, class_name FROM Classes ORDER BY class_name ASC");
    $turmas = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Erro ao carregar turmas: " . $e->getMessage();
    $turmas = []; // Garante que $turmas seja um array vazio em caso de erro
}

// Query para buscar todos os professores para o campo 'Professor Responsável' nos modais
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
    <img src="../images/iflogov2.jpg" alt="Logo IF" width="40" class="me-2">
    <span class="fs-5 text-white">Painel IF</span>
  </div>
  <?php include 'menu.php'; ?>
</nav>
    <div class="flex-grow-1">
      <header class="topbar d-flex justify-content-between align-items-center p-3 shadow-sm">
        <h5 class="mb-0">Gerenciar Matérias</h5>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCadastrarMateria">Cadastrar Nova Matéria</button>
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
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditarMateria"
                            data-id="1" data-nome="Português" data-turma-id="1" data-professor-id="1">Editar</button>
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

  <div class="modal fade" id="modalCadastrarMateria" tabindex="-1" aria-labelledby="modalCadastrarMateriaLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCadastrarMateriaLabel">Cadastrar Nova Matéria</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-3">
              <label for="nomeMateria" class="form-label">Nome da Matéria</label>
              <input type="text" class="form-control" id="nomeMateria" required>
            </div>
            <div class="mb-3">
              <label for="turmaMateria" class="form-label">Turma</label>
              <select class="form-select" id="turmaMateria" required>
                <option value="">Selecione...</option>
                <?php foreach ($turmas as $turma): ?>
                    <option value="<?= htmlspecialchars($turma['id_class']); ?>">
                        <?= htmlspecialchars($turma['class_name'] . ' - ' . $turma['class_course']); ?>
                    </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="professorMateria" class="form-label">Professor Responsável</label>
              <select class="form-select" id="professorMateria" required>
                <option value="">Selecione...</option>
                <?php foreach ($professores as $professor): ?>
                    <option value="<?= htmlspecialchars($professor['id_teacher']); ?>">
                        <?= htmlspecialchars($professor['full_name']); ?>
                    </option>
                <?php endforeach; ?>
              </select>
            </div>
            <button type="submit" class="btn btn-success">Cadastrar Matéria</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalEditarMateria" tabindex="-1" aria-labelledby="modalEditarMateriaLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditarMateriaLabel">Editar Matéria</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <input type="hidden" id="editMateriaId">
            <div class="mb-3">
              <label for="editNomeMateria" class="form-label">Nome da Matéria</label>
              <input type="text" class="form-control" id="editNomeMateria" required>
            </div>
            <div class="mb-3">
              <label for="editTurmaMateria" class="form-label">Turma</label>
              <select class="form-select" id="editTurmaMateria" required>
                <option value="">Selecione...</option>
                <?php foreach ($turmas as $turma): ?>
                    <option value="<?= htmlspecialchars($turma['id_class']); ?>">
                        <?= htmlspecialchars($turma['class_name'] . ' - ' . $turma['class_course']); ?>
                    </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="editProfessorMateria" class="form-label">Professor Responsável</label>
              <select class="form-select" id="editProfessorMateria" required>
                <option value="">Selecione...</option>
                <?php foreach ($professores as $professor): ?>
                    <option value="<?= htmlspecialchars($professor['id_teacher']); ?>">
                        <?= htmlspecialchars($professor['full_name']); ?>
                    </option>
                <?php endforeach; ?>
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalEditarMateria = document.getElementById('modalEditarMateria');
        modalEditarMateria.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Botão que acionou o modal

            // Extrai as informações dos atributos data-*
            var id = button.getAttribute('data-id');
            var nome = button.getAttribute('data-nome');
            var turmaId = button.getAttribute('data-turma-id');
            var professorId = button.getAttribute('data-professor-id');

            // Atualiza os campos do formulário no modal
            var inputId = modalEditarMateria.querySelector('#editMateriaId');
            var inputNome = modalEditarMateria.querySelector('#editNomeMateria');
            var selectTurma = modalEditarMateria.querySelector('#editTurmaMateria');
            var selectProfessor = modalEditarMateria.querySelector('#editProfessorMateria');

            inputId.value = id;
            inputNome.value = nome;
            selectTurma.value = turmaId;
            selectProfessor.value = professorId;
        });
    });
  </script>
</body>
</html>