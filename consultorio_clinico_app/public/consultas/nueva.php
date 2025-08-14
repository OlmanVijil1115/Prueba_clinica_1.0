<?php
require_once __DIR__ . '/../init.php';
require_login(['medico','admin','enfermero']);
$pdo = get_pdo();
$err = '';
$paciente_id = (int)input('paciente_id', 0);
if (is_post()) {
    verify_csrf();
    $paciente_id = (int)input('paciente_id');
    $motivo = trim((string)input('motivo'));
    $historia = trim((string)input('historia_actual'));
    $peso = trim((string)input('peso'));
    $talla = trim((string)input('talla'));
    $sv_temp = trim((string)input('sv_temperatura'));
    $sv_pa = trim((string)input('sv_pa'));
    $sv_fc = trim((string)input('sv_fc'));
    $hallazgos = trim((string)input('hallazgos'));
    $dx_codigo = trim((string)input('dx_codigo'));
    $dx_desc = trim((string)input('dx_desc'));
    $from_cita = (int)input('from_cita', 0);
    if (!$paciente_id) { $err = 'Selecciona un paciente.'; }
    if (!$err) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('INSERT INTO consulta (paciente_id, fecha_consulta, motivo, historia_actual, creado_por) VALUES (?, NOW(), ?, ?, ?)');
            $stmt->execute([$paciente_id, $motivo ?: null, $historia ?: null, current_user()['id']]);
            $consulta_id = (int)$pdo->lastInsertId();

            if ($peso !== '' || $talla !== '' || $sv_temp !== '' || $sv_pa !== '' || $sv_fc !== '' || $hallazgos !== '') {
                $signos = [];
                if ($sv_temp !== '') $signos['temperatura'] = (float)$sv_temp;
                if ($sv_pa !== '') $signos['presion_arterial'] = $sv_pa;
                if ($sv_fc !== '') $signos['frecuencia_cardiaca'] = (int)$sv_fc;
                $json = $signos ? json_encode($signos, JSON_UNESCAPED_UNICODE) : null;
                $stmt = $pdo->prepare('INSERT INTO examenfisico (consulta_id, peso, talla, signos_vitales, hallazgos, creado_por) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute([$consulta_id, $peso !== '' ? $peso : null, $talla !== '' ? $talla : null, $json, $hallazgos ?: null, current_user()['id']]);
            }

            if ($dx_desc) {
                $stmt = $pdo->prepare('INSERT INTO diagnostico (consulta_id, codigo_icd10, descripcion, creado_por) VALUES (?, ?, ?, ?)');
                $stmt->execute([$consulta_id, $dx_codigo ?: null, $dx_desc, current_user()['id']]);
                $diagnostico_id = (int)$pdo->lastInsertId();

                if (!empty($_POST['med_nombre'])) {
                    $meds = $_POST['med_nombre'];
                    $doses = $_POST['med_dosis'] ?? [];
                    $freqs = $_POST['med_frecuencia'] ?? [];
                    foreach ($meds as $i => $name) {
                        $name = trim((string)$name);
                        if (!$name) continue;
                        $stmt = $pdo->prepare('INSERT INTO medicamento (diagnostico_id, nombre, dosis, frecuencia, creado_por) VALUES (?, ?, ?, ?, ?)');
                        $stmt->execute([$diagnostico_id, $name, trim((string)($doses[$i] ?? '')) ?: null, trim((string)($freqs[$i] ?? '')) ?: null, current_user()['id']]);
                    }
                }
            }

            if (!empty($_POST['ant_tipo'])) {
                $tipos = $_POST['ant_tipo'];
                $descs = $_POST['ant_desc'] ?? [];
                foreach ($tipos as $i => $tipo) {
                    $tipo = (string)$tipo;
                    $desc = trim((string)($descs[$i] ?? ''));
                    if (!$tipo || !$desc) continue;
                    $stmt = $pdo->prepare('INSERT INTO antecedente (consulta_id, tipo, descripcion, creado_por) VALUES (?, ?, ?, ?)');
                    $stmt->execute([$consulta_id, $tipo, $desc, current_user()['id']]);
                }
            }

            if ($from_cita) {
                $pdo->prepare("UPDATE cita SET estado='confirmada', modificado_por=?, modificado_en=NOW() WHERE cita_id=?")->execute([current_user()['id'], $from_cita]);
            }

            $pdo->commit();
            flash('ok', 'Consulta registrada');
            redirect('ver.php?id=' . $consulta_id);
        } catch (Exception $e) {
            $pdo->rollBack();
            $err = 'Error al guardar: ' . $e->getMessage();
        }
    }
}
$title = 'Nueva consulta';
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/nav.php';
?>
<section class="card slide-up">
  <h2>Nueva consulta</h2>
  <?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>
  <form method="post" class="form">
    <?php csrf_field(); ?>
    <input type="hidden" name="from_cita" value="<?= e((int)input('from_cita', 0)) ?>">
    <label>ID Paciente
      <input type="number" name="paciente_id" value="<?= e($paciente_id ?: '') ?>" required>
    </label>
    <div class="grid two">
      <label>Motivo de consulta
        <input name="motivo" placeholder="Dolor de cabeza...">
      </label>
      <label>Historia actual
        <input name="historia_actual" placeholder="Inicio hace 3 días...">
      </label>
    </div>

    <h3>Examen físico</h3>
    <div class="grid three">
      <label>Peso (kg)
        <input name="peso" type="number" step="0.01">
      </label>
      <label>Talla (m)
        <input name="talla" type="number" step="0.01">
      </label>
      <label>Temperatura (°C)
        <input name="sv_temperatura" type="number" step="0.1">
      </label>
      <label>Presión arterial
        <input name="sv_pa" placeholder="120/80">
      </label>
      <label>Frecuencia cardiaca (lpm)
        <input name="sv_fc" type="number" step="1">
      </label>
      <label>Hallazgos
        <input name="hallazgos" placeholder="Sin hallazgos">
      </label>
    </div>

    <h3>Antecedentes</h3>
    <div id="antecedentes"></div>
    <button type="button" class="btn" onclick="addAnt()">Agregar antecedente</button>

    <h3>Diagnóstico principal</h3>
    <div class="grid two">
      <label>Código ICD-10
        <input name="dx_codigo" placeholder="G43.9">
      </label>
      <label>Descripción
        <input name="dx_desc" placeholder="Migraña no especificada">
      </label>
    </div>

    <h3>Tratamiento (medicamentos)</h3>
    <div id="meds"></div>
    <button type="button" class="btn" onclick="addMed()">Agregar medicamento</button>

    <div class="actions">
      <button class="btn btn-primary">Guardar consulta</button>
      <a class="btn" href="index.php">Cancelar</a>
    </div>
  </form>
</section>
<script>
function addAnt() {
  const wrap = document.getElementById('antecedentes');
  const div = document.createElement('div');
  div.className = 'grid two';
  div.innerHTML = `
    <label>Tipo
      <select name="ant_tipo[]">
        <option value="personal">Personal</option>
        <option value="quirurgico">Quirúrgico</option>
        <option value="familiar">Familiar</option>
        <option value="alergia">Alergia</option>
        <option value="otro">Otro</option>
      </select>
    </label>
    <label>Descripción
      <input name="ant_desc[]" placeholder="Detalle">
    </label>
  `;
  wrap.appendChild(div);
}
function addMed() {
  const wrap = document.getElementById('meds');
  const div = document.createElement('div');
  div.className = 'grid three';
  div.innerHTML = `
    <label>Nombre
      <input name="med_nombre[]" placeholder="Paracetamol">
    </label>
    <label>Dosis
      <input name="med_dosis[]" placeholder="500 mg">
    </label>
    <label>Frecuencia
      <input name="med_frecuencia[]" placeholder="Cada 8 horas por 3 días">
    </label>
  `;
  wrap.appendChild(div);
}
addAnt(); addMed();
</script>
<?php include __DIR__ . '/../partials/footer.php'; ?>
