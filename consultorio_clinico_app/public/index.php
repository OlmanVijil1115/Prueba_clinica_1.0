<?php
require_once __DIR__ . '/init.php';
if (current_user()) { redirect('dashboard.php'); }
$error = '';
if (is_post()) {
    verify_csrf();
    $username = trim((string)input('username'));
    $password = (string)input('password');
    if (login($username, $password)) {
        flash('ok', 'Bienvenido');
        redirect('dashboard.php');
    } else {
        $error = 'Usuario o contraseña incorrectos';
    }
}
$title = 'Iniciar sesión';
include __DIR__ . '/partials/header.php';
?>
<section class="auth-card slide-up">
  <h1 class="title">Bienvenido</h1>
  <p class="subtitle">Inicia sesión para continuar</p>
  <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
  <form method="post" class="form">
    <?php csrf_field(); ?>
    <label>Usuario
      <input required name="username" type="text" autocomplete="username" placeholder="usuario">
    </label>
    <label>Contraseña
      <input required name="password" type="password" autocomplete="current-password" placeholder="••••••••">
    </label>
    <button class="btn btn-primary" type="submit">Entrar</button>
  </form>
  <p class="hint">Usuario demo: admin / admin123</p>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>
