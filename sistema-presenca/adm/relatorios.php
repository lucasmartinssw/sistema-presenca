<?php
$pagina_atual = 'relatorios';
include '../../backend/verifica.php'; // Inclui o arquivo de verificação de sessão/autenticação
include '../../backend/conect.php'; // Inclui o arquivo de conexão com o banco de dados

// Query para buscar dados de relatórios, se necessário
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Relatórios - Instituto Federal</title>
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
        <h5 class="mb-0">Gerar Relatórios</h5>
        </header>
      <main class="p-4">
        <div class="card">
          <div class="card-body">
            <h6 class="card-title">Opções de Relatórios</h6>
            <p class="card-text">Aqui você poderá configurar e gerar diversos tipos de relatórios.</p>
            <form>
                <div class="mb-3">
                    <label for="tipoRelatorio" class="form-label">Tipo de Relatório</label>
                    <select class="form-select" id="tipoRelatorio" name="tipoRelatorio">
                        <option value="">Selecione...</option>
                        <option value="presenca">Presença por Turma</option>
                        <option value="alunos_ativos">Alunos Ativos</option>
                        <option value="professores_cursos">Professores por Curso</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="dataInicio" class="form-label">Data Início</label>
                    <input type="date" class="form-control" id="dataInicio" name="dataInicio">
                </div>
                <div class="mb-3">
                    <label for="dataFim" class="form-label">Data Fim</label>
                    <input type="date" class="form-control" id="dataFim" name="dataFim">
                </div>
                <button type="submit" class="btn btn-primary">Gerar Relatório</button>
            </form>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>