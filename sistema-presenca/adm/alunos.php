<?php
// Inclui os arquivos de backend
include '../../backend/verifica.php';
include '../../backend/conect.php';

// --- ROTEADOR DE AÇÕES (EXECUTADO APENAS EM REQUISIÇÕES POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? 'create'; // Define 'create' como padrão se nenhuma ação for enviada
    $response = ['success' => false, 'message' => 'Ação desconhecida.'];

    try {
        // --- LÓGICA DE ATUALIZAÇÃO ---
        if ($action === 'update') {
            if (isset($_POST['id_student'], $_POST['enrollment_id'], $_POST['id_class'])) {
                $sql = "UPDATE students SET enrollment_id = :enrollment_id, id_class = :id_class, guardian_name = :guardian_name, guardian_phone = :guardian_phone WHERE id_student = :id_student";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':enrollment_id', $_POST['enrollment_id'], PDO::PARAM_STR);
                $stmt->bindParam(':id_class', $_POST['id_class'], PDO::PARAM_INT);
                $stmt->bindParam(':guardian_name', $_POST['guardian_name'], PDO::PARAM_STR);
                $stmt->bindParam(':guardian_phone', $_POST['guardian_phone'], PDO::PARAM_STR);
                $stmt->bindParam(':id_student', $_POST['id_student'], PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $response = ['success' => true, 'message' => 'Aluno atualizado com sucesso!'];
                } else {
                    $response['message'] = 'Erro ao atualizar o aluno.';
                }
            } else {
                $response['message'] = 'Dados insuficientes para atualização.';
            }
        }
        // --- LÓGICA DE EXCLUSÃO (SOFT DELETE) ---
        elseif ($action === 'delete') {
            if (isset($_POST['id_student'])) {
                $sql = "UPDATE students SET is_active = 0 WHERE id_student = :id_student";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id_student', $_POST['id_student'], PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $response = ['success' => true, 'message' => 'Aluno excluído com sucesso!'];
                } else {
                    $response['message'] = 'Erro ao excluir o aluno.';
                }
            } else {
                $response['message'] = 'ID do aluno não fornecido para exclusão.';
            }
        }
        // --- LÓGICA DE CADASTRO (CREATE) ---
        elseif ($action === 'create') {
            if (isset($_POST['full_name'], $_POST['enrollment_id'], $_POST['id_class'])) {
                $sql = "INSERT INTO students (id_class, enrollment_id, full_name, guardian_name, guardian_phone, is_active) VALUES (:id_class, :enrollment_id, :full_name, :guardian_name, :guardian_phone, 1)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id_class', $_POST['id_class'], PDO::PARAM_INT);
                $stmt->bindParam(':enrollment_id', $_POST['enrollment_id'], PDO::PARAM_STR);
                $stmt->bindParam(':full_name', $_POST['full_name'], PDO::PARAM_STR);
                $stmt->bindParam(':guardian_name', $_POST['guardian_name'], PDO::PARAM_STR);
                $stmt->bindParam(':guardian_phone', $_POST['guardian_phone'], PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $response = ['success' => true, 'message' => 'Aluno cadastrado com sucesso!'];
                } else {
                    $response['message'] = 'Erro ao executar a inserção.';
                }
            } else {
                 $response['message'] = 'Dados inválidos para cadastro.';
            }
        }
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            $response['message'] = 'Erro: A matrícula ou o aluno já está cadastrado.';
        } else {
            $response['message'] = 'Erro de banco de dados: ' . $e->getMessage();
        }
    }

    echo json_encode($response);
    exit; // Impede que o restante do HTML seja renderizado
}


// --- LÓGICA PARA EXIBIÇÃO DA PÁGINA (GET) ---
$pagina_atual = 'alunos';
try {
    $stmt_turmas = $pdo->query("SELECT id_class, class_name, class_course FROM Classes ORDER BY class_name ASC");
    $turmas = $stmt_turmas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $turmas = []; }
try {
    $stmt_alunos_user = $pdo->query("SELECT id_user, username FROM users WHERE user_type = 'student' ORDER BY username ASC");
    $alunos_student = $stmt_alunos_user->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $alunos_student = []; }
try {
    $stmt_students = $pdo->query("SELECT s.id_student, s.full_name, s.enrollment_id, c.class_name, c.class_course, s.guardian_name, s.guardian_phone, c.id_class FROM students s JOIN Classes c ON s.id_class = c.id_class WHERE s.is_active = 1 ORDER BY s.full_name ASC");
    $students_list = $stmt_students->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $students_list = []; }
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
                                    <th>Telefone Responsável</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="student-table-body">
                                <?php if (empty($students_list)): ?>
                                    <tr><td colspan="6" class="text-center">Nenhum aluno cadastrado.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($students_list as $student): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($student['full_name']) ?></td>
                                        <td><?= htmlspecialchars($student['enrollment_id']) ?></td>
                                        <td><?= htmlspecialchars($student['class_name'] . ' - ' . $student['class_course']) ?></td>
                                        <td><?= htmlspecialchars($student['guardian_name']) ?></td>
                                        <td><?= htmlspecialchars($student['guardian_phone']) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary btn-edit" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEditarAluno"
                                                data-id="<?= $student['id_student'] ?>"
                                                data-nome="<?= htmlspecialchars($student['full_name']) ?>"
                                                data-matricula="<?= htmlspecialchars($student['enrollment_id']) ?>"
                                                data-curso="<?= $student['id_class'] ?>"
                                                data-responsavel="<?= htmlspecialchars($student['guardian_name']) ?>"
                                                data-telefone="<?= htmlspecialchars($student['guardian_phone']) ?>">
                                                Editar
                                            </button>
                                            <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $student['id_student'] ?>">
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

    <div class="modal fade" id="modalCadastrarAluno" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cadastrar Novo Aluno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCadastrarAluno">
                         <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label for="nomeAluno" class="form-label">Nome Completo</label>
                            <select class="form-select" id="nomeAluno" required>
                                <option value="">Selecione o aluno...</option>
                                <?php foreach ($alunos_student as $aluno): ?>
                                    <option value="<?= $aluno['id_user']; ?>" data-username="<?= htmlspecialchars($aluno['username']); ?>">
                                        <?= htmlspecialchars($aluno['username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="matriculaAluno" class="form-label">Matrícula</label>
                            <input type="text" class="form-control" id="matriculaAluno" name="enrollment_id" required>
                        </div>
                        <div class="mb-3">
                            <label for="cursoAluno" class="form-label">Curso</label>
                            <select class="form-select" id="cursoAluno" name="id_class" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($turmas as $turma): ?>
                                    <option value="<?= $turma['id_class']; ?>"><?= htmlspecialchars($turma['class_name'] . ' - ' . $turma['class_course']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="responsavelAluno" class="form-label">Nome do Responsável</label>
                            <input type="text" class="form-control" id="responsavelAluno" name="guardian_name">
                        </div>
                        <div class="mb-3">
                            <label for="telefoneResponsavel" class="form-label">Telefone do Responsável</label>
                            <input type="text" class="form-control" id="telefoneResponsavel" name="guardian_phone">
                        </div>
                        <button type="submit" class="btn btn-success">Cadastrar Aluno</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditarAluno" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Aluno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarAluno">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" id="editAlunoId" name="id_student">
                        <div class="mb-3">
                            <label for="editNomeAluno" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="editNomeAluno" name="full_name" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="editMatriculaAluno" class="form-label">Matrícula</label>
                            <input type="text" class="form-control" id="editMatriculaAluno" name="enrollment_id" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCursoAluno" class="form-label">Curso</label>
                            <select class="form-select" id="editCursoAluno" name="id_class" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($turmas as $turma): ?>
                                    <option value="<?= $turma['id_class']; ?>"><?= htmlspecialchars($turma['class_name'] . ' - ' . $turma['class_course']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editResponsavelAluno" class="form-label">Nome do Responsável</label>
                            <input type="text" class="form-control" id="editResponsavelAluno" name="guardian_name">
                        </div>
                        <div class="mb-3">
                            <label for="editTelefoneResponsavel" class="form-label">Telefone do Responsável</label>
                            <input type="text" class="form-control" id="editTelefoneResponsavel" name="guardian_phone">
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'modal-cadastrar-usuario.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // Função genérica para enviar dados via fetch
        function sendData(formData) {
            fetch('alunos.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Ocorreu um erro de comunicação.');
            });
        }

        // --- CADASTRO ---
        const formCadastrar = document.getElementById('formCadastrarAluno');
        formCadastrar.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(formCadastrar);
            const selectNome = document.getElementById('nomeAluno');
            const selectedOption = selectNome.options[selectNome.selectedIndex];
            formData.append('full_name', selectedOption.getAttribute('data-username'));
            sendData(formData);
        });
        
        // --- POVOAR MODAL DE EDIÇÃO ---
        const modalEditarAluno = document.getElementById('modalEditarAluno');
        modalEditarAluno.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            modalEditarAluno.querySelector('#editAlunoId').value = button.getAttribute('data-id');
            modalEditarAluno.querySelector('#editNomeAluno').value = button.getAttribute('data-nome');
            modalEditarAluno.querySelector('#editMatriculaAluno').value = button.getAttribute('data-matricula');
            modalEditarAluno.querySelector('#editCursoAluno').value = button.getAttribute('data-curso');
            modalEditarAluno.querySelector('#editResponsavelAluno').value = button.getAttribute('data-responsavel');
            modalEditarAluno.querySelector('#editTelefoneResponsavel').value = button.getAttribute('data-telefone');
        });

        // --- SUBMISSÃO DO FORMULÁRIO DE EDIÇÃO ---
        const formEditarAluno = document.getElementById('formEditarAluno');
        formEditarAluno.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(formEditarAluno);
            sendData(formData);
        });

        // --- AÇÕES DE EXCLUIR (usando delegação de eventos) ---
        const tableBody = document.getElementById('student-table-body');
        tableBody.addEventListener('click', function(event) {
            if (event.target && event.target.classList.contains('btn-delete')) {
                const studentId = event.target.getAttribute('data-id');
                if (confirm('Tem certeza que deseja excluir este aluno? A ação não pode ser desfeita.')) {
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('id_student', studentId);
                    sendData(formData);
                }
            }
        });
    });
    </script>
    <script src="cadastro-usuario-modal.js"></script>
</body>
</html>