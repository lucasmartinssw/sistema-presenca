<?php
include '../../backend/verifica.php';
$pagina_atual = 'alunos';
include '../../backend/conect.php'; // Inclui o arquivo de conexão com o banco de dados

// Bloco de INSERÇÃO de Aluno MODIFICADO
// Agora, definimos is_active como 1 ao cadastrar um novo aluno.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nomeAluno'])) {
    $id_class = $_POST['id_class'];
    $enrollment_id = $_POST['matriculaAluno'];
    $full_name = $_POST['nomeAluno'];
    $guardian_name = $_POST['responsavelAluno'];
    $guardian_phone = $_POST['telefoneResponsavel'];

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO students (id_class, enrollment_id, full_name, guardian_name, guardian_phone, is_active) 
             VALUES (:id_class, :enrollment_id, :full_name, :guardian_name, :guardian_phone, 1)" // <-- Adicionado valor 1
        );
        $stmt->execute([
            'id_class' => $id_class,
            'enrollment_id' => $enrollment_id,
            'full_name' => $full_name,
            'guardian_name' => $guardian_name,
            'guardian_phone' => $guardian_phone
        ]);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Aluno cadastrado com sucesso!'];
    } catch (PDOException $e) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erro ao cadastrar aluno: ' . $e->getMessage()];
    }
    header('Location: alunos.php');
    exit;
}

// Bloco de EXCLUSÃO de Aluno MODIFICADO
// Em vez de 'DELETE', agora usamos 'UPDATE' para alterar is_active para 0.
if (isset($_GET['excluir']) && is_numeric($_GET['excluir'])) {
    $idExcluir = $_GET['excluir'];
    try {
        $stmtDel = $pdo->prepare("UPDATE students SET is_active = 0 WHERE id_student = :id");
        $stmtDel->execute(['id' => $idExcluir]);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Aluno excluído com sucesso!'];
    } catch (PDOException $e) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erro ao excluir aluno!'];
    }
    header('Location: alunos.php');
    exit;
}

// Buscar todos os ALUNOS para exibir na tabela MODIFICADO
// Adicionamos 'WHERE s.is_active = 1' para listar apenas alunos ativos.
$alunos = [];
try {
    $stmtAlunos = $pdo->query(
        "SELECT s.id_student, s.full_name, s.enrollment_id, s.guardian_name, s.guardian_phone, c.class_course 
         FROM students s
         JOIN classes c ON s.id_class = c.id_class
         WHERE s.is_active = 1
         ORDER BY s.full_name ASC"
    );
    $alunos = $stmtAlunos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<div class="alert alert-danger text-center">Erro ao buscar alunos: ' . $e->getMessage() . '</div>';
}

// Buscar todas as TURMAS para o <select> do modal (sem alteração aqui)
$turmas = [];
try {
    $stmtTurmas = $pdo->query("SELECT id_class, class_name, class_course FROM classes WHERE is_active = 1 ORDER BY class_name ASC");
    $turmas = $stmtTurmas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<div class="alert alert-danger text-center">Erro ao carregar turmas: ' . $e->getMessage() . '</div>';
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
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCadastroAluno">Cadastrar Novo Aluno</button>
      </header>
      <main class="p-4">
        <?php
        // Exibe a mensagem "flash" da sessão, se existir
        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-'. htmlspecialchars($_SESSION['message']['type']) .' text-center">' . htmlspecialchars($_SESSION['message']['text']) . '</div>';
            unset($_SESSION['message']); // Apaga a mensagem
        }
        ?>
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
                <?php if (empty($alunos)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Nenhum aluno cadastrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($alunos as $aluno): ?>
                    <tr>
                      <td><?= htmlspecialchars($aluno['full_name']) ?></td>
                      <td><?= htmlspecialchars($aluno['enrollment_id']) ?></td>
                      <td><?= htmlspecialchars($aluno['class_course']) ?></td>
                      <td><?= htmlspecialchars($aluno['guardian_name']) ?></td>
                      <td><?= htmlspecialchars($aluno['guardian_phone']) ?></td>
                      <td>
                        <a href="#" class="btn btn-sm btn-primary">Editar</a>
                        <a href="alunos.php?excluir=<?= $aluno['id_student'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este aluno?');">Excluir</a>
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

<div class="modal fade" id="modalCadastroAluno" tabindex="-1" aria-labelledby="modalCadastroAlunoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCadastroAlunoLabel">Cadastrar Novo Aluno</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="alunos.php" method="POST">
                    <div class="mb-3">
                        <label for="nomeAluno" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="nomeAluno" name="nomeAluno" required>
                    </div>
                    <div class="mb-3">
                        <label for="matriculaAluno" class="form-label">Matrícula</label>
                        <input type="text" class="form-control" id="matriculaAluno" name="matriculaAluno" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_class" class="form-label">Turma</label>
                        <select class="form-select" id="id_class" name="id_class" required>
                            <option value="">Selecione a Turma</option>
                            <?php foreach ($turmas as $turma): ?>
                                <option value="<?= $turma['id_class'] ?>"><?= htmlspecialchars($turma['class_name'] . ' - ' . $turma['class_course']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="responsavelAluno" class="form-label">Nome do Responsável</label>
                        <input type="text" class="form-control" id="responsavelAluno" name="responsavelAluno" required>
                    </div>
                    <div class="mb-3">
                        <label for="telefoneResponsavel" class="form-label">Telefone do Responsável</label>
                        <input type="text" class="form-control" id="telefoneResponsavel" name="telefoneResponsavel" placeholder="(XX) XXXXX-XXXX" required>
                    </div>
                    <button type="submit" class="btn btn-success">Salvar Aluno</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Esconde alertas após 5 segundos
setTimeout(function() {
    const alert = document.querySelector('.alert');
    if (alert) {
        alert.style.display = 'none';
    }
}, 5000);
</script>
</body>
</html>