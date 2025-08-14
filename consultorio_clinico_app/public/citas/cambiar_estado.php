<?php
require_once __DIR__ . '/../init.php';
require_login();
$pdo = get_pdo();
$id = (int)input('id');
$a = input('a');
$allowed = ['pendiente','confirmada','cancelada'];
if (!in_array($a, $allowed, true)) { redirect('index.php'); }
$stmt = $pdo->prepare('UPDATE cita SET estado = ?, modificado_por=?, modificado_en=NOW() WHERE cita_id = ?');
$stmt->execute([$a, current_user()['id'], $id]);
flash('ok', 'Estado actualizado');
redirect('index.php');
