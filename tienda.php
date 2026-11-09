<?php
session_start();
// SHT — tienda. Productos de muestra: reemplazar por datos de la BD cuando esté lista.
$products = [
 ['id'=>'orig-01','name'=>'Jardín interior (original)','type'=>'Obra original','price'=>120000,'image'=>'assets/artworks/obra-01.svg'],
 ['id'=>'print-02','name'=>'Print "Entre mundos" A4','type'=>'Print','price'=>12000,'image'=>'assets/artworks/obra-02.svg'],
 ['id'=>'stick-03','name'=>'Pack de stickers "Fragmentos"','type'=>'Stickers','price'=>4500,'image'=>'assets/artworks/obra-03.svg'],
 ['id'=>'pin-04','name'=>'Pin esmaltado "Sueño turquesa"','type'=>'Pines','price'=>6000,'image'=>'assets/artworks/obra-04.svg'],
];
function clp(int $n): string { return '$' . number_format($n, 0, ',', '.'); }
?>
<!doctype html>
<html lang="es" data-theme="dark">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#21171f"><meta name="description" content="Tienda SHT — obras originales, prints, stickers y pines.">
  <title>Tienda — SHT</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/tienda.css">
</head>
<body>
<a class="skip-link" href="#contenido">Saltar al contenido</a>
<header class="site-header">
  <a class="brand" href="index.php#inicio" aria-label="SHT, inicio"><span class="brand-mark">S</span><span>SHT<span class="brand-dot">.</span></span></a>
  <button class="menu-toggle" aria-label="Abrir menú" aria-expanded="false">☰</button>
  <nav class="main-nav" aria-label="Navegación principal">
    <a href="index.php#galeria">Galería</a><a href="index.php#sobre">Sobre SHT</a><a href="index.php#contacto">Contacto</a>
    <a class="nav-shop" href="tienda.php" aria-current="page">Tienda <span aria-hidden="true">🛒</span></a>
  </nav>
  <div class="header-actions">
    <button class="cart-toggle" type="button" aria-label="Abrir carrito">Carrito <span class="cart-count" id="cart-count">0</span></button>
    <button class="theme-toggle" type="button" aria-label="Cambiar a tema claro"><span class="theme-icon">☼</span><span class="theme-label">Modo claro</span></button>
    <?php if (!empty($_SESSION['user'])): ?>
      <a class="login-btn" href="logout.php" aria-label="Cerrar sesión"><span class="login-icon" aria-hidden="true">👤</span><span class="login-label">Salir (<?= htmlspecialchars($_SESSION['user']['nombre']) ?>)</span></a>
    <?php else: ?>
      <a class="login-btn" href="login.php" aria-label="Iniciar sesión"><span class="login-icon" aria-hidden="true">👤</span><span class="login-label">Iniciar sesión</span></a>
    <?php endif; ?>
  </div>
</header>

<main id="contenido" class="section-wrap shop-page">
  <div class="section-heading">
    <div><p class="eyebrow">TIENDA</p><h1>Obras y <em>objetos</em></h1></div>
    <p class="section-note">Originales, prints, stickers y pines.<br>Productos de muestra por ahora.</p>
  </div>

  <div class="shop-filters" role="group" aria-label="Filtrar productos">
    <button class="chip is-active" data-filter="all" type="button">Todo</button>
    <?php foreach (array_unique(array_column($products, 'type')) as $t): ?>
      <button class="chip" data-filter="<?= htmlspecialchars($t, ENT_QUOTES) ?>" type="button"><?= htmlspecialchars($t) ?></button>
    <?php endforeach; ?>
  </div>

  <div class="shop-grid">
  <?php foreach ($products as $p): ?>
    <article class="product-card" data-type="<?= htmlspecialchars($p['type'], ENT_QUOTES) ?>">
      <div class="product-image"><img loading="lazy" src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>"></div>
      <div class="product-info">
        <p class="work-category"><?= htmlspecialchars($p['type']) ?></p>
        <h3><?= htmlspecialchars($p['name']) ?></h3>
        <div class="product-buy">
          <strong class="product-price"><?= clp($p['price']) ?></strong>
          <button class="button button-primary add-to-cart" type="button"
            data-id="<?= htmlspecialchars($p['id'], ENT_QUOTES) ?>"
            data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
            data-price="<?= (int)$p['price'] ?>">Agregar</button>
        </div>
      </div>
    </article>
  <?php endforeach; ?>
  </div>
</main>

<aside class="cart-drawer" id="cart-drawer" aria-label="Carrito de compras" hidden>
  <div class="cart-head"><h2>Tu carrito</h2><button class="cart-close" type="button" aria-label="Cerrar carrito">×</button></div>
  <ul class="cart-list" id="cart-list"></ul>
  <p class="cart-empty" id="cart-empty">Tu carrito está vacío. Agrega un producto para empezar.</p>
  <div class="cart-foot">
    <div class="cart-total"><span>Total</span><strong id="cart-total">$0</strong></div>
    <button class="button button-primary" id="checkout" type="button" disabled>Pagar (próximamente)</button>
  </div>
</aside>
<div class="cart-backdrop" id="cart-backdrop" hidden></div>

<footer class="site-footer"><a class="brand" href="index.php#inicio"><span class="brand-mark">S</span><span>SHT<span class="brand-dot">.</span></span></a><p>Un universo ilustrado, hecho con intención.</p><a href="index.php" class="back-top">VOLVER AL PORTAFOLIO ←</a><small>© <?= date('Y') ?> SHT · Portafolio artístico</small></footer>

<script src="js/app.js"></script>
<script>
(function () {
  var cart = []; // en memoria; luego se puede guardar en sesión o BD
  var $ = function (s) { return document.querySelector(s); };
  var drawer = $('#cart-drawer'), backdrop = $('#cart-backdrop');
  var fmt = function (n) { return '$' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); };

  function render() {
    var list = $('#cart-list'); list.innerHTML = '';
    var total = 0, count = 0;
    cart.forEach(function (it) {
      total += it.price * it.qty; count += it.qty;
      var li = document.createElement('li');
      li.innerHTML = '<div><strong></strong><small></small></div>' +
        '<div class="cart-qty"><button type="button" data-act="minus" aria-label="Quitar uno">−</button><span></span>' +
        '<button type="button" data-act="plus" aria-label="Agregar uno">+</button></div>';
      li.querySelector('strong').textContent = it.name;
      li.querySelector('small').textContent = fmt(it.price) + ' c/u';
      li.querySelector('.cart-qty span').textContent = it.qty;
      li.dataset.id = it.id;
      list.appendChild(li);
    });
    $('#cart-total').textContent = fmt(total);
    $('#cart-count').textContent = count;
    $('#cart-empty').hidden = cart.length > 0;
    $('#checkout').disabled = true; // activar al integrar Webpay / Mercado Pago
  }
  function openCart(o) { drawer.hidden = !o; backdrop.hidden = !o; document.body.classList.toggle('cart-open', o); }

  document.querySelectorAll('.add-to-cart').forEach(function (b) {
    b.addEventListener('click', function () {
      var id = b.dataset.id, f = cart.find(function (i) { return i.id === id; });
      if (f) f.qty++; else cart.push({ id: id, name: b.dataset.name, price: +b.dataset.price, qty: 1 });
      render(); openCart(true);
    });
  });
  $('#cart-list').addEventListener('click', function (e) {
    var a = e.target.dataset.act; if (!a) return;
    var id = e.target.closest('li').dataset.id, it = cart.find(function (i) { return i.id === id; });
    it.qty += a === 'plus' ? 1 : -1;
    if (it.qty <= 0) cart = cart.filter(function (i) { return i.id !== id; });
    render();
  });
  document.querySelector('.cart-toggle').addEventListener('click', function () { openCart(true); });
  document.querySelector('.cart-close').addEventListener('click', function () { openCart(false); });
  backdrop.addEventListener('click', function () { openCart(false); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') openCart(false); });

  document.querySelectorAll('.chip').forEach(function (c) {
    c.addEventListener('click', function () {
      document.querySelectorAll('.chip').forEach(function (x) { x.classList.toggle('is-active', x === c); });
      document.querySelectorAll('.product-card').forEach(function (p) {
        p.hidden = c.dataset.filter !== 'all' && p.dataset.type !== c.dataset.filter;
      });
    });
  });
  render();
})();
</script>
</body></html>