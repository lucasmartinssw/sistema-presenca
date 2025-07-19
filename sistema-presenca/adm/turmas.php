<?php
include '../../backend/verifica.php'; // Inclui o arquivo de verificação de sessão/autenticação
$pagina_atual = 'turmas';
include '../../backend/conect.php'; // Inclui o arquivo de conexão com o banco de dados

// Bloco de Inserção MODIFICADO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nomeTurma'], $_POST['cursoTurma'], $_POST['anoTurma'])) {
    $nomeTurma = $_POST['nomeTurma'];
    $cursoTurma = $_POST['cursoTurma'];
    $anoTurma = $_POST['anoTurma'];
    try {
        $stmt = $pdo->prepare("INSERT INTO classes (class_name, class_course, class_year, is_active) VALUES (:nome, :curso, :ano, 1)");
        $stmt->execute(['nome' => $nomeTurma, 'curso' => $cursoTurma, 'ano' => $anoTurma]);
        
        // Salva a mensagem de sucesso na sessão
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Turma cadastrada com sucesso!'];

    } catch (PDOException $e) {
        // Salva a mensagem de erro na sessão
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erro ao cadastrar turma!'];
    }
    header('Location: turmas.php'); // Redireciona para a URL limpa
    exit;
}

// Bloco de Exclusão MODIFICADO
if (isset($_GET['excluir']) && is_numeric($_GET['excluir'])) {
    $idExcluir = $_GET['excluir'];
    try {
        $stmtDel = $pdo->prepare("UPDATE classes SET is_active = 0 WHERE id_class = :id");
        $stmtDel->execute(['id' => $idExcluir]);
        
        // Salva a mensagem de sucesso na sessão
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Turma excluída com sucesso!'];

    } catch (PDOException $e) {
        // Salva a mensagem de erro na sessão
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erro ao excluir turma!'];
    }
    header('Location: turmas.php'); // Redireciona para a URL limpa
    exit;
}

// Buscar turmas da tabela 'classes'
$turmas = [];
try {
    $stmt = $pdo->query("SELECT id_class, class_name, class_course, class_year FROM classes WHERE is_active = 1");
    $turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<div class="alert alert-danger text-center">Erro ao buscar turmas: ' . $e->getMessage() . '</div>';
}

// Exibe a mensagem "flash" da sessão, se existir
if (isset($_SESSION['message'])) {
    // Exibe o alerta com a classe CSS (success, danger, etc.) e o texto da sessão
    echo '<div class="alert alert-'. $_SESSION['message']['type'] .' text-center">' . $_SESSION['message']['text'] . '</div>';
    
    // Apaga a mensagem da sessão para que não seja exibida novamente
    unset($_SESSION['message']);
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
                <?php foreach ($turmas as $turma): ?>
                <tr>
                  <td><?= htmlspecialchars($turma['class_name']) ?></td>
                  <td><?= htmlspecialchars($turma['class_course']) ?></td>
                  <td><?= htmlspecialchars($turma['class_year']) ?></td>
                  <td>
                    <a class="btn btn-sm btn-primary">Editar</a>
                    
                    <a href="turmas.php?excluir=<?= $turma['id_class'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta turma?');">Excluir</a>
                  </td>
                </tr>
                <?php endforeach; ?>
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
          <form action="turmas.php" method="POST"> <div class="mb-3">
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
  <script>
    // Esconde alertas após 5 segundos
    setTimeout(function() {
      document.querySelectorAll('.alert').forEach(function(el) {
        el.style.display = 'none';
      });
    }, 5000);
  </script>
</body>
</html>