<?php
function e($str) { return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8'); }
function redirect($path) { header('Location: ' . $path); exit; }
function is_post() { return $_SERVER['REQUEST_METHOD'] === 'POST'; }
function input($key, $default = null) { return $_POST[$key] ?? $_GET[$key] ?? $default; }
?>
