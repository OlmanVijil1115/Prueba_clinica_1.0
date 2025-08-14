<?php
function flash($key, $message = null) {
    if ($message === null) {
        $msg = $_SESSION['flash'][$key] ?? null;
        if (isset($_SESSION['flash'][$key])) unset($_SESSION['flash'][$key]);
        return $msg;
    } else {
        $_SESSION['flash'][$key] = $message;
    }
}
?>
