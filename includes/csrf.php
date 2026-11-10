<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
function csrf_field(): string { return '<input type="hidden" name="csrf" value="'.htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8').'">'; }
function verify_csrf(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(403); exit('Solicitud no válida. Recarga la página.'); }
}
