<?php
require 'config/database.php';
$pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
 id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(50) UNIQUE NOT NULL,
 password VARCHAR(255) NOT NULL, rol ENUM('admin','usuario') DEFAULT 'usuario',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$pdo->exec("CREATE TABLE IF NOT EXISTS dibujos (
 id INT AUTO_INCREMENT PRIMARY KEY, titulo VARCHAR(100) NOT NULL, descripcion TEXT,
 archivo VARCHAR(255) NOT NULL, subido_por INT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (subido_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$pdo->exec("CREATE TABLE IF NOT EXISTS productos (
 id INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(140) NOT NULL, descripcion TEXT NULL,
 categoria VARCHAR(60) NOT NULL DEFAULT 'Print', precio INT UNSIGNED NOT NULL,
 stock INT UNSIGNED NOT NULL DEFAULT 0, imagen VARCHAR(255) NOT NULL DEFAULT 'images/demo/obra-01.svg',
 activo TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$pdo->exec("CREATE TABLE IF NOT EXISTS pedidos (
 id INT AUTO_INCREMENT PRIMARY KEY, nombre_cliente VARCHAR(120) NOT NULL,
 email VARCHAR(190) NOT NULL, telefono VARCHAR(40) NULL, direccion TEXT NOT NULL,
 notas TEXT NULL, total INT UNSIGNED NOT NULL, metodo_pago VARCHAR(40) NOT NULL DEFAULT 'Transferencia',
 estado ENUM('pendiente','pagado','preparando','enviado','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$pdo->exec("CREATE TABLE IF NOT EXISTS detalle_pedido (
 id INT AUTO_INCREMENT PRIMARY KEY, pedido_id INT NOT NULL, producto_id INT NULL,
 nombre_producto VARCHAR(140) NOT NULL, cantidad INT UNSIGNED NOT NULL,
 precio_unitario INT UNSIGNED NOT NULL,
 FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
 FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$pdo->exec("CREATE TABLE IF NOT EXISTS pagos (
 id INT AUTO_INCREMENT PRIMARY KEY, pedido_id INT NOT NULL,
 proveedor VARCHAR(50) NOT NULL, referencia VARCHAR(190) NULL,
 estado VARCHAR(40) NOT NULL DEFAULT 'pendiente', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$exists = $pdo->query("SELECT id FROM usuarios WHERE username='admin'")->fetchColumn();
if (!$exists) {
    $stmt = $pdo->prepare("INSERT INTO usuarios(username,password,rol) VALUES(?,?,'admin')");
    $stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT)]);
}
$count = (int)$pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();
if (!$count) {
    $seed = [
        ['Jardín interior (original)', 'Obra original firmada', 'Obra original', 120000, 1, 'images/demo/obra-01.svg'],
        ['Print Entre mundos A4', 'Impresión artística tamaño A4', 'Print', 12000, 15, 'images/demo/obra-02.svg'],
        ['Pack de stickers Fragmentos', 'Pack de stickers ilustrados', 'Stickers', 4500, 30, 'images/demo/obra-03.svg'],
        ['Pin Sueño turquesa', 'Pin esmaltado', 'Pines', 6000, 20, 'images/demo/obra-04.svg']
    ];
    $s = $pdo->prepare("INSERT INTO productos(nombre,descripcion,categoria,precio,stock,imagen) VALUES(?,?,?,?,?,?)");
    foreach ($seed as $p) $s->execute($p);
}
?>
<!doctype html>
<html lang="es">
<meta charset="utf-8">
<title>Instalación SHT</title>

<body style="font:16px Arial;max-width:650px;margin:50px auto;line-height:1.6">
    <h1>Instalación completada</h1>
    <p>Se crearon las tablas de tienda y pedidos y se agregaron productos de ejemplo si el catálogo estaba vacío.</p>
    <p><b>Usuario:</b> admin<br><b>Contraseña inicial:</b> admin123</p>
    <p>Inicia sesión y cambia la contraseña antes de publicar el sitio.</p>
    <p><a href="login.php">Ir al inicio de sesión</a></p>
    <p><b>Importante:</b> elimina o renombra install.php después de instalar.</p>
</body>

</html>