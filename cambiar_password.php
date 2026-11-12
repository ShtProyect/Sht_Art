<?php
require 'config/database.php';
require 'includes/auth.php';
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$msg = '';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['actual'] ?? '';
    $new = $_POST['nueva'] ?? '';
    $confirm = $_POST['confirmar'] ?? '';
    $q = $pdo->prepare("SELECT password FROM usuarios WHERE id=?");
    $q->execute([$_SESSION['user_id']]);
    $hash = $q->fetchColumn();
    if (!$hash || !password_verify($current, $hash)) $err = 'La contraseña actual no es correcta.';
    elseif (strlen($new) < 8) $err = 'La nueva contraseña debe tener al menos 8 caracteres.';
    elseif ($new !== $confirm) $err = 'Las contraseñas nuevas no coinciden.';
    else {
        $s = $pdo->prepare("UPDATE usuarios SET password=? WHERE id=?");
        $s->execute([password_hash($new, PASSWORD_DEFAULT), $_SESSION['user_id']]);
        $msg = 'Contraseña actualizada correctamente.';
    }
}
?>
<!doctype html>
<html lang="es" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Cambiar contraseña — SHT</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/tienda.css">
</head>

<body>
    <main class="auth-page">
        <section class="auth-card">
            <p class="eyebrow">SEGURIDAD DE LA CUENTA</p>
            <h1>Cambiar <em>contraseña</em></h1><?php if ($msg): ?><p class="admin-alert success"><?= h($msg) ?></p><?php endif; ?><?php if ($err): ?><p class="admin-alert error"><?= h($err) ?></p><?php endif; ?><form method="post">
                <div class="field"><label>Contraseña actual</label><input type="password" name="actual" required></div>
                <div class="field"><label>Nueva contraseña</label><input type="password" name="nueva" minlength="8" required></div>
                <div class="field"><label>Repetir nueva contraseña</label><input type="password" name="confirmar" minlength="8" required></div><button class="button button-primary" type="submit">Actualizar contraseña</button>
            </form><a class="auth-back text-link" href="<?= ($_SESSION['rol'] ?? '') === 'admin' ? 'admin.php' : 'index.php' ?>">← Volver</a>
        </section>
    </main>
</body>

</html>