<?php
session_start();
require 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->query("SELECT d.*, u.username FROM dibujos d 
                     LEFT JOIN usuarios u ON d.subido_por = u.id 
                     ORDER BY d.created_at DESC");
$dibujos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Galería de Dibujos</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>🎨 Galería de Dibujos</h1>
    <nav>
        <span>Hola, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <?php if ($_SESSION['rol'] === 'admin'): ?>
            <a href="admin.php">Panel Admin</a>
        <?php endif; ?>
        <a href="logout.php">Cerrar sesión</a>
    </nav>
</header>

<main class="galeria">
    <?php if (empty($dibujos)): ?>
        <p>No hay dibujos subidos todavía.</p>
    <?php else: ?>
        <?php foreach ($dibujos as $d): ?>
            <div class="card">
                <img src="uploads/<?= htmlspecialchars($d['archivo']) ?>" alt="<?= htmlspecialchars($d['titulo']) ?>">
                <h3><?= htmlspecialchars($d['titulo']) ?></h3>
                <p><?= htmlspecialchars($d['descripcion']) ?></p>
                <small>Subido por <?= htmlspecialchars($d['username'] ?? 'Desconocido') ?></small>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>
</body>
</html>