# Arte Hilo: E-commerce de Accesorios Artesanales

Proyecto web para la venta de pulseras, collares y accesorios hechos a mano.

## Características principales
- Catálogo de productos con imágenes y filtros
- Carrito de compras y checkout
- Registro e inicio de sesión de usuarios
- Panel administrativo (ABC de usuarios, productos y pedidos)
- Confirmación y seguimiento de pedidos
- Responsive (adaptado a móvil y escritorio)

## Instalación
1. **Clona el repositorio:**
   ```sh
   git clone https://github.com/MIKKELMIKE/ArteHilo.git
   ```
2. **Configura la base de datos:**
   - Crea una base de datos MySQL.
   - Importa el archivo `arte_hilo_ionos.sql` (no incluido en el repo, solicítalo si lo necesitas).
3. **Configura la conexión:**
   - Copia `includes/db.example.php` a `includes/db.php` y pon tus credenciales.
4. **Coloca el proyecto en tu servidor local (XAMPP, MAMP, etc.)**
5. **Accede desde tu navegador:**
   - Ejemplo: `http://localhost/ArteHilo`

## Archivos importantes
- `includes/db.example.php`: Ejemplo de configuración de base de datos (no contiene datos reales)
- `.gitignore`: Protege archivos sensibles y de desarrollo
- `admin/`: Panel administrativo
- `css/`, `js/`, `img/`: Recursos estáticos

## Seguridad
- No se suben contraseñas ni datos sensibles al repositorio
- Usa `.gitignore` para proteger archivos críticos

## Créditos
Desarrollado por MIKKELMIKE

---
¿Dudas? Abre un issue en el repositorio.
