<?php
session_start();
require 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagen'])) {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $file = $_FILES['imagen'];

    if ($titulo && $file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ext, $permitidas) && $file['size'] <= 5 * 1024 * 1024) {
            $nombreArchivo = uniqid('dib_') . '.' . $ext;
            $destino = 'uploads/' . $nombreArchivo;

            if (move_uploaded_file($file['tmp_name'], $destino)) {
                $stmt = $pdo->prepare("INSERT INTO dibujos (titulo, descripcion, archivo, subido_por) VALUES (?, ?, ?, ?)");
                $stmt->execute([$titulo, $descripcion, $nombreArchivo, $_SESSION['user_id']]);
                $mensaje = "✅ Dibujo subido correctamente";
            } else {
                $mensaje = "❌ Error al mover el archivo";
            }
        } else {
            $mensaje = "❌ Formato no permitido o archivo demasiado grande (máx 5MB)";
        }
    } else {
        $mensaje = "❌ Completa todos los campos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>⚙️ Panel de Administración</h1>
    <nav>
        <a href="galeria.php">Ver Galería</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>
</header>

<main class="admin-panel">
    <?php if ($mensaje): ?>
        <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <h2>Subir nuevo dibujo</h2>
        <input type="text" name="titulo" placeholder="Título" required>
        <textarea name="descripcion" placeholder="Descripción (opcional)"></textarea>
        <input type="file" name="imagen" accept="image/*" required>
        <button type="submit">Subir dibujo</button>
    </form>
</main>
</body>
</html>