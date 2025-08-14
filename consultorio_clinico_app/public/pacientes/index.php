<?php
require_once __DIR__ . '/../init.php';
require_login();
$pdo = get_pdo();
$q = trim((string)input('q', ''));
if ($q) {
    $stmt = $pdo->prepare('SELECT * FROM paciente WHERE nombre LIKE ? ORDER BY nombre LIMIT 100');
    $stmt->execute(['%' . $q . '%']);
} else {
    $stmt = $pdo->query('SELECT * FROM paciente ORDER BY creado_en DESC LIMIT 100');
}
$pacientes = $stmt->fetchAll();
$title = 'Pacientes';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/nav.php';
?>
<section>
  <div class="toolbar">
    <form class="inline">
      <input type="text" name="q" placeholder="Buscar por nombre..." value="<?= e($q) ?>">
      <button class="btn">Buscar</button>
    </form>
    <a class="btn btn-primary" href="nuevo.php">Nuevo paciente</a>
  </div>
  <div class="table">
    <div class="row head">
      <div>Nombre</div>
      <div>Identificación</div>
      <div>Edad</div>
      <div>Teléfono</div>
      <div>Acciones</div>
    </div>
    <?php foreach ($pacientes as $p): ?>
    <div class="row">
      <div><?= e($p['nombre']) ?></div>
      <div><?= e($p['identificacion'] ?? '-') ?></div>
      <div><?= e($p['edad'] ?? '') ?></div>
      <div><?= e($p['telefono'] ?? '-') ?></div>
      <div class="actions">
        <a class="btn btn-sm" href="ver.php?id=<?= e($p['paciente_id']) ?>">Ver</a>
        <a class="btn btn-sm" href="editar.php?id=<?= e($p['paciente_id']) ?>">Editar</a>
        <a class="btn btn-sm danger" href="eliminar.php?id=<?= e($p['paciente_id']) ?>" onclick="return confirm('¿Eliminar paciente?')">Eliminar</a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php include __DIR__ . '/../partials/footer.php'; ?>
