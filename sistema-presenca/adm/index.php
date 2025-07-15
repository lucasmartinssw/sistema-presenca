<?php
include '../../backend/verifica.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Instituto Federal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="d-flex">
    <nav class="sidebar d-flex flex-column p-3">
      <div class="d-flex align-items-center mb-4">
        <img src="iflogov2.jpg" alt="Logo IF" width="40" class="me-2">
        <span class="fs-5 text-white">Painel IF</span>
      </div>
      <ul class="nav nav-pills flex-column">
        <li class="nav-item"><a href="index.html" class="nav-link text-white active">Dashboard</a></li>
        <li class="nav-item"><a href="turmas.php" class="nav-link text-white">Turmas</a></li>
        <li class="nav-item"><a href="professores.html" class="nav-link text-white">Professores</a></li>
        <li class="nav-item"><a href="alunos.html" class="nav-link text-white">Alunos</a></li>
        <li class="nav-item"><a href="materias.html" class="nav-link text-white">Matérias</a></li>
        <li class="nav-item"><a href="relatorios.html" class="nav-link text-white">Relatórios</a></li>
        <li class="nav-item mt-3"><a href="../../backend/sair.php" class="nav-link text-danger">Sair</a></li>
      </ul>
    </nav>
    <div class="flex-grow-1">
      <header class="topbar d-flex justify-content-between align-items-center p-3 shadow-sm">
        <h5 class="mb-0">Dashboard</h5>
        <span class="text-muted">Usuário logado</span>
      </header>
      <main class="p-4">
        <div class="card">
          <div class="card-body">
            <h6 class="card-title">Bem-vindo</h6>
            <p class="card-text">Selecione uma opção no menu lateral.</p>
          </div>
        </div>
      </main>
    </div>
  </div>
</body>
</html>