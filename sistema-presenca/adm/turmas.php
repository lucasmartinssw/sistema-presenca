<?php
$pagina_atual = 'turmas';
include '../../backend/verifica.php'; // Inclui o arquivo de verificação de sessão/autenticação
include '../../backend/conect.php'; // Inclui o arquivo de conexão com o banco de dados

// Você pode adicionar queries aqui para popular campos de seleção no modal, se necessário
// Ex: Query para buscar os anos ou cursos existentes
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
    <img src="../images/iflogov2.jpg" alt="Logo IF" width="40" class="me-2 rounded">
    <span class="fs-5 text-white">Painel IF</span>
  </div>
  <?php include 'menu.php'; ?>
</nav>
    <div class="flex-grow-1">
      <header class="topbar d-flex justify-content-between align-items-center p-3 shadow-sm">
        <h5 class="mb-0">Gerenciar Turmas</h5>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCadastroTurma">Cadastrar Nova Turma</button>
      </header>
      <main class="p-4">
        <div class="card">
          <div class="card-body overflow-auto">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Nome da Turma</th>
                  <th>Curso</th>
                  <th>Ano</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Turma A - Info</td>
                  <td>Informática</td>
                  <td>2025</td>
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

  <!-- Modal para cadastro de nova turma -->
  <div class="modal fade" id="modalCadastroTurma" tabindex="-1" aria-labelledby="modalCadastroTurmaLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCadastroTurmaLabel">Cadastrar Nova Turma</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="../../backend/cadastrar_turma.php" method="POST">
            <div class="mb-3">
              <label for="nomeTurma" class="form-label">Nome da Turma</label>
              <input type="text" class="form-control" id="nomeTurma" name="nomeTurma" required>
            </div>
            <div class="mb-3">
              <label for="cursoTurma" class="form-label">Curso</label>
              <input type="text" class="form-control" id="cursoTurma" name="cursoTurma" required>
            </div>
            <div class="mb-3">
              <label for="anoTurma" class="form-label">Ano</label>
              <input type="number" class="form-control" id="anoTurma" name="anoTurma" min="1900" max="2100" required>
            </div>
            <button type="submit" class="btn btn-success">Salvar Turma</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>