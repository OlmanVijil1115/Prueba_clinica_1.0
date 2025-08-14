# ClinicaLite (PHP + MySQL)

Aplicación mínima y moderna para consultorio/clinica médica con login por roles (admin, medico, enfermero), gestión de pacientes, citas y consultas clínicas.

## Requisitos
- PHP 8.x con extensiones PDO y MySQL habilitadas
- MySQL/MariaDB
- Servidor web (Apache/Nginx). En Apache, idealmente apunta tu DocumentRoot a la carpeta `public/`.

## Instalación rápida
1. Importa tu base de datos: usa el archivo SQL que ya tienes (`consultorio_clinico.sql`).
   - Nota: La app soporta el schema tal cual. La contraseña del usuario `admin` se migrará automáticamente a hash seguro en el primer login si está en texto plano (admin123).
2. Copia la carpeta `consultorio_clinico_app` a tu servidor (por ejemplo `htdocs/consultorio_clinico_app`).
3. Configura `config/config.php` con tus credenciales de BD y BASE_URL si tu `public/` no es el DocumentRoot.
4. Accede a `http://localhost/consultorio_clinico_app/public/` (o la ruta que corresponda).
5. Inicia sesión con: usuario `admin` y contraseña `admin123` (se guardará con bcrypt en ese momento).

## Características
- Autenticación con sesiones, hashing de contraseñas (bcrypt), protección CSRF en formularios y consultas preparadas (PDO).
- Roles: admin, medico, enfermero (control de acceso básico por módulo).
- Pacientes: alta, edición, eliminación, búsqueda por nombre, ficha con historial.
- Citas: agenda por fecha, asignación a médico/enfermero, cambio de estado (pendiente, confirmada, cancelada) y registro rápido de consulta.
- Consultas: motivo, historia, examen físico (con IMC), antecedentes, diagnóstico ICD-10 y tratamiento (múltiples medicamentos).
- UI minimalista con micro-animaciones y componentes sencillos (cards, tablas, badges, toasts).

## Estructura
- `public/`: punto de entrada web (login, dashboard y módulos). Si tu servidor no apunta aquí, usa la ruta `/public` en la URL.
- `includes/`: librerías PHP (DB, auth, csrf, utilidades, flash).
- `config/`: configuración de la app.
- `public/assets/`: CSS y JS.

## Notas de seguridad
- Esta base es educativa/ligera. Antes de producción añade HTTPS, políticas de contraseñas, logs de auditoría, límites de tasa (rate limiting), y backups.
- El campo `usuario.password_hash` del SQL original es `char(60)`. Usamos bcrypt para garantizar compatibilidad (60 chars). Si deseas usar Argon2, cambia el tipo a `varchar(255)` y ajusta `password_hash`.

## Personalización
- Cambia colores/tipografía en `public/assets/css/styles.css`.
- Agrega campos a pacientes/consultas editando formularios y queries.
- Agrega nuevos roles añadiéndolos al enum `usuario.rol` y a las validaciones de `require_login`.

¡Éxitos!
