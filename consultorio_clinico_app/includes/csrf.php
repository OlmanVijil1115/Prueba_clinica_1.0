<?php
function csrf_token() {
    if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }
    return $_SESSION['csrf'];
}
function csrf_field() {
    $t = csrf_token();
    echo '<input type="hidden" name="csrf" value="' . e($t) . '">';
}
function verify_csrf() {
    if (!is_post()) return;
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) {
        http_response_code(403);
        exit('CSRF token inválido.');
    }
}
?>
