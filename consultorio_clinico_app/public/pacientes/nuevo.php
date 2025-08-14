<?php
require_once __DIR__ . '/../init.php';
require_login();
$pdo = get_pdo();
$err = '';
if (is_post()) {
    verify_csrf();
    $nombre = trim((string)input('nombre'));
    $fecha_nacimiento = input('fecha_nacimiento') ?: null;
    $telefono = trim((string)input('telefono')) ?: null;
    $domicilio = trim((string)input('domicilio')) ?: null;
    $identificacion = trim((string)input('identificacion')) ?: null;
    if (!$nombre) { $err = 'El nombre es obligatorio.'; }
    if (!$err) {
        $stmt = $pdo->prepare('INSERT INTO paciente (nombre, fecha_nacimiento, telefono, domicilio, identificacion, creado_por) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$nombre, $fecha_nacimiento, $telefono, $domicilio, $identificacion, current_user()['id']]);
        flash('ok', 'Paciente creado');
        redirect('index.php');
    }
}
$title = 'Nuevo paciente';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/nav.php';
?>
<section class="card slide-up">
  <h2>Nuevo paciente</h2>
  <?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>
  <form method="post" class="form">
    <?php csrf_field(); ?>
    <label>Nombre completo
      <input name="nombre" required>
    </label>
    <label>Fecha de nacimiento
      <input name="fecha_nacimiento" type="date">
    </label>
    <label>Identificación
      <input name="identificacion">
    </label>
    <label>Teléfono
      <input name="telefono">
    </label>
    <label>Domicilio
      <input name="domicilio">
    </label>
    <div class="actions">
      <button class="btn btn-primary">Guardar</button>
      <a class="btn" href="index.php">Cancelar</a>
    </div>
  </form>
</section>
<?php include __DIR__ . '/../partials/footer.php'; ?>
