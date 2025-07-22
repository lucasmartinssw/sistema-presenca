<!-- menu.php -->
<ul class="nav nav-pills flex-column">
  <li class="nav-item">
    <a href="index.php" class="nav-link text-white <?= (isset($pagina_atual) && $pagina_atual === 'index') ? 'active' : '' ?>">Dashboard</a>
  </li>
  <li class="nav-item">
    <a href="turmas.php" class="nav-link text-white <?= (isset($pagina_atual) && $pagina_atual === 'turmas') ? 'active' : '' ?>">Turmas</a>
  </li>
  <li class="nav-item">
    <a href="professores.php" class="nav-link text-white <?= (isset($pagina_atual) && $pagina_atual === 'professores') ? 'active' : '' ?>">Professores</a>
  </li>
  <li class="nav-item">
    <a href="alunos.php" class="nav-link text-white <?= (isset($pagina_atual) && $pagina_atual === 'alunos') ? 'active' : '' ?>">Alunos</a>
  </li>
  <li class="nav-item">
    <a href="relatorios.php" class="nav-link text-white <?= (isset($pagina_atual) && $pagina_atual === 'relatorios') ? 'active' : '' ?>">Relatórios</a>
  </li>
  <li class="nav-item">
    <a href="cadastrar-usuario.php" class="nav-link text-white <?= (isset($pagina_atual) && $pagina_atual === 'cadastrar-usuario.php') ? 'active' : '' ?>">Cadastrar Usuário</a>  
  <li class="nav-item">
    <a href="../../backend/sair.php" class="nav-link text-danger">Sair</a>
  </li>
</ul>