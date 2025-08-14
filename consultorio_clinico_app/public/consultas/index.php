<?php
require_once __DIR__ . '/../init.php';
require_login();
$pdo = get_pdo();
$user = current_user();
if ($user['rol'] === 'medico') {
    $stmt = $pdo->prepare('SELECT c.consulta_id, c.fecha_consulta, p.nombre FROM consulta c JOIN paciente p ON p.paciente_id = c.paciente_id WHERE c.creado_por = ? ORDER BY c.fecha_consulta DESC LIMIT 100');
    $stmt->execute([$user['id']]);
} else {
    $stmt = $pdo->query('SELECT c.consulta_id, c.fecha_consulta, p.nombre FROM consulta c JOIN paciente p ON p.paciente_id = c.paciente_id ORDER BY c.fecha_consulta DESC LIMIT 100');
}
$rows = $stmt->fetchAll();
$title = 'Consultas';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/nav.php';
?>
<section>
  <div class="toolbar">
    <a class="btn btn-primary" href="nueva.php">Nueva consulta</a>
  </div>
  <div class="table">
    <div class="row head">
      <div>Fecha</div>
      <div>Paciente</div>
      <div>Acciones</div>
    </div>
    <?php foreach ($rows as $r): ?>
    <div class="row">
      <div><?= e(date('d/m/Y H:i', strtotime($r['fecha_consulta']))) ?></div>
      <div><?= e($r['nombre']) ?></div>
      <div><a class="btn btn-sm" href="ver.php?id=<?= e($r['consulta_id']) ?>">Ver</a></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php include __DIR__ . '/../partials/footer.php'; ?>
