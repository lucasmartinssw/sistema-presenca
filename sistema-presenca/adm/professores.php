<?php
include '../../backend/verifica.php';
$pagina_atual = 'professores';
include '../../backend/conect.php';

// --- PROCESSAMENTO DOS FORMULÁRIOS (AÇÕES) ---

// Verificamos qual formulário foi enviado através de um campo 'action'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // AÇÃO: Criar novo VÍNCULO
    if ($_POST['action'] === 'create_vinculo') {
        $id_teacher = $_POST['id_teacher'];
        $id_subject = $_POST['id_subject'];
        $id_class = $_POST['id_class'];

        try {
            // Verifica se o vínculo já existe para evitar duplicatas
            $checkStmt = $pdo->prepare("SELECT id_class_subject_teacher FROM class_subject_teachers WHERE id_teacher = :id_teacher AND id_subject = :id_subject AND id_class = :id_class");
            $checkStmt->execute(['id_teacher' => $id_teacher, 'id_subject' => $id_subject, 'id_class' => $id_class]);
            if ($checkStmt->fetch()) {
                $_SESSION['message'] = ['type' => 'warning', 'text' => 'Este vínculo já existe!'];
            } else {
                $stmt = $pdo->prepare("INSERT INTO class_subject_teachers (id_teacher, id_subject, id_class) VALUES (:id_teacher, :id_subject, :id_class)");
                $stmt->execute(['id_teacher' => $id_teacher, 'id_subject' => $id_subject, 'id_class' => $id_class]);
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Vínculo realizado com sucesso!'];
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erro ao criar vínculo: ' . $e->getMessage()];
        }
    }

    // AÇÃO: Editar um VÍNCULO existente
    if ($_POST['action'] === 'update_vinculo') {
        $id_vinculo = $_POST['id_vinculo'];
        $id_teacher = $_POST['id_teacher'];
        $id_subject = $_POST['id_subject'];
        $id_class = $_POST['id_class'];

        try {
            // Verifica se a nova combinação já existe (excluindo o próprio registro que está sendo editado)
             $checkStmt = $pdo->prepare("SELECT id_class_subject_teacher FROM class_subject_teachers WHERE id_teacher = :id_teacher AND id_subject = :id_subject AND id_class = :id_class AND id_class_subject_teacher != :id_vinculo");
            $checkStmt->execute(['id_teacher' => $id_teacher, 'id_subject' => $id_subject, 'id_class' => $id_class, 'id_vinculo' => $id_vinculo]);
            if ($checkStmt->fetch()) {
                $_SESSION['message'] = ['type' => 'warning', 'text' => 'Este vínculo já existe em outro registro!'];
            } else {
                $stmt = $pdo->prepare("UPDATE class_subject_teachers SET id_teacher = :id_teacher, id_subject = :id_subject, id_class = :id_class WHERE id_class_subject_teacher = :id_vinculo");
                $stmt->execute(['id_teacher' => $id_teacher, 'id_subject' => $id_subject, 'id_class' => $id_class, 'id_vinculo' => $id_vinculo]);
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Vínculo atualizado com sucesso!'];
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erro ao atualizar vínculo: ' . $e->getMessage()];
        }
    }

    // AÇÃO: Cadastrar nova MATÉRIA
    if ($_POST['action'] === 'create_subject') {
        $subjectName = $_POST['nomeMateria'];
        try {
            $stmt = $pdo->prepare("INSERT INTO subjects (subject_name) VALUES (:subject_name)");
            $stmt->execute(['subject_name' => $subjectName]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Matéria cadastrada com sucesso!'];
        } catch (PDOException $e) {
             if ($e->errorInfo[1] == 1062) { // Código de erro para entrada duplicada (nome da matéria)
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erro: Esta matéria já existe!'];
            } else {
                $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erro ao cadastrar matéria: ' . $e->getMessage()];
            }
        }
    }

    // Redireciona para a mesma página para limpar o POST e mostrar a mensagem
    header('Location: professores.php');
    exit;
}


// Bloco para EXCLUIR Vínculo
if (isset($_GET['excluir']) && is_numeric($_GET['excluir'])) {
    $id_excluir = $_GET['excluir'];
    try {
        $stmtDel = $pdo->prepare("DELETE FROM class_subject_teachers WHERE id_class_subject_teacher = :id");
        $stmtDel->execute(['id' => $id_excluir]);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Vínculo excluído com sucesso!'];
    } catch (PDOException $e) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erro ao excluir vínculo!'];
    }
    header('Location: professores.php');
    exit;
}

// --- BUSCAR DADOS PARA EXIBIÇÃO E MODAIS ---

// 1. Buscar VÍNCULOS para a tabela principal (adicionado IDs para o modal de edição)
$vinculos = [];
try {
    // ########## INÍCIO DA CORREÇÃO ##########
    $stmt = $pdo->query(
       "SELECT 
            cst.id_class_subject_teacher, 
            u.username, 
            s.subject_name, 
            cl.class_name, 
            cl.class_course,
            cst.id_teacher,
            cst.id_subject,
            cst.id_class
        FROM class_subject_teachers AS cst
        JOIN teachers AS t ON cst.id_teacher = t.id_teacher
        JOIN users AS u ON t.id_user = u.id_user
        JOIN subjects AS s ON cst.id_subject = s.id_subject
        JOIN classes AS cl ON cst.id_class = cl.id_class
        WHERE t.is_active = 1 AND cl.is_active = 1
        ORDER BY u.username, s.subject_name"
    );
    // ########## FIM DA CORREÇÃO ##########
    $vinculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    error_log("Erro ao buscar vínculos: " . $e->getMessage());
}

// 2. Buscar PROFESSORES ativos para os modais
$professores = [];
try {
    $stmtProf = $pdo->query(
       "SELECT 
            t.id_teacher, 
            u.username 
        FROM teachers AS t
        JOIN users AS u ON t.id_user = u.id_user
        WHERE t.is_active = 1 AND u.user_type = 'teacher'
        ORDER BY u.username ASC"
    );
    $professores = $stmtProf->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro ao buscar professores: " . $e->getMessage());
}

// 3. Buscar MATÉRIAS para os modais
$materias = [];
try {
    $stmtMat = $pdo->query("SELECT id_subject, subject_name FROM subjects ORDER BY subject_name ASC");
    $materias = $stmtMat->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    error_log("Erro ao buscar matérias: " . $e->getMessage());
}

// 4. Buscar TURMAS ativas para os modais
$turmas = [];
try {
    $stmtTurmas = $pdo->query("SELECT id_class, class_name, class_course FROM classes WHERE is_active = 1 ORDER BY class_name ASC");
    $turmas = $stmtTurmas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    error_log("Erro ao buscar turmas: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vínculos - Instituto Federal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
            <h5 class="mb-0">Gerenciar Vínculos</h5>
            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCadastroVinculo"><i class="bi bi-link-45deg"></i> Criar Vínculo</button>
                <button class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalCadastroMateria"><i class="bi bi-book"></i> Cadastrar Matéria</button>
            </div>
        </header>
        <main class="p-4">
            <?php
            if (isset($_SESSION['message'])) {
                echo '<div class="alert alert-' . htmlspecialchars($_SESSION['message']['type']) . ' text-center">' . htmlspecialchars($_SESSION['message']['text']) . '</div>';
                unset($_SESSION['message']);
            }
            ?>
            <div class="card">
                <div class="card-body overflow-auto">
                    <table class="table">
                        <thead class="table-light">
                        <tr>
                            <th>Professor</th>
                            <th>Matéria</th>
                            <th>Turma</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($vinculos)): ?>
                            <tr><td colspan="4" class="text-center">Nenhum vínculo cadastrado.</td></tr>
                        <?php else: ?>
                            <?php foreach ($vinculos as $vinculo): ?>
                                <tr>
                                    <td><?= htmlspecialchars($vinculo['username']) ?></td>
                                    <td><?= htmlspecialchars($vinculo['subject_name']) ?></td>
                                    <td><?= htmlspecialchars($vinculo['class_name'] . ' - ' . $vinculo['class_course']) ?></td>
                                    <td>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditarVinculo"
                                                data-id="<?= $vinculo['id_class_subject_teacher'] ?>"
                                                data-teacher-id="<?= $vinculo['id_teacher'] ?>"
                                                data-subject-id="<?= $vinculo['id_subject'] ?>"
                                                data-class-id="<?= $vinculo['id_class'] ?>">
                                            Editar
                                        </button>
                                        <a href="professores.php?excluir=<?= $vinculo['id_class_subject_teacher'] ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este vínculo?');">Excluir</a>
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

<div class="modal fade" id="modalCadastroVinculo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vincular Professor a Matéria/Turma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="professores.php" method="POST">
                    <input type="hidden" name="action" value="create_vinculo">
                    <div class="mb-3">
                        <label for="id_teacher" class="form-label">Professor</label>
                        <select class="form-select" name="id_teacher" required>
                            <option value="">Selecione um professor</option>
                            <?php foreach ($professores as $professor): ?>
                                <option value="<?= $professor['id_teacher'] ?>"><?= htmlspecialchars($professor['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id_subject" class="form-label">Matéria</label>
                        <select class="form-select" name="id_subject" required>
                            <option value="">Selecione uma matéria</option>
                            <?php foreach ($materias as $materia): ?>
                                <option value="<?= $materia['id_subject'] ?>"><?= htmlspecialchars($materia['subject_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id_class" class="form-label">Turma</label>
                        <select class="form-select" name="id_class" required>
                            <option value="">Selecione uma turma</option>
                            <?php foreach ($turmas as $turma): ?>
                                <option value="<?= $turma['id_class'] ?>"><?= htmlspecialchars($turma['class_name'] . ' - ' . $turma['class_course']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar Vínculo</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarVinculo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Vínculo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="professores.php" method="POST">
                    <input type="hidden" name="action" value="update_vinculo">
                    <input type="hidden" name="id_vinculo" id="edit_id_vinculo">
                    <div class="mb-3">
                        <label for="edit_id_teacher" class="form-label">Professor</label>
                        <select class="form-select" name="id_teacher" id="edit_id_teacher" required>
                            <option value="">Selecione um professor</option>
                            <?php foreach ($professores as $professor): ?>
                               <option value="<?= $professor['id_teacher'] ?>"><?= htmlspecialchars($professor['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_id_subject" class="form-label">Matéria</label>
                        <select class="form-select" name="id_subject" id="edit_id_subject" required>
                            <option value="">Selecione uma matéria</option>
                            <?php foreach ($materias as $materia): ?>
                                <option value="<?= $materia['id_subject'] ?>"><?= htmlspecialchars($materia['subject_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_id_class" class="form-label">Turma</label>
                        <select class="form-select" name="id_class" id="edit_id_class" required>
                            <option value="">Selecione uma turma</option>
                            <?php foreach ($turmas as $turma): ?>
                                <option value="<?= $turma['id_class'] ?>"><?= htmlspecialchars($turma['class_name'] . ' - ' . $turma['class_course']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCadastroMateria" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cadastrar Nova Matéria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="professores.php" method="POST">
                    <input type="hidden" name="action" value="create_subject">
                    <div class="mb-3">
                        <label for="nomeMateria" class="form-label">Nome da Matéria</label>
                        <input type="text" class="form-control" id="nomeMateria" name="nomeMateria" required>
                    </div>
                    <button type="submit" class="btn btn-info text-white">Salvar Matéria</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'modal-cadastrar-usuario.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="cadastro-usuario-modal.js"></script>
<script>
    // Script existente para edição de vínculos
    // Esconde alertas de feedback após 5 segundos
    setTimeout(function() {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.style.display = 'none';
        }
    }, 5000);

    // Adiciona listener para o modal de edição para preencher os campos
    document.addEventListener('DOMContentLoaded', function () {
        var modalEditar = document.getElementById('modalEditarVinculo');
        modalEditar.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Botão que acionou o modal

            // Extrai as informações dos atributos data-*
            var id = button.getAttribute('data-id');
            var teacherId = button.getAttribute('data-teacher-id');
            var subjectId = button.getAttribute('data-subject-id');
            var classId = button.getAttribute('data-class-id');

            // Seleciona os campos do formulário no modal
            var inputId = modalEditar.querySelector('#edit_id_vinculo');
            var selectTeacher = modalEditar.querySelector('#edit_id_teacher');
            var selectSubject = modalEditar.querySelector('#edit_id_subject');
            var selectClass = modalEditar.querySelector('#edit_id_class');

            // Atualiza os valores dos campos do formulário
            inputId.value = id;
            selectTeacher.value = teacherId;
            selectSubject.value = subjectId;
            selectClass.value = classId;
        });
    });
</script>
</body>
</html>