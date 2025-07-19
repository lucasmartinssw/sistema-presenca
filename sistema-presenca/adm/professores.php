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

    // // AÇÃO: Cadastrar novo PROFESSOR (e seu usuário)
    // if ($_POST['action'] === 'create_teacher') {
    //     $fullName = $_POST['nomeProfessor'];
    //     $email = $_POST['emailProfessor'];
    //     $password = $_POST['senhaProfessor'];

    //     // É uma boa prática usar transações quando você precisa executar múltiplas queries que dependem uma da outra.
    //     $pdo->beginTransaction();
    //     try {
    //         // 1. Criar o usuário
    //         $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    //         $stmtUser = $pdo->prepare("INSERT INTO users (email, password_hash, user_type) VALUES (:email, :password, 'teacher')");
    //         $stmtUser->execute(['email' => $email, 'password' => $hashedPassword]);
    //         $id_user = $pdo->lastInsertId();

    //         // 2. Criar o professor, vinculando ao usuário recém-criado
    //         $stmtTeacher = $pdo->prepare("INSERT INTO teachers (id_user, full_name, is_active) VALUES (:id_user, :full_name, 1)");
    //         $stmtTeacher->execute(['id_user' => $id_user, 'full_name' => $fullName]);

    //         // Se tudo deu certo, confirma as operações
    //         $pdo->commit();
    //         $_SESSION['message'] = ['type' => 'success', 'text' => 'Professor cadastrado com sucesso!'];

    //     } catch (PDOException $e) {
    //         // Se algo deu errado, desfaz tudo
    //         $pdo->rollBack();
    //         if ($e->errorInfo[1] == 1062) { // Código de erro para entrada duplicada (email)
    //             $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erro: Este e-mail já está em uso!'];
    //         } else {
    //             $_SESSION['message'] = ['type' => 'danger', 'text' => 'Erro ao cadastrar professor: ' . $e->getMessage()];
    //         }
    //     }
    // }

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

// 1. Buscar VÍNCULOS para a tabela principal
$vinculos = [];
try {
    $stmt = $pdo->query(
       "SELECT cst.id_class_subject_teacher, t.full_name, s.subject_name, cl.class_name, cl.class_course
        FROM class_subject_teachers AS cst
        JOIN teachers AS t ON cst.id_teacher = t.id_teacher
        JOIN subjects AS s ON cst.id_subject = s.id_subject
        JOIN classes AS cl ON cst.id_class = cl.id_class
        WHERE t.is_active = 1 AND cl.is_active = 1
        ORDER BY t.full_name, s.subject_name"
    );
    $vinculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { /* Tratar erro */ }

// 2. Buscar PROFESSORES ativos para o modal de vínculo
$professores = [];
try {
    $stmtProf = $pdo->query("SELECT id_teacher, full_name FROM teachers WHERE is_active = 1 ORDER BY full_name ASC");
    $professores = $stmtProf->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { /* Tratar erro */ }

// 3. Buscar MATÉRIAS para o modal de vínculo
$materias = [];
try {
    $stmtMat = $pdo->query("SELECT id_subject, subject_name FROM subjects ORDER BY subject_name ASC");
    $materias = $stmtMat->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { /* Tratar erro */ }

// 4. Buscar TURMAS ativas para o modal de vínculo
$turmas = [];
try {
    $stmtTurmas = $pdo->query("SELECT id_class, class_name, class_course FROM classes WHERE is_active = 1 ORDER BY class_name ASC");
    $turmas = $stmtTurmas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { /* Tratar erro */ }
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
                <!-- <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCadastroProfessor"><i class="bi bi-person-plus"></i> Cadastrar Professor</button> -->
                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalCadastroMateria"><i class="bi bi-book"></i> Cadastrar Matéria</button>
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
                    <table class="table table-bordered">
                        <thead>
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
                                    <td><?= htmlspecialchars($vinculo['full_name']) ?></td>
                                    <td><?= htmlspecialchars($vinculo['subject_name']) ?></td>
                                    <td><?= htmlspecialchars($vinculo['class_name'] . ' - ' . $vinculo['class_course']) ?></td>
                                    <td>
                                        <a href="professores.php?excluir=<?= $vinculo['id_class_subject_teacher'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este vínculo?');">Excluir</a>
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
                                <option value="<?= $professor['id_teacher'] ?>"><?= htmlspecialchars($professor['full_name']) ?></option>
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

<div class="modal fade" id="modalCadastroProfessor" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cadastrar Novo Professor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="professores.php" method="POST">
                    <input type="hidden" name="action" value="create_teacher">
                    <div class="mb-3">
                        <label for="nomeProfessor" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="nomeProfessor" name="nomeProfessor" required>
                    </div>
                    <div class="mb-3">
                        <label for="emailProfessor" class="form-label">E-mail Institucional</label>
                        <input type="email" class="form-control" id="emailProfessor" name="emailProfessor" required>
                    </div>
                    <div class="mb-3">
                        <label for="senhaProfessor" class="form-label">Senha Provisória</label>
                        <input type="password" class="form-control" id="senhaProfessor" name="senhaProfessor" required>
                    </div>
                    <button type="submit" class="btn btn-success">Salvar Professor</button>
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
                    <button type="submit" class="btn btn-info">Salvar Matéria</button>
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