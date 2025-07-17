<!-- menu.php -->
<ul class="nav nav-pills flex-column">
  <li class="nav-item"><a href="index.html" class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) === 'index.html' ? 'active' : '' ?>">Dashboard</a></li>
  <li class="nav-item"><a href="turmas.php" class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) === 'turmas.php' ? 'active' : '' ?>">Turmas</a></li>
  <li class="nav-item"><a href="professores.php" class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) === 'professores.php' ? 'active' : '' ?>">Professores</a></li>
  <li class="nav-item"><a href="alunos.php" class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) === 'alunos.php' ? 'active' : '' ?>">Alunos</a></li>
  <li class="nav-item"><a href="relatorios.php" class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) === 'relatorios.php' ? 'active' : '' ?>">Relatorios</a></li>
  <li class="nav-item"><a href="login.php" class="nav-link text-danger <?= basename($_SERVER['PHP_SELF']) === 'login.php' ? 'active' : '' ?>">Sair</a></li>
  <!-- Repita para os outros itens -->
</ul>