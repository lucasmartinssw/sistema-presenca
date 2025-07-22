<?php
$pagina_atual = 'turmas';
include '../../backend/verifica.php'; // Inclui o arquivo de verificação de sessão/autenticação
include '../../backend/conect.php'; // Inclui o arquivo de conexão com o banco de dados

// --- PROCESSAMENTO DOS FORMULÁRIOS (AÇÕES) ---

// Função para exibir alertas
function showAlert($type, $message) {
    $_SESSION['message'] = ['type' => $type, 'text' => $message];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // AÇÃO: Cadastrar nova turma
    if ($_POST['action'] === 'create_class') {
        $class_name = trim($_POST['class_name']);
        $class_course = trim($_POST['class_course']);
        $class_year = intval($_POST['class_year']); // Garante que é um inteiro

        // Validação básica
        if (empty($class_name) || empty($class_course) || empty($class_year)) {
            showAlert('danger', 'Por favor, preencha todos os campos para cadastrar a turma.');
        } else {
            try {
                // Prepara e executa a inserção
                $stmt = $pdo->prepare("INSERT INTO classes (class_name, class_course, class_year) VALUES (:class_name, :class_course, :class_year)");
                $stmt->execute([
                    ':class_name' => $class_name,
                    ':class_course' => $class_course,
                    ':class_year' => $class_year
                ]);
                showAlert('success', 'Turma cadastrada com sucesso!');
            } catch (PDOException $e) {
                showAlert('danger', 'Erro ao cadastrar turma: ' . $e->getMessage());
            }
        }
    }
    // AÇÃO: Editar turma existente
    else if ($_POST['action'] === 'edit_class') {
        $id_class = intval($_POST['edit_id_class']);
        $class_name = trim($_POST['edit_class_name']);
        $class_course = trim($_POST['edit_class_course']);
        $class_year = intval($_POST['edit_class_year']);
        $is_active = isset($_POST['edit_is_active']) ? 1 : 0; // Checkbox

        if (empty($class_name) || empty($class_course) || empty($class_year)) {
            showAlert('danger', 'Por favor, preencha todos os campos para editar a turma.');
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE classes SET class_name = :class_name, class_course = :class_course, class_year = :class_year, is_active = :is_active WHERE id_class = :id_class");
                $stmt->execute([
                    ':class_name' => $class_name,
                    ':class_course' => $class_course,
                    ':class_year' => $class_year,
                    ':is_active' => $is_active,
                    ':id_class' => $id_class
                ]);
                showAlert('success', 'Turma atualizada com sucesso!');
            } catch (PDOException $e) {
                showAlert('danger', 'Erro ao atualizar turma: ' . $e->getMessage());
            }
        }
    }
    // AÇÃO: Excluir turma
    else if ($_POST['action'] === 'delete_class') {
        $id_class = intval($_POST['delete_id_class']);

        try {
            // Antes de excluir a turma, considere verificar dependências (alunos, matérias)
            // ou definir um 'is_active' para 0 em vez de excluir fisicamente.
            // Por simplicidade, faremos a exclusão direta aqui.
            $stmt = $pdo->prepare("DELETE FROM classes WHERE id_class = :id_class");
            $stmt->execute([':id_class' => $id_class]);
            showAlert('success', 'Turma excluída com sucesso!');
        } catch (PDOException $e) {
            showAlert('danger', 'Erro ao excluir turma: ' . $e->getMessage());
        }
    }
    
    // Redireciona para evitar reenvio do formulário ao atualizar a página
    header('Location: turmas.php');
    exit;
}

// --- FIM DO PROCESSAMENTO DOS FORMULÁRIOS ---

// Query para buscar todas as turmas para exibição
try {
    $stmt = $pdo->query("SELECT id_class, class_name, class_course, class_year, is_active FROM classes ORDER BY class_name ASC");
    $turmas = $stmt->fetchAll();
} catch (PDOException $e) {
    showAlert('danger', 'Erro ao carregar turmas: ' . $e->getMessage());
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
        <?php 
        // Exibir mensagens de alerta se houver
        if (isset($_SESSION['message'])) {
            $message_type = $_SESSION['message']['type'];
            $message_text = $_SESSION['message']['text'];
            echo "<div class='alert alert-$message_type alert-dismissible fade show' role='alert'>
                    $message_text
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>";
            unset($_SESSION['message']); // Limpa a mensagem após exibir
        }
        ?>
        <div class="card">
          <div class="card-body overflow-auto">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nome da Turma</th>
                  <th>Curso</th>
                  <th>Ano</th>
                  <th>Ativa</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($turmas)): ?>
                <tr>
                  <td colspan="6" class="text-center">Nenhuma turma cadastrada.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($turmas as $turma): ?>
                <tr>
                  <td><?= htmlspecialchars($turma['id_class']) ?></td>
                  <td><?= htmlspecialchars($turma['class_name']) ?></td>
                  <td><?= htmlspecialchars($turma['class_course']) ?></td>
                  <td><?= htmlspecialchars($turma['class_year']) ?></td>
                  <td><?= $turma['is_active'] ? 'Sim' : 'Não' ?></td>
                  <td>
                    <button class="btn btn-sm btn-primary" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalEditarTurma"
                            data-id="<?= htmlspecialchars($turma['id_class']) ?>"
                            data-nome="<?= htmlspecialchars($turma['class_name']) ?>"
                            data-curso="<?= htmlspecialchars($turma['class_course']) ?>"
                            data-ano="<?= htmlspecialchars($turma['class_year']) ?>"
                            data-ativa="<?= htmlspecialchars($turma['is_active']) ?>">
                      Editar
                    </button>
                    <button class="btn btn-sm btn-danger" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalExcluirTurma"
                            data-id="<?= htmlspecialchars($turma['id_class']) ?>"
                            data-nome="<?= htmlspecialchars($turma['class_name']) ?>">
                      Excluir
                    </button>
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

  <div class="modal fade" id="modalCadastroTurma" tabindex="-1" aria-labelledby="modalCadastroTurmaLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCadastroTurmaLabel">Cadastrar Nova Turma</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="turmas.php" method="POST">
            <input type="hidden" name="action" value="create_class">
            <div class="mb-3">
              <label for="class_name" class="form-label">Nome da Turma</label>
              <input type="text" class="form-control" id="class_name" name="class_name" required>
            </div>
            <div class="mb-3">
              <label for="class_course" class="form-label">Curso</label>
              <input type="text" class="form-control" id="class_course" name="class_course" required>
            </div>
            <div class="mb-3">
              <label for="class_year" class="form-label">Ano</label>
              <input type="number" class="form-control" id="class_year" name="class_year" min="1900" max="2100" required>
            </div>
            <button type="submit" class="btn btn-success">Cadastrar</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalEditarTurma" tabindex="-1" aria-labelledby="modalEditarTurmaLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditarTurmaLabel">Editar Turma</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="turmas.php" method="POST">
            <input type="hidden" name="action" value="edit_class">
            <input type="hidden" name="edit_id_class" id="edit_id_class">
            <div class="mb-3">
              <label for="edit_class_name" class="form-label">Nome da Turma</label>
              <input type="text" class="form-control" id="edit_class_name" name="edit_class_name" required>
            </div>
            <div class="mb-3">
              <label for="edit_class_course" class="form-label">Curso</label>
              <input type="text" class="form-control" id="edit_class_course" name="edit_class_course" required>
            </div>
            <div class="mb-3">
              <label for="edit_class_year" class="form-label">Ano</label>
              <input type="number" class="form-control" id="edit_class_year" name="edit_class_year" min="1900" max="2100" required>
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="edit_is_active" name="edit_is_active">
              <label class="form-check-label" for="edit_is_active">Turma Ativa</label>
            </div>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalExcluirTurma" tabindex="-1" aria-labelledby="modalExcluirTurmaLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalExcluirTurmaLabel">Confirmar Exclusão</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Você tem certeza que deseja excluir a turma "<strong id="turmaNomeExcluir"></strong>"?</p>
          <div class="alert alert-warning" role="alert">
            **Atenção:** A exclusão de uma turma pode afetar alunos e matérias vinculadas. Esta ação é irreversível.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <form action="turmas.php" method="POST" class="d-inline">
            <input type="hidden" name="action" value="delete_class">
            <input type="hidden" name="delete_id_class" id="delete_id_class">
            <button type="submit" class="btn btn-danger">Excluir Turma</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Lógica para preencher o modal de Edição
        var modalEditarTurma = document.getElementById('modalEditarTurma');
        modalEditarTurma.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Botão que acionou o modal

            // Extrai as informações dos atributos data-*
            var id = button.getAttribute('data-id');
            var nome = button.getAttribute('data-nome');
            var curso = button.getAttribute('data-curso');
            var ano = button.getAttribute('data-ano');
            var ativa = button.getAttribute('data-ativa');

            // Atualiza os campos do formulário no modal
            var inputId = modalEditarTurma.querySelector('#edit_id_class');
            var inputNome = modalEditarTurma.querySelector('#edit_class_name');
            var inputCurso = modalEditarTurma.querySelector('#edit_class_course');
            var inputAno = modalEditarTurma.querySelector('#edit_class_year');
            var inputAtiva = modalEditarTurma.querySelector('#edit_is_active');

            inputId.value = id;
            inputNome.value = nome;
            inputCurso.value = curso;
            inputAno.value = ano;
            inputAtiva.checked = (ativa === '1'); // Define o estado do checkbox
        });

        // Lógica para preencher o modal de Exclusão
        var modalExcluirTurma = document.getElementById('modalExcluirTurma');
        modalExcluirTurma.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Botão que acionou o modal
            var id = button.getAttribute('data-id');
            var nome = button.getAttribute('data-nome');

            var inputIdExcluir = modalExcluirTurma.querySelector('#delete_id_class');
            var turmaNomeExcluir = modalExcluirTurma.querySelector('#turmaNomeExcluir');

            inputIdExcluir.value = id;
            turmaNomeExcluir.textContent = nome; // Exibe o nome da turma no texto do modal
        });

        // Esconde alertas após 5 segundos
        setTimeout(function() {
            const alert = document.querySelector('.alert');
            if (alert) {
                new bootstrap.Alert(alert).close();
            }
        }, 5000);
    });
  </script>
</body>
</html>