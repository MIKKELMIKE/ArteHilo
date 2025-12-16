# Arte Hilo: E-commerce de Accesorios Artesanales

Proyecto web para la venta de pulseras, collares y accesorios hechos a mano.

## Características principales
- Catálogo de productos con imágenes y filtros avanzados (por género, tipo, destacados)
- Carrito de compras y proceso de checkout intuitivo
- Registro e inicio de sesión de usuarios con recuperación de contraseña
- Panel administrativo completo (ABC de usuarios, productos y pedidos)
- Confirmación visual y seguimiento de pedidos para el cliente
- Responsive: diseño adaptado a móvil, tablet y escritorio
- Validación de formularios en frontend y backend
- Sistema de cupones y descuentos (opcional)
- Gestión de stock y actualización automática
- Galería de imágenes para productos
- Sección de contacto y formulario de ayuda

## Instalación y configuración
1. **Clona el repositorio:**
   ```sh
   git clone https://github.com/MIKKELMIKE/ArteHilo.git
   ```
2. **Configura la base de datos:**
   - Crea una base de datos MySQL vacía.
   - Importa el archivo `arte_hilo_ionos.sql` (solicítalo si no lo tienes).
3. **Configura la conexión:**
   - Copia `includes/db.example.php` a `includes/db.php` y pon tus credenciales reales.
4. **Coloca el proyecto en tu servidor local (XAMPP, MAMP, Laragon, etc.)**
5. **Accede desde tu navegador:**
   - Ejemplo: `http://localhost/ArteHilo`

## Estructura de carpetas
- `admin/` — Panel administrativo (gestión de usuarios, productos y pedidos)
- `includes/` — Archivos de conexión y utilidades (no subas `db.php` real)
- `css/` — Estilos personalizados
- `js/` — Scripts de carrito, validación y filtros
- `img/` — Imágenes de productos y recursos visuales
- `arte_hilo_ionos.sql` — Script de base de datos (no público)

## Seguridad y buenas prácticas
- **Nunca subas `includes/db.php` con tus credenciales reales**
- `.gitignore` protege archivos sensibles y de desarrollo
- Las contraseñas de usuarios se almacenan con hash seguro (bcrypt)
- El código está preparado para entornos compartidos y producción

## Tecnologías utilizadas
- PHP 7.4+
- MySQL/MariaDB
- HTML5, CSS3, TailwindCSS
- JavaScript (vanilla)
- PDO para conexión segura a base de datos

## Créditos y contacto
Desarrollado por MIKKELMIKE
- Instagram: [@arte.hilo_](https://instagram.com/arte.hilo_)
- Facebook: [arte.hilo](https://facebook.com/arte.hilo)

---
¿Dudas, sugerencias o quieres contribuir? Abre un issue o pull request en el repositorio.
