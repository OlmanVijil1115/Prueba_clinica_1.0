<?php
require_once __DIR__ . '/../init.php';
require_login();
$pdo = get_pdo();
$id = (int)input('id');
$stmt = $pdo->prepare('SELECT * FROM paciente WHERE paciente_id = ?');
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { http_response_code(404); exit('Paciente no encontrado'); }
$histStmt = $pdo->prepare('SELECT c.consulta_id, c.fecha_consulta, d.descripcion as dx FROM consulta c LEFT JOIN diagnostico d ON d.consulta_id = c.consulta_id WHERE c.paciente_id = ? ORDER BY c.fecha_consulta DESC');
$histStmt->execute([$id]);
$hist = $histStmt->fetchAll();
$title = 'Ficha del paciente';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/nav.php';
?>
<section>
  <h2><?= e($p['nombre']) ?></h2>
  <div class="grid two">
    <div class="card">
      <h3>Datos</h3>
      <p><strong>Identificación:</strong> <?= e($p['identificacion'] ?? '-') ?></p>
      <p><strong>Edad:</strong> <?= e($p['edad'] ?? '-') ?></p>
      <p><strong>Teléfono:</strong> <?= e($p['telefono'] ?? '-') ?></p>
      <p><strong>Domicilio:</strong> <?= e($p['domicilio'] ?? '-') ?></p>
    </div>
    <div class="card">
      <h3>Acciones</h3>
      <p><a class="btn" href="../citas/nueva.php?paciente_id=<?= e($p['paciente_id']) ?>">Agendar cita</a></p>
      <p><a class="btn" href="../consultas/nueva.php?paciente_id=<?= e($p['paciente_id']) ?>">Nueva consulta</a></p>
    </div>
  </div>
  <div class="card">
    <h3>Historial de consultas</h3>
    <?php if (!$hist): ?>
      <p class="muted">Sin consultas registradas.</p>
    <?php else: ?>
      <div class="table">
        <div class="row head">
          <div>Fecha</div>
          <div>Diagnóstico</div>
          <div>Acciones</div>
        </div>
        <?php foreach ($hist as $c): ?>
          <div class="row">
            <div><?= e(date('d/m/Y H:i', strtotime($c['fecha_consulta']))) ?></div>
            <div><?= e($c['dx'] ?? '-') ?></div>
            <div><a class="btn btn-sm" href="../consultas/ver.php?id=<?= e($c['consulta_id']) ?>">Ver</a></div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php include __DIR__ . '/../partials/footer.php'; ?>
