<?php
function find_user_by_username($username) {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT * FROM usuario WHERE username = ?');
    $stmt->execute([$username]);
    return $stmt->fetch();
}
function login($username, $password) {
    $user = find_user_by_username($username);
    if (!$user) return false;
    $hash = $user['password_hash'];
    $ok = false;
    if (preg_match('/^\$2y\$/', $hash)) {
        $ok = password_verify($password, $hash);
    } else {
        // Migración automática desde texto plano a bcrypt (60 chars)
        if ($hash === $password) {
            $new = password_hash($password, PASSWORD_BCRYPT);
            $pdo = get_pdo();
            $upd = $pdo->prepare('UPDATE usuario SET password_hash = ?, modificado_en = NOW(), modificado_por = ? WHERE usuario_id = ?');
            $upd->execute([$new, $user['usuario_id'], $user['usuario_id']]);
            $ok = true;
        }
    }
    if (!$ok) return false;
    $_SESSION['user'] = [
        'id' => (int)$user['usuario_id'],
        'username' => $user['username'],
        'rol' => $user['rol'],
    ];
    try {
        get_pdo()->prepare('UPDATE usuario SET modificado_en = NOW() WHERE usuario_id = ?')->execute([$user['usuario_id']]);
    } catch (Exception $e) {}
    return true;
}
function require_login($roles = null) {
    if (empty($_SESSION['user'])) { redirect(BASE_URL . '/index.php'); }
    if ($roles) {
        $roles = is_array($roles) ? $roles : [$roles];
        if (!in_array($_SESSION['user']['rol'], $roles, true)) {
            http_response_code(403);
            exit('No tienes permiso para acceder aquí.');
        }
    }
}
function current_user() { return $_SESSION['user'] ?? null; }
function logout() { session_destroy(); redirect(BASE_URL . '/index.php'); }
?>
