<?php
require_once __DIR__ . '/../init.php';
require_login();
header('Content-Type: application/json; charset=utf-8');
$q = trim($_GET['q'] ?? '');
if (strlen($q) < 2) { echo json_encode([]); exit; }
$pdo = get_pdo();
$stmt = $pdo->prepare('SELECT paciente_id, nombre, edad FROM paciente WHERE nombre LIKE ? ORDER BY nombre LIMIT 10');
$stmt->execute(['%' . $q . '%']);
echo json_encode($stmt->fetchAll());
