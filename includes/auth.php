<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
function require_admin(): void {
    if (empty($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'admin') {
        header('Location: login.php'); exit;
    }
}
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function clp($v): string { return '$' . number_format((float)$v, 0, ',', '.'); }
