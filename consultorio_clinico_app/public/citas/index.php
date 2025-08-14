<?php
require_once __DIR__ . '/../init.php';
require_login();
$pdo = get_pdo();
$user = current_user();
$fecha = input('fecha', date('Y-m-d'));
if ($user['rol'] === 'medico') {
    $stmt = $pdo->prepare('SELECT c.*, p.nombre as paciente, u.username as doctor FROM cita c JOIN paciente p ON p.paciente_id = c.paciente_id JOIN usuario u ON u.usuario_id = c.usuario_id WHERE DATE(c.fecha_hora) = ? AND c.usuario_id = ? ORDER BY c.fecha_hora');
    $stmt->execute([$fecha, $user['id']]);
} else {
    $stmt = $pdo->prepare('SELECT c.*, p.nombre as paciente, u.username as doctor FROM cita c JOIN paciente p ON p.paciente_id = c.paciente_id JOIN usuario u ON u.usuario_id = c.usuario_id WHERE DATE(c.fecha_hora) = ? ORDER BY c.fecha_hora');
    $stmt->execute([$fecha]);
}
$citas = $stmt->fetchAll();
$title = 'Citas';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/nav.php';
?>
<section>
  <div class="toolbar">
    <form class="inline">
      <input type="date" name="fecha" value="<?= e($fecha) ?>">
      <button class="btn">Filtrar</button>
    </form>
    <a class="btn btn-primary" href="nueva.php">Nueva cita</a>
  </div>
  <div class="table">
    <div class="row head">
      <div>Hora</div>
      <div>Paciente</div>
      <div>Profesional</div>
      <div>Estado</div>
      <div>Acciones</div>
    </div>
    <?php foreach ($citas as $c): ?>
    <div class="row">
      <div><?= e(date('H:i', strtotime($c['fecha_hora']))) ?></div>
      <div><?= e($c['paciente']) ?></div>
      <div><?= e($c['doctor']) ?></div>
      <div><span class="badge <?= e($c['estado']) ?>"><?= e($c['estado']) ?></span></div>
      <div class="actions">
        <a class="btn btn-sm" href="../consultas/nueva.php?paciente_id=<?= e($c['paciente_id']) ?>&from_cita=<?= e($c['cita_id']) ?>">Registrar consulta</a>
        <?php if ($c['estado'] !== 'cancelada'): ?>
          <a class="btn btn-sm" href="cambiar_estado.php?id=<?= e($c['cita_id']) ?>&a=cancelada" onclick="return confirm('¿Cancelar cita?')">Cancelar</a>
        <?php endif; ?>
        <?php if ($c['estado'] !== 'confirmada'): ?>
          <a class="btn btn-sm" href="cambiar_estado.php?id=<?= e($c['cita_id']) ?>&a=confirmada">Confirmar</a>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php include __DIR__ . '/../partials/footer.php'; ?>
