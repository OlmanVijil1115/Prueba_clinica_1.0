<?php
require_once __DIR__ . '/../init.php';
require_login(['admin','enfermero','medico']);
$pdo = get_pdo();
$err = '';
$paciente_id = (int)input('paciente_id', 0);
if (is_post()) {
    verify_csrf();
    $paciente_id = (int)input('paciente_id');
    $usuario_id = (int)input('usuario_id');
    $fecha = input('fecha');
    $hora = input('hora');
    if (!$paciente_id || !$usuario_id || !$fecha || !$hora) { $err = 'Completa todos los campos.'; }
    if (!$err) {
        $fecha_hora = $fecha . ' ' . $hora . ':00';
        $stmt = $pdo->prepare("INSERT INTO cita (paciente_id, usuario_id, fecha_hora, estado, creado_por) VALUES (?, ?, ?, 'pendiente', ?)");
        $stmt->execute([$paciente_id, $usuario_id, $fecha_hora, current_user()['id']]);
        flash('ok', 'Cita creada');
        redirect('index.php?fecha=' . $fecha);
    }
}
$users = $pdo->query("SELECT usuario_id, username, rol FROM usuario WHERE rol IN ('medico','enfermero') ORDER BY rol, username")->fetchAll();
$title = 'Nueva cita';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/nav.php';
?>
<section class="card slide-up">
  <h2>Nueva cita</h2>
  <?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>
  <form method="post" class="form">
    <?php csrf_field(); ?>
    <label>Paciente
      <input type="number" id="paciente_id" name="paciente_id" value="<?= e($paciente_id ?: '') ?>" placeholder="ID del paciente" required>
      <small>Escribe el ID o busca por nombre abajo.</small>
    </label>
    <div>
      <input type="text" id="buscar_paciente" placeholder="Buscar por nombre...">
      <div id="sugerencias" class="suggestions"></div>
    </div>
    <label>Profesional (medico/enfermero)
      <select name="usuario_id" required>
        <option value="">Selecciona</option>
        <?php foreach ($users as $u): ?>
          <option value="<?= e($u['usuario_id']) ?>"><?= e(ucfirst($u['rol']) . ' - ' . $u['username']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <div class="grid two">
      <label>Fecha
        <input type="date" name="fecha" required value="<?= e(input('fecha', date('Y-m-d'))) ?>">
      </label>
      <label>Hora
        <input type="time" name="hora" required value="<?= e(input('hora', '09:00')) ?>">
      </label>
    </div>
    <div class="actions">
      <button class="btn btn-primary">Guardar</button>
      <a class="btn" href="index.php">Cancelar</a>
    </div>
  </form>
</section>
<script>
document.getElementById('buscar_paciente').addEventListener('input', async (e) => {
  const q = e.target.value.trim();
  const box = document.getElementById('sugerencias');
  box.innerHTML = '';
  if (q.length < 2) return;
  const res = await fetch('../api/pacientes_search.php?q=' + encodeURIComponent(q));
  const data = await res.json();
  data.forEach(p => {
    const div = document.createElement('div');
    div.className = 'suggestion';
    div.textContent = `#${p.paciente_id} - ${p.nombre} (${p.edad || ''})`;
    div.onclick = () => { document.getElementById('paciente_id').value = p.paciente_id; box.innerHTML = ''; };
    box.appendChild(div);
  });
});
</script>
<?php include __DIR__ . '/../partials/footer.php'; ?>
