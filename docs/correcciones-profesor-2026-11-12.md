# Correcciones solicitadas por el profesor — 12-11-2026

Base: `feature-dashboard`. Rama de trabajo: `feature/cambios` (no mezclar sin revisión).

## Cambios preparados
- Se corrigió el guardado de dibujos nuevos: ya no se duplica `artworks/` en la base de datos.
- Los nombres de archivos nuevos incorporan ID de la obra, título normalizado y sufijo aleatorio; se conserva el ID y título en la tabla `dibujos`.
- Se muestran ID y título en el dashboard, y se usa una única función para resolver rutas de las imágenes.
- Compatibilidad con rutas históricas como `artworks/artworks/imagen.jpg` y `uploads/artworks/imagen.jpg`, sin borrar registros de BD.
- Las imágenes SVG de demostración se reubicaron en `images/demo/`, eliminando `assets/`; las cargas reales siguen en `uploads/artworks`.
- La portada muestra la imagen de la primera obra real cuando existe.
- Se agregó el menú móvil del panel administrativo, estados accesibles del menú y ajustes responsive.
- Se normalizan rutas antiguas de productos en la vista, sin migrar registros existentes.

## Pruebas obligatorias en XAMPP/cPanel antes de aprobar
1. Ejecutar `php -l` en `index.php`, `admin.php`, `tienda.php`, `install.php`, `includes/media.php`.
2. Subir una obra JPG, PNG o WEBP en el dashboard; comprobar ID, título, ruta final, portada y vista ampliada.
3. Editar una obra sin reemplazar imagen, luego reemplazarla; confirmar que el archivo previo se retira cuando corresponde.
4. Borrar una obra y comprobar eliminación de archivo y registro.
5. Probar galería con ruta heredada `artworks/artworks/archivo.jpg` (si existen registros afectados).
6. Probar inicio, tienda, carrito y panel en 320, 375, 768 y 1280 px; abrir/cerrar menú con toque y tecla Escape.
7. Verificar que ninguna referencia a `assets/` queda en archivos del proyecto (salvo documentación histórica).
8. Probar enlaces y persistencia con una base de datos de prueba y hacer respaldo antes de producción.

## Riesgos pendientes fuera de esta corrección
- **Crítico**: `install.php` incluye creación de usuario administrativo con clave predeterminada `admin123`. Proteger/eliminar en producción y cambiar toda credencial inicial.
- `config/database.php` contiene configuración local: externalizar credenciales a un archivo no versionado o variables de entorno antes de despliegue.
- No están configuradas ni certificadas las pasarelas PayPal, Mercado Pago o BancoEstado; no declarar pagos automáticos funcionales.
- Reemplazar correo/Instagram provisionales y obras demo por información autorizada del cliente.
- Revisar sanitización, control de stock y confirmación de pagos del checkout antes de aceptar transacciones reales.

Estado: cambios preparados para revisión; despliegue y pruebas con servidor web/base de datos reales PENDIENTES.
