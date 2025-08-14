<?php
require_once __DIR__ . '/../init.php';
require_login(['admin']);
$pdo = get_pdo();
$id = (int)input('id');
$pdo->prepare('DELETE FROM paciente WHERE paciente_id = ?')->execute([$id]);
flash('ok', 'Paciente eliminado');
redirect('index.php');
