# SHT_art — Portafolio y tienda

Proyecto PHP + MySQL para el portafolio artístico SHT, con catálogo de productos, carrito, registro de pedidos y panel de administración.

## Requisitos
- PHP 8.0 o superior (extensiones PDO, pdo_mysql y fileinfo).
- MySQL 5.7+ o MariaDB.
- Apache/Nginx configurado para PHP (por ejemplo XAMPP).

## Instalación
1. Copia la carpeta `Sht_art` dentro de `htdocs` (XAMPP) o en el directorio público de tu servidor.
2. Crea una base de datos MySQL llamada `Sht_art`, o ajusta `config/database.php` con el host, usuario, contraseña y nombre correctos.
3. Configura `DB_HOST`, `DB_NAME`, `DB_USER` y `DB_PASS` en `config/database.php`.
4. Abre `http://localhost/Sht_art/install.php`. El instalador crea las tablas `usuarios`, `dibujos`, `productos`, `pedidos`, `detalle_pedido` y `pagos`, y carga productos de muestra si el catálogo está vacío.
5. Accede a `http://localhost/Sht_art/login.php` con usuario `admin` y contraseña inicial `admin123`.
6. Entra a `admin.php` para administrar productos y pedidos. Usa `cambiar_password.php` para cambiar la contraseña inicial.
7. **Cambia la contraseña inicial del administrador y elimina o renombra `install.php` después de instalar.**

## Funcionalidades incorporadas
- Catálogo dinámico desde MySQL: categoría, precio, descripción, imagen y disponibilidad.
- Carrito guardado en sesión PHP, con cantidades y validación de stock en el servidor.
- Checkout con datos de contacto y dirección de envío.
- Registro transaccional de pedidos y sus productos; el stock se descuenta al confirmar el pedido.
- Panel de administración con indicadores, mantenimiento de productos, carga de imágenes y cambio de estado de pedidos.
- Protección básica de rutas administrativas y token CSRF.
- Estados: pendiente, pagado, preparando, enviado, entregado y cancelado.

## Pago
El checkout registra pedidos mediante **transferencia bancaria** y los deja pendientes. No cobra dinero ni confirma pagos automáticamente. Para pagos en línea se debe configurar una pasarela como Webpay o Mercado Pago y validar la confirmación desde el servidor. No marques pedidos como pagados basándote solo en una redirección del navegador.

## Notas
- No subas credenciales reales de MySQL al repositorio.
- Las imágenes cargadas desde administración se guardan en `uploads/products/`.
- `admin.php` requiere rol `admin`.
- El registro público de usuarios no está implementado; el checkout permite comprar como invitado.


## Gestión de dibujos desde administración

Desde `admin.php`, el administrador también puede publicar dibujos en la galería de la página de inicio. En la sección **Portafolio** puede agregar una imagen con título y descripción, editar sus datos o reemplazar la imagen, y eliminar dibujos. Los archivos se guardan en `uploads/artworks/` y los registros se almacenan en la tabla `dibujos` de MySQL. Los cambios aparecen automáticamente en `index.php`.
