<?php
// SHT — portafolio artístico. Funciona como demo sin BD y usa MySQL si está configurado.
$works = [];
try {
    if (file_exists(__DIR__ . '/config/database.php')) {
        require __DIR__ . '/config/database.php';
        if (isset($pdo)) {
            $stmt = $pdo->query("SELECT titulo, descripcion, archivo, created_at FROM dibujos ORDER BY created_at DESC");
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $i => $d) {
                $works[] = ['title'=>$d['titulo'], 'description'=>$d['descripcion'] ?: 'Una pieza del universo visual de SHT.', 'image'=>'uploads/'.rawurlencode($d['archivo']), 'date'=>!empty($d['created_at']) ? date('Y', strtotime($d['created_at'])) : 'Obra'];
            }
        }
    }
} catch (Throwable $e) { /* La portada sigue funcionando en modo demostración. */ }
$demo = [
 ['title'=>'Jardín interior','description'=>'Estudio de color, naturaleza y memoria. Obra de muestra para reemplazar por una ilustración del artista.','image'=>'assets/artworks/obra-01.svg','date'=>'Estudio visual'],
 ['title'=>'Entre mundos','description'=>'Personajes y formas que conviven en un mismo universo imaginario.','image'=>'assets/artworks/obra-02.svg','date'=>'Exploración'],
 ['title'=>'Fragmentos','description'=>'Una composición sobre movimiento, textura y pequeños detalles.','image'=>'assets/artworks/obra-03.svg','date'=>'Colección'],
 ['title'=>'Sueño turquesa','description'=>'Contraste de tonos profundos con acentos turquesa.','image'=>'assets/artworks/obra-04.svg','date'=>'Color'],
];
if (!$works) $works = $demo;
?>
<!doctype html>
<html lang="es" data-theme="dark">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#21171f"><meta name="description" content="SHT — portafolio e ilustraciones.">
  <title>SHT — Portafolio artístico</title><link rel="stylesheet" href="css/style.css">
</head>
<body>
<a class="skip-link" href="#contenido">Saltar al contenido</a>
<header class="site-header">
  <a class="brand" href="#inicio" aria-label="SHT, inicio"><span class="brand-mark">S</span><span>SHT<span class="brand-dot">.</span></span></a>
  <button class="menu-toggle" aria-label="Abrir menú" aria-expanded="false">☰</button>
  <nav class="main-nav" aria-label="Navegación principal"><a href="#galeria">Galería</a><a href="#sobre">Sobre SHT</a><a href="#contacto">Contacto</a></nav>
  <button class="theme-toggle" type="button" aria-label="Cambiar a tema claro"><span class="theme-icon">☼</span><span class="theme-label">Modo claro</span></button>
</header>
<main id="contenido">
  <section class="hero" id="inicio">
    <div class="hero-orbit orbit-one"></div><div class="hero-orbit orbit-two"></div>
    <div class="hero-copy"><p class="eyebrow"><span></span> PORTAFOLIO DE ILUSTRACIÓN</p><h1>Un mundo propio,<br><em>trazo a trazo.</em></h1><p class="hero-lead">Bienvenido al universo visual de <strong>SHT</strong>. Un espacio para descubrir ilustraciones, ideas y pequeños mundos hechos a mano.</p><div class="hero-actions"><a class="button button-primary" href="#galeria">Explorar obras <span>↘</span></a><a class="text-link" href="#sobre">Conocer al artista <span>→</span></a></div></div>
    <div class="hero-art" aria-label="Composición abstracta decorativa"><div class="art-frame"><img src="assets/artworks/obra-01.svg" alt="Ilustración abstracta de muestra en tonos burdeos y turquesa"></div><div class="art-sticker">ARTE<br>INDEPENDIENTE</div><div class="art-caption"><span>01 / UNIVERSO SHT</span><span>✳</span></div></div>
    <a class="scroll-cue" href="#galeria"><span class="scroll-line"></span> DESLIZA PARA EXPLORAR</a>
  </section>
  <section class="intro-strip"><p>ILUSTRACIÓN · COLOR · EXPERIMENTACIÓN</p><span>✳</span><p>UNA MIRADA PERSONAL AL ARTE</p></section>
  <section class="gallery-section section-wrap" id="galeria">
    <div class="section-heading"><div><p class="eyebrow">01 — SELECCIÓN DE TRABAJOS</p><h2>La <em>galería</em></h2></div><p class="section-note">Cada pieza guarda una idea, un momento<br>o una historia. Explora sin prisa.</p></div>
    <div class="gallery-grid">
    <?php foreach ($works as $i=>$w): ?>
      <article class="work-card reveal" style="--delay:<?= (int)($i%4)*90 ?>ms">
        <button class="work-image" type="button" data-image="<?= htmlspecialchars($w['image'], ENT_QUOTES) ?>" data-title="<?= htmlspecialchars($w['title'], ENT_QUOTES) ?>" data-description="<?= htmlspecialchars($w['description'], ENT_QUOTES) ?>" aria-label="Ampliar obra: <?= htmlspecialchars($w['title']) ?>"><img loading="lazy" src="<?= htmlspecialchars($w['image']) ?>" alt="<?= htmlspecialchars($w['title']) ?>"><span class="image-open">↗</span></button>
        <div class="work-meta"><div><p class="work-category"><?= htmlspecialchars($w['date']) ?></p><h3><?= htmlspecialchars($w['title']) ?></h3></div><span class="work-number"><?= str_pad((string)($i+1),2,'0',STR_PAD_LEFT) ?></span></div>
        <p class="work-description"><?= htmlspecialchars($w['description']) ?></p>
      </article>
    <?php endforeach; ?>
    </div>
    <p class="gallery-footnote">Las imágenes ilustrativas de esta versión deben reemplazarse por las obras y textos definitivos del artista.</p>
  </section>
  <section class="about-section" id="sobre"><div class="about-inner section-wrap"><div class="about-symbol">S<span>✳</span></div><div class="about-copy"><p class="eyebrow">02 — DETRÁS DE LAS OBRAS</p><h2>Hola, soy <em>SHT.</em></h2><p>Este espacio reúne ilustraciones, experimentos visuales y procesos creativos. Cada obra es una invitación a mirar con curiosidad y encontrar una historia propia.</p><p class="about-small">Este texto es provisional: aquí irá la presentación personal que el artista quiera compartir.</p><a class="text-link" href="#contacto">Hablemos de proyectos <span>→</span></a></div></div></section>
  <section class="contact-section section-wrap" id="contacto"><div><p class="eyebrow">03 — CONVERSEMOS</p><h2>¿Tienes una idea<br>en <em>mente?</em></h2><p class="contact-lead">Para consultas, colaboraciones o proyectos, puedes contactar directamente al artista.</p></div><div class="contact-links"><a href="mailto:correo@ejemplo.com" class="contact-card"><span>Correo electrónico</span><strong>Escríbeme <b>↗</b></strong><small>correo@ejemplo.com · reemplazar</small></a><a href="https://instagram.com/" target="_blank" rel="noopener noreferrer" class="contact-card"><span>Instagram</span><strong>Ver perfil <b>↗</b></strong><small>@usuario · reemplazar enlace</small></a></div></section>
</main>
<footer class="site-footer"><a class="brand" href="#inicio"><span class="brand-mark">S</span><span>SHT<span class="brand-dot">.</span></span></a><p>Un universo ilustrado, hecho con intención.</p><a href="#inicio" class="back-top">VOLVER ARRIBA ↑</a><small>© <?= date('Y') ?> SHT · Portafolio artístico</small></footer>
<div class="lightbox" role="dialog" aria-modal="true" aria-label="Vista ampliada de la obra" hidden><button class="lightbox-close" aria-label="Cerrar">×</button><img src="" alt=""><div class="lightbox-copy"><h3></h3><p></p></div></div>
<script src="js/app.js"></script>
</body></html>
