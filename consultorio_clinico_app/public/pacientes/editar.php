<?php
require_once __DIR__ . '/../init.php';
require_login();
$pdo = get_pdo();
$id = (int)input('id');
$stmt = $pdo->prepare('SELECT * FROM paciente WHERE paciente_id = ?');
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { http_response_code(404); exit('Paciente no encontrado'); }
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
        $stmt = $pdo->prepare('UPDATE paciente SET nombre=?, fecha_nacimiento=?, telefono=?, domicilio=?, identificacion=?, modificado_por=?, modificado_en=NOW() WHERE paciente_id=?');
        $stmt->execute([$nombre, $fecha_nacimiento, $telefono, $domicilio, $identificacion, current_user()['id'], $id]);
        flash('ok', 'Paciente actualizado');
        redirect('index.php');
    }
}
$title = 'Editar paciente';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/nav.php';
?>
<section class="card slide-up">
  <h2>Editar paciente</h2>
  <?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>
  <form method="post" class="form">
    <?php csrf_field(); ?>
    <label>Nombre completo
      <input name="nombre" value="<?= e($p['nombre']) ?>" required>
    </label>
    <label>Fecha de nacimiento
      <input name="fecha_nacimiento" type="date" value="<?= e($p['fecha_nacimiento']) ?>">
    </label>
    <label>Identificación
      <input name="identificacion" value="<?= e($p['identificacion']) ?>">
    </label>
    <label>Teléfono
      <input name="telefono" value="<?= e($p['telefono']) ?>">
    </label>
    <label>Domicilio
      <input name="domicilio" value="<?= e($p['domicilio']) ?>">
    </label>
    <div class="actions">
      <button class="btn btn-primary">Actualizar</button>
      <a class="btn" href="index.php">Cancelar</a>
    </div>
  </form>
</section>
<?php include __DIR__ . '/../partials/footer.php'; ?>
