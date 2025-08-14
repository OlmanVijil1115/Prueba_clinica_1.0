<?php
require_once __DIR__ . '/../init.php';
require_login();
$pdo = get_pdo();
$id = (int)input('id');
$stmt = $pdo->prepare('SELECT c.*, p.nombre FROM consulta c JOIN paciente p ON p.paciente_id = c.paciente_id WHERE c.consulta_id = ?');
$stmt->execute([$id]);
$c = $stmt->fetch();
if (!$c) { http_response_code(404); exit('Consulta no encontrada'); }
$ex = $pdo->prepare('SELECT * FROM examenfisico WHERE consulta_id = ?');
$ex->execute([$id]);
$examen = $ex->fetch();
$ant = $pdo->prepare('SELECT * FROM antecedente WHERE consulta_id = ?');
$ant->execute([$id]);
$ant_rows = $ant->fetchAll();
$dxs = $pdo->prepare('SELECT * FROM diagnostico WHERE consulta_id = ?');
$dxs->execute([$id]);
$diag = $dxs->fetch();
$meds = [];
if ($diag) {
    $mm = $pdo->prepare('SELECT * FROM medicamento WHERE diagnostico_id = ?');
    $mm->execute([$diag['diagnostico_id']]);
    $meds = $mm->fetchAll();
}
$title = 'Consulta #' . $id;
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/nav.php';
?>
<section>
  <h2>Consulta de <?= e($c['nombre']) ?> — <?= e(date('d/m/Y H:i', strtotime($c['fecha_consulta']))) ?></h2>
  <div class="grid two">
    <div class="card">
      <h3>Motivo e historia</h3>
      <p><strong>Motivo:</strong> <?= e($c['motivo'] ?? '-') ?></p>
      <p><strong>Historia actual:</strong> <?= e($c['historia_actual'] ?? '-') ?></p>
    </div>
    <div class="card">
      <h3>Examen físico</h3>
      <?php if (!$examen): ?>
        <p class="muted">No registrado</p>
      <?php else:
        $sv = $examen['signos_vitales'] ? json_decode($examen['signos_vitales'], true) : [];
      ?>
        <p><strong>Peso:</strong> <?= e($examen['peso']) ?> kg</p>
        <p><strong>Talla:</strong> <?= e($examen['talla']) ?> m</p>
        <p><strong>IMC:</strong> <?= e($examen['imc']) ?></p>
        <p><strong>Signos vitales:</strong>
          <?= e(($sv['temperatura'] ?? '-') . ' °C') ?>,
          <?= e($sv['presion_arterial'] ?? '-') ?>,
          <?= e(($sv['frecuencia_cardiaca'] ?? '-') . ' lpm') ?>
        </p>
        <p><strong>Hallazgos:</strong> <?= e($examen['hallazgos'] ?? '-') ?></p>
      <?php endif; ?>
    </div>
  </div>
  <div class="grid two">
    <div class="card">
      <h3>Antecedentes</h3>
      <?php if (!$ant_rows): ?><p class="muted">Ninguno</p><?php else: ?>
        <ul>
          <?php foreach ($ant_rows as $a): ?>
            <li><strong><?= e($a['tipo']) ?>:</strong> <?= e($a['descripcion']) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
    <div class="card">
      <h3>Diagnóstico y tratamiento</h3>
      <?php if (!$diag): ?>
        <p class="muted">No registrado</p>
      <?php else: ?>
        <p><strong>Diagnóstico:</strong> [<?= e($diag['codigo_icd10'] ?? '-') ?>] <?= e($diag['descripcion']) ?></p>
        <?php if ($meds): ?>
          <p><strong>Indicaciones:</strong></p>
          <ul>
            <?php foreach ($meds as $m): ?>
              <li><?= e($m['nombre']) ?> — <?= e($m['dosis'] ?? '') ?> — <?= e($m['frecuencia'] ?? '') ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
  <p><a class="btn" href="index.php">Volver</a></p>
</section>
<?php include __DIR__ . '/../partials/footer.php'; ?>
