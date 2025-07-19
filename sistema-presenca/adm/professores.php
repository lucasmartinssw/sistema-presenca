<?php
$pagina_atual = 'professores';
include '../../backend/verifica.php'; // Inclui o arquivo de verificação de sessão/autenticação
include '../../backend/conect.php'; // Inclui o arquivo de conexão com o banco de dados

// Você pode adicionar queries aqui para popular campos de seleção no modal, se necessário
// Ex: Query para buscar os tipos de curso para o campo 'Curso' ou turmas
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Professores - Instituto Federal</title>
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
        <h5 class="mb-0">Gerenciar Professores</h5>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCadastroProfessor">Cadastrar Novo Professor</button>
      </header>
      <main class="p-4">
        <div class="card">
          <div class="card-body overflow-auto">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Nome Completo</th>
                  <th>E-mail</th>
                  <th>Tipo de Usuário</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>João Silva</td>
                  <td>joao.silva@if.edu.br</td>
                  <td>Teacher</td>
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

  <!-- Modal para cadastro de novo professor -->
  <div class="modal fade" id="modalCadastroProfessor" tabindex="-1" aria-labelledby="modalCadastroProfessorLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCadastroProfessorLabel">Cadastrar Novo Professor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="../../backend/cadastrar_professor.php" method="POST">
            <div class="mb-3">
              <label for="nomeProfessor" class="form-label">Nome Completo</label>
              <input type="text" class="form-control" id="nomeProfessor" name="nomeProfessor" required>
            </div>
            <div class="mb-3">
              <label for="emailProfessor" class="form-label">E-mail Institucional</label>
              <input type="email" class="form-control" id="emailProfessor" name="emailProfessor" required>
            </div>
            <div class="mb-3">
              <label for="senhaProfessor" class="form-label">Senha</label>
              <input type="password" class="form-control" id="senhaProfessor" name="senhaProfessor" required>
            </div>
            <input type="hidden" name="tipoUsuario" value="teacher">
            <button type="submit" class="btn btn-success">Salvar Professor</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>