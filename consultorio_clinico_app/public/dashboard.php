<?php
require_once __DIR__ . '/init.php';
require_login();
$user = current_user();
$pdo = get_pdo();
$today = date('Y-m-d');
if ($user['rol'] === 'medico') {
    $stmt = $pdo->prepare("SELECT c.cita_id, c.paciente_id, p.nombre, c.fecha_hora, c.estado FROM cita c JOIN paciente p ON p.paciente_id = c.paciente_id WHERE DATE(c.fecha_hora) = ? AND c.usuario_id = ? ORDER BY c.fecha_hora");
    $stmt->execute([$today, $user['id']]);
} else {
    $stmt = $pdo->prepare("SELECT c.cita_id, c.paciente_id, p.nombre, u.username as doctor, c.fecha_hora, c.estado FROM cita c JOIN paciente p ON p.paciente_id = c.paciente_id JOIN usuario u ON u.usuario_id = c.usuario_id WHERE DATE(c.fecha_hora) = ? ORDER BY c.fecha_hora");
    $stmt->execute([$today]);
}
$citas = $stmt->fetchAll();
$title = 'Panel';
include __DIR__ . '/partials/header.php';
include __DIR__ . '/partials/nav.php';
?>
<section>
  <div class="grid">
    <div class="card hover-lift">
      <h3>Pacientes</h3>
      <p>Registra y gestiona fichas.</p>
      <a class="btn btn-secondary" href="<?= BASE_URL ?>/pacientes/">Abrir</a>
    </div>
    <div class="card hover-lift">
      <h3>Citas</h3>
      <p>Agenda y confirma citas.</p>
      <a class="btn btn-secondary" href="<?= BASE_URL ?>/citas/">Abrir</a>
    </div>
    <div class="card hover-lift">
      <h3>Consultas</h3>
      <p>Registra consultas y diagnósticos.</p>
      <a class="btn btn-secondary" href="<?= BASE_URL ?>/consultas/">Abrir</a>
    </div>
  </div>
</section>
<section>
  <h2>Agenda de hoy</h2>
  <?php if (!$citas): ?>
    <p class="muted">No hay citas para hoy.</p>
  <?php else: ?>
    <div class="table">
      <div class="row head">
        <?php if ($user['rol'] !== 'medico'): ?><div>Profesional</div><?php endif; ?>
        <div>Paciente</div>
        <div>Fecha/Hora</div>
        <div>Estado</div>
        <div>Acciones</div>
      </div>
      <?php foreach ($citas as $c): ?>
      <div class="row">
        <?php if ($user['rol'] !== 'medico'): ?><div><?= e($c['doctor'] ?? $user['username']) ?></div><?php endif; ?>
        <div><?= e($c['nombre']) ?></div>
        <div><?= e(date('d/m H:i', strtotime($c['fecha_hora']))) ?></div>
        <div><span class="badge <?= e($c['estado']) ?>"><?= e($c['estado']) ?></span></div>
        <div class="actions">
          <a class="btn btn-sm" href="<?= BASE_URL ?>/citas/">Ver</a>
          <a class="btn btn-sm" href="<?= BASE_URL ?>/consultas/nueva.php?paciente_id=<?= e($c['paciente_id']) ?>&from_cita=<?= e($c['cita_id']) ?>">Nueva consulta</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>
