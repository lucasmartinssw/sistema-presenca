<?php
$pagina_atual = 'alunos';
include '../../backend/verifica.php'; // Inclui o arquivo de verificação de sessão/autenticação
include '../../backend/conect.php'; // Inclui o arquivo de conexão com o banco de dados

// Query para buscar todas as turmas para o campo 'Curso' no modal de cadastro e edição
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
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCadastrarAluno">Cadastrar Novo Aluno</button>
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
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditarAluno"
                            data-id="1" data-nome="Ana Paula" data-matricula="2023123" data-curso="1" 
                            data-responsavel="Maria Paula" data-telefone="(34) 91234-5678">Editar</button>
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

  <div class="modal fade" id="modalCadastrarAluno" tabindex="-1" aria-labelledby="modalCadastrarAlunoLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCadastrarAlunoLabel">Cadastrar Novo Aluno</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-3">
              <label for="nomeAluno" class="form-label">Nome Completo</label>
              <input type="text" class="form-control" id="nomeAluno" required>
            </div>
            <div class="mb-3">
              <label for="matriculaAluno" class="form-label">Matrícula</label>
              <input type="text" class="form-control" id="matriculaAluno" required>
            </div>
            <div class="mb-3">
              <label for="cursoAluno" class="form-label">Curso</label>
              <select class="form-select" id="cursoAluno" required>
                <option value="">Selecione...</option>
                <?php foreach ($turmas as $turma): ?>
                    <option value="<?= htmlspecialchars($turma['id_class']); ?>">
                        <?= htmlspecialchars($turma['class_name'] . ' - ' . $turma['class_course']); ?>
                    </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="responsavelAluno" class="form-label">Nome do Responsável</label>
              <input type="text" class="form-control" id="responsavelAluno">
            </div>
            <div class="mb-3">
              <label for="telefoneResponsavel" class="form-label">Telefone do Responsável</label>
              <input type="text" class="form-control" id="telefoneResponsavel" placeholder="(XX) XXXXX-XXXX">
            </div>
            <button type="submit" class="btn btn-success">Cadastrar Aluno</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalEditarAluno" tabindex="-1" aria-labelledby="modalEditarAlunoLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditarAlunoLabel">Editar Aluno</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <input type="hidden" id="editAlunoId">
            <div class="mb-3">
              <label for="editNomeAluno" class="form-label">Nome Completo</label>
              <input type="text" class="form-control" id="editNomeAluno" required>
            </div>
            <div class="mb-3">
              <label for="editMatriculaAluno" class="form-label">Matrícula</label>
              <input type="text" class="form-control" id="editMatriculaAluno" required>
            </div>
            <div class="mb-3">
              <label for="editCursoAluno" class="form-label">Curso</label>
              <select class="form-select" id="editCursoAluno" required>
                <option value="">Selecione...</option>
                <?php foreach ($turmas as $turma): ?>
                    <option value="<?= htmlspecialchars($turma['id_class']); ?>">
                        <?= htmlspecialchars($turma['class_name'] . ' - ' . $turma['class_course']); ?>
                    </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="editResponsavelAluno" class="form-label">Nome do Responsável</label>
              <input type="text" class="form-control" id="editResponsavelAluno">
            </div>
            <div class="mb-3">
              <label for="editTelefoneResponsavel" class="form-label">Telefone do Responsável</label>
              <input type="text" class="form-control" id="editTelefoneResponsavel" placeholder="(XX) XXXXX-XXXX">
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
        var modalEditarAluno = document.getElementById('modalEditarAluno');
        modalEditarAluno.addEventListener('show.bs.modal', function (event) {
            // Botão que acionou o modal
            var button = event.relatedTarget;

            // Extrai as informações dos atributos data-*
            var id = button.getAttribute('data-id');
            var nome = button.getAttribute('data-nome');
            var matricula = button.getAttribute('data-matricula');
            var curso = button.getAttribute('data-curso');
            var responsavel = button.getAttribute('data-responsavel');
            var telefone = button.getAttribute('data-telefone');

            // Atualiza os campos do formulário no modal
            var inputId = modalEditarAluno.querySelector('#editAlunoId');
            var inputNome = modalEditarAluno.querySelector('#editNomeAluno');
            var inputMatricula = modalEditarAluno.querySelector('#editMatriculaAluno');
            var selectCurso = modalEditarAluno.querySelector('#editCursoAluno');
            var inputResponsavel = modalEditarAluno.querySelector('#editResponsavelAluno');
            var inputTelefone = modalEditarAluno.querySelector('#editTelefoneResponsavel');

            inputId.value = id;
            inputNome.value = nome;
            inputMatricula.value = matricula;
            selectCurso.value = curso; // Define o valor do select
            inputResponsavel.value = responsavel;
            inputTelefone.value = telefone;
        });
    });
  </script>

  <?php include 'modal-cadastrar-usuario.php'; ?>
  <script src="cadastro-usuario-modal.js"></script>
</body>
</html>