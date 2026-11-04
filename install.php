<?php
// Ejecuta este archivo una vez para crear las tablas y el admin
require 'config/database.php';

// Crear tabla usuarios
$pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin','usuario') DEFAULT 'usuario',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Crear tabla dibujos
$pdo->exec("CREATE TABLE IF NOT EXISTS dibujos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    archivo VARCHAR(255) NOT NULL,
    subido_por INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subido_por) REFERENCES usuarios(id) ON DELETE SET NULL
)");

// Crear admin por defecto (usuario: admin / contraseña: admin123)
$password = password_hash('admin123', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT IGNORE INTO usuarios (username, password, rol) VALUES (?, ?, 'admin')");
$stmt->execute(['admin', $password]);

echo "✅ Instalación completada.<br>";
echo "Usuario: <b>admin</b><br>";
echo "Contraseña: <b>admin123</b><br>";
echo "<a href='login.php'>Ir al login</a>";