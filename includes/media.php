<?php
/** Rutas públicas de ilustraciones y productos. No aceptar rutas arbitrarias del navegador. */
function artwork_public_url(string $stored): string {
    $path = str_replace('\\', '/', trim($stored));
    // Compatibilidad con: artworks/x.jpg, uploads/artworks/x.jpg y artworks/artworks/x.jpg.
    $path = (string)preg_replace('#^(?:uploads/)?(?:artworks/)+#i', '', $path);
    if (!preg_match('/\A[a-zA-Z0-9][a-zA-Z0-9._-]*\.(?:jpe?g|png|webp|gif)\z/i', $path)) {
        return 'images/demo/obra-01.svg';
    }
    return 'uploads/artworks/' . rawurlencode($path);
}
function artwork_storage_path(string $stored): ?string {
    $url = artwork_public_url($stored);
    if (strpos($url, 'uploads/artworks/') !== 0) return null;
    return dirname(__DIR__) . '/uploads/artworks/' . basename(rawurldecode($url));
}
function product_public_url(string $stored): string {
    $path = str_replace('\\', '/', trim($stored));
    if (preg_match('#\A(?:assets/artworks/|images/demo/)(obra-0[1-4]\.svg)\z#i', $path, $m)) {
        return 'images/demo/' . $m[1];
    }
    if (preg_match('#\Auploads/products/([a-zA-Z0-9][a-zA-Z0-9._-]*\.(?:jpe?g|png|webp|gif))\z#i', $path, $m)) {
        return 'uploads/products/' . rawurlencode($m[1]);
    }
    return 'images/demo/obra-01.svg';
}
