<?php $user = current_user(); ?>
<header class="topbar">
  <div class="brand">
    <span class="logo"></span>
    <span class="brand-name"><?= e(APP_NAME) ?></span>
  </div>
  <?php if ($user): ?>
  <nav class="menu">
    <a href="<?= BASE_URL ?>/dashboard.php">Inicio</a>
    <a href="<?= BASE_URL ?>/pacientes/">Pacientes</a>
    <a href="<?= BASE_URL ?>/citas/">Citas</a>
    <a href="<?= BASE_URL ?>/consultas/">Consultas</a>
  </nav>
  <div class="user">
    <span class="role-badge"><?= e($user['rol']) ?></span>
    <span class="username"><?= e($user['username']) ?></span>
    <a class="btn btn-outline" href="<?= BASE_URL ?>/logout.php">Salir</a>
  </div>
  <?php endif; ?>
</header>
<main class="container">
