<?php
// Configuración local de MySQL. Ajusta estos datos en tu entorno XAMPP/cPanel.
define('DB_HOST', 'localhost');
define('DB_NAME', 'Sht_art');
define('DB_USER', 'root');
define('DB_PASS', '');

$pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER,
    DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);
