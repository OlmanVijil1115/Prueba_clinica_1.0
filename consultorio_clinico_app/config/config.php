<?php
// Configuración de base de datos y app
define('DB_HOST', 'localhost');
define('DB_NAME', 'consultorio_clinico');
define('DB_USER', 'root');
define('DB_PASS', '');

define('APP_NAME', 'ClinicaLite');

// Auto-detección de BASE_URL para XAMPP/WAMP (sin usar backslashes)
// Si tu servidor apunta el DocumentRoot a la carpeta public, BASE_URL será ''
// Si accedes como http://localhost/consultorio_clinico_app/public/, BASE_URL será '/consultorio_clinico_app/public'
$BASE_URL = '';
if ($BASE_URL === '') {
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    $p = strrpos($scriptDir, '/public');
    $auto = $p !== false ? substr($scriptDir, 0, $p + 7) : $scriptDir;
    $BASE_URL = $auto === '/' ? '' : $auto;
}
define('BASE_URL', $BASE_URL);

date_default_timezone_set('America/Guatemala');
?>
