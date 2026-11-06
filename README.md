# SHT — Portafolio artístico (prototipo actualizado)

Esta versión adapta el proyecto PHP existente a la revisión del cliente: identidad SHT, paleta burdeos/turquesa, tema oscuro y claro, galería de mayor tamaño, descripciones, aparición progresiva al desplazarse, vista ampliada y contacto por correo/Instagram.

## Ejecutar en XAMPP
1. Copia la carpeta `Sht_Art` a `C:\xampp\htdocs\`.
2. Inicia Apache en XAMPP. MySQL no es obligatorio para visualizar la portada de demostración.
3. Abre `http://localhost/Sht_Art/`.

También puedes ejecutar desde la carpeta del proyecto: `php -S localhost:8000` y abrir `http://localhost:8000`.

## Datos que debe completar el equipo
- Reemplazar las cuatro imágenes SVG de demostración en `assets/artworks/` por ilustraciones autorizadas del artista.
- Cambiar títulos y descripciones de muestra en `index.php` si aún no hay base de datos conectada.
- Reemplazar `correo@ejemplo.com` y `https://instagram.com/` por los datos reales confirmados por el cliente.
- Completar la presentación provisional de la sección “Sobre SHT”.

## MySQL (opcional para esta demo)
La portada intenta consultar la tabla `dibujos` usando `config/database.php`. Si la conexión no está disponible, muestra las obras SVG de demostración. Si se habilita MySQL, configura las credenciales para tu entorno y ejecuta `install.php` solo si realmente necesitas las tablas heredadas. No publiques credenciales reales ni dejes el instalador accesible en producción.

## Nota sobre funcionalidades heredadas
Los archivos `login.php`, `admin.php`, `logout.php` e `install.php` se conservan como parte del código recibido, pero no aparecen en la navegación pública porque autenticación y panel de administración no están confirmados dentro del MVP actual.

## Accesibilidad considerada
- Contraste y tipografía legible.
- Control manual de tema oscuro/claro.
- Estados de foco visibles y enlace para saltar al contenido.
- Respeto a `prefers-reduced-motion`.
- La animación de entrada no es esencial para acceder al contenido.

Este paquete es un prototipo funcional en PHP/CSS/JS inspirado en un diseño de alta fidelidad; no es un archivo nativo `.fig` de Figma.
