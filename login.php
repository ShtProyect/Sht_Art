<?php
session_start();
require 'config/database.php';
require 'includes/auth.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    $q = $pdo->prepare("SELECT * FROM usuarios WHERE username=?");
    $q->execute([$u]);
    $user = $q->fetch();
    if ($user && password_verify($p, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['rol'] = $user['rol'];
        header('Location: ' . ($user['rol'] === 'admin' ? 'admin.php' : 'index.php'));
        exit;
    }
    $error = 'Usuario o contraseña incorrectos.';
}
?>
<!doctype html>
<html lang="es" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Iniciar sesión — SHT</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/tienda.css">
</head>

<body>
    <main class="auth-page">
        <section class="auth-card">
            <p class="eyebrow">CUENTA SHT</p>
            <h1>Iniciar <em>sesión</em></h1>
            <p class="auth-lead">Accede a tu cuenta para continuar.</p>
            <?php if ($error): ?><p class="auth-error"><?= h($error) ?></p><?php endif; ?>
            <form method="post">
                <div class="field"><label for="username">Usuario</label><input id="username" name="username" required autocomplete="username"></div>
                <div class="field"><label for="password">Contraseña</label><input id="password" name="password" type="password" required autocomplete="current-password"></div><button class="button button-primary" type="submit">Entrar</button>
            </form><a class="auth-back text-link" href="index.php">← Volver al portafolio</a>
        </section>
    </main>
</body>

</html>