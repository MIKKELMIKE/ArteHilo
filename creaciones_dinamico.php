<?php
$productos = [];
try {
  // Usar getDB() en lugar de global $pdo
  $db = getDB();
  $stmt = $db->prepare("
    SELECT id, nombre, descripcion, precio, imagen_url, categoria, genero, 
        materiales, stock, activo, destacado, tiempo_produccion, cuidados
    FROM productos 
    WHERE activo = 1 
    ORDER BY destacado DESC, categoria, nombre
  ");
  $stmt->execute();
  $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  // Debug: descomentar para ver errores
  // echo "Error: " . $e->getMessage();
  $productos = [];
}
?>

<section class="page-header mb-10" aria-labelledby="creaciones-heading">
  <h1 id="creaciones-heading" class="text-4xl md:text-5xl font-bold text-center text-gray-900 mb-4">
     Nuestras Creaciones
  </h1>
  <p class="text-center text-gray-600 max-w-2xl mx-auto text-lg mb-6">
    Cada pieza es única y elaborada con amor. Explora nuestro catálogo y encuentra el accesorio perfecto para ti.
  </p>
  
  <div class="max-w-xl mx-auto">
    <div class="relative">
      <input type="text" id="search-input" placeholder=" Buscar productos por nombre..."
          class="w-full px-4 py-3 pl-12 rounded-lg border-2 border-earth-medium focus:border-primary focus:outline-none transition-colors"
          aria-label="Buscar productos">
      <svg class="absolute left-4 top-4 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
      </svg>
    </div>
  </div>
</section>

<nav class="filter-navigation mb-10" aria-label="Filtros de productos">
  <div class="flex justify-center flex-wrap gap-2 md:gap-3">
    <button class="gender-filter-btn bg-primary text-white font-semibold py-2 px-6 rounded-lg transition-all duration-300 shadow-md hover:shadow-lg hover:bg-primary-dark" data-gender="todos" aria-pressed="true">Todos</button>
    <button class="gender-filter-btn bg-earth-light text-gray-700 font-semibold py-2 px-5 rounded-lg border border-earth-medium transition-all duration-300 hover:bg-pink-100 hover:border-pink-500" data-gender="Dama"> Dama</button>
    <button class="gender-filter-btn bg-earth-light text-gray-700 font-semibold py-2 px-5 rounded-lg border border-earth-medium transition-all duration-300 hover:bg-blue-100 hover:border-blue-500" data-gender="Caballero"> Caballero</button>
    <span class="border-l-2 border-gray-300 mx-1"></span>
    <button class="filter-btn bg-earth-light text-gray-700 font-semibold py-2 px-4 rounded-lg border border-earth-medium transition-all duration-300 hover:bg-secondary hover:border-primary" data-filter="Pulsera">Pulseras</button>
    <button class="filter-btn bg-earth-light text-gray-700 font-semibold py-2 px-4 rounded-lg border border-earth-medium transition-all duration-300 hover:bg-secondary hover:border-primary" data-filter="Collar">Collares</button>
    <button class="filter-btn bg-earth-light text-gray-700 font-semibold py-2 px-4 rounded-lg border border-earth-medium transition-all duration-300 hover:bg-secondary hover:border-primary" data-filter="Llavero">Llaveros</button>
    <button class="filter-btn bg-earth-light text-gray-700 font-semibold py-2 px-4 rounded-lg border border-earth-medium transition-all duration-300 hover:bg-secondary hover:border-primary" data-filter="Tobillera">Tobilleras</button>
  </div>
</nav>

<div class="text-center mb-6">
  <p class="text-gray-600"><span id="product-count"><?= count($productos) ?></span> productos encontrados</p>
</div>

<section class="products-gallery mb-12" aria-label="Catálogo de productos">
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="products-grid">
    
    <?php if (empty($productos)): ?>
      <div class="col-span-full text-center py-12">
        <p class="text-xl text-gray-500">No hay productos disponibles en este momento</p>
      </div>
    <?php else: ?>
      <?php foreach ($productos as $producto): ?>
        <article class="product-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" 
             data-category="<?= e($producto['categoria']) ?>" 
             data-gender="<?= e($producto['genero']) ?>"
             data-name="<?= strtolower(e($producto['nombre'])) ?>">
          
          <div class="relative aspect-square bg-gray-200 overflow-hidden group">
            <img src="<?= e($producto['imagen_url']) ?>" 
               alt="<?= e($producto['nombre']) ?>" 
               class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
               loading="lazy"
               onerror="this.src='img/logo.png'">
            
            <div class="absolute top-3 left-3 flex flex-col gap-2">
              <span class="bg-primary text-white text-xs px-3 py-1 rounded-full font-semibold shadow-md"><?= e($producto['categoria']) ?></span>
              <?php if ($producto['destacado']): ?>
              <span class="bg-yellow-500 text-white text-xs px-3 py-1 rounded-full font-semibold shadow-md">⭐ Destacado</span>
              <?php endif; ?>
            </div>
            
            <?php if ($producto['stock'] <= 5 && $producto['stock'] > 0): ?>
              <div class="absolute top-3 right-3">
                <span class="bg-red-500 text-white text-xs px-3 py-1 rounded-full font-semibold shadow-md">¡Quedan <?= $producto['stock'] ?>!</span>
              </div>
            <?php elseif ($producto['stock'] == 0): ?>
              <div class="absolute top-3 right-3">
                <span class="bg-gray-700 text-white text-xs px-3 py-1 rounded-full font-semibold shadow-md">Agotado</span>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="p-4">
            <h3 class="text-lg font-bold text-gray-800 mb-1 line-clamp-1"><?= e($producto['nombre']) ?></h3>
            <p class="text-gray-600 text-xs mb-2 line-clamp-2"><?= e($producto['descripcion']) ?></p>
            
            <div class="flex items-center justify-between mb-3">
              <span class="text-xs text-gray-500">
                <?= $producto['genero'] === 'Unisex' ? ' Unisex' : ($producto['genero'] === 'Dama' ? ' Dama' : ' Caballero') ?>
              </span>
              <span class="text-xl font-bold text-primary">$<?= number_format($producto['precio'], 2) ?></span>
            </div>
            
            <div class="space-y-2">
              <?php if ($producto['stock'] > 0): ?>
                <button onclick="agregarAlCarrito(<?= $producto['id'] ?>, '<?= htmlspecialchars($producto['nombre'], ENT_QUOTES) ?>', <?= $producto['precio'] ?>)" 
                    class="w-full bg-primary text-white font-bold py-2 px-3 rounded-lg hover:bg-primary-dark shadow-md hover:shadow-lg transition-all duration-300 text-sm">
                   Agregar al Carrito
                </button>
                <button onclick="compraRapida(<?= $producto['id'] ?>)" 
                    class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold py-1.5 px-3 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 text-xs">
                  ⚡ Comprar Ahora
                </button>
              <?php else: ?>
                <button disabled class="w-full bg-gray-300 text-gray-500 font-bold py-2 px-3 rounded-lg cursor-not-allowed text-sm">Agotado Temporalmente</button>
              <?php endif; ?>
              
              <button onclick="verDetalles(<?= $producto['id'] ?>)" 
                  class="w-full bg-earth-light text-gray-700 font-semibold py-1.5 px-3 rounded-lg hover:bg-secondary border border-earth-medium transition-colors duration-300 text-xs">
                👁️ Ver Detalles
              </button>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
    
  </div>
</section>

<div class="max-w-5xl mx-auto mb-10">
  <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-300 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
    <div class="flex flex-col md:flex-row items-center gap-6">
      <div class="flex-shrink-0">
        <div class="bg-amber-100 rounded-full p-6 border-4 border-amber-300">
          <svg class="w-16 h-16 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
          </svg>
        </div>
      </div>
      <div class="flex-1 text-center md:text-left">
        <h2 class="text-3xl font-bold text-gray-900 mb-2"> ¿Quieres algo único?</h2>
        <p class="text-lg text-gray-700 mb-4">
          Creamos piezas <strong>100% personalizadas</strong> según tus preferencias. 
          Elige colores, piedras, tamaño y diseño. ¡Tu imaginación es el límite!
        </p>
        <ul class="text-sm text-gray-600 mb-4 space-y-1">
          <li> Colores y materiales a tu elección</li>
          <li> Piedras naturales especiales</li>
          <li> Medidas personalizadas</li>
          <li> Perfecto para regalos únicos</li>
        </ul>
      </div>
      <div class="flex-shrink-0">
        <a href="index.php?page=contacto" 
          class="inline-block bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold text-lg px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
          Cotizar Pedido Especial
        </a>
      </div>
    </div>
  </div>
</div>

<div id="product-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
  <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto relative" id="modal-content"></div>
</div>

<script>
const productosData = <?= json_encode($productos) ?>;

function verDetalles(productoId) {
  const producto = productosData.find(p => p.id == productoId);
  if (!producto) return;
  
  const modalContent = document.getElementById('modal-content');
  modalContent.innerHTML = `
    <button onclick="cerrarModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">✕</button>
    <div class="p-8">
      <img src="${producto.imagen_url}" alt="${producto.nombre}" class="w-full h-80 object-cover rounded-lg mb-6">
      <h2 class="text-3xl font-bold mb-4">${producto.nombre}</h2>
      <p class="text-gray-600 mb-4">${producto.descripcion}</p>
      <div class="grid grid-cols-2 gap-4 mb-6">
        <div><p class="text-sm text-gray-500">Categoría</p><p class="font-semibold">${producto.categoria}</p></div>
        <div><p class="text-sm text-gray-500">Género</p><p class="font-semibold">${producto.genero}</p></div>
        <div><p class="text-sm text-gray-500">Precio</p><p class="text-2xl font-bold text-primary">$${parseFloat(producto.precio).toFixed(2)} MXN</p></div>
        <div><p class="text-sm text-gray-500">Disponibles</p><p class="font-semibold">${producto.stock} unidades</p></div>
      </div>
      ${producto.stock > 0 ? `
        <button onclick="agregarAlCarrito(${producto.id}, '${producto.nombre}', ${producto.precio}); cerrarModal();" 
            class="w-full bg-primary text-white font-semibold py-3 px-6 rounded-lg hover:bg-primary-dark shadow-md hover:shadow-lg transition-all duration-300">
           Agregar al Carrito
        </button>
      ` : `<button disabled class="w-full bg-gray-300 text-gray-500 font-semibold py-3 px-6 rounded-lg cursor-not-allowed">Producto Agotado</button>`}
    </div>
  `;
  
  document.getElementById('product-modal').classList.remove('hidden');
  document.getElementById('product-modal').classList.add('flex');
}

function cerrarModal() {
  document.getElementById('product-modal').classList.add('hidden');
  document.getElementById('product-modal').classList.remove('flex');
}

document.getElementById('product-modal').addEventListener('click', function(e) {
  if (e.target === this) cerrarModal();
});

let generoActivo = 'todos';
let categoriaActiva = 'todos';

document.querySelectorAll('.gender-filter-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.gender-filter-btn').forEach(b => {
      b.classList.remove('bg-primary', 'text-white');
      b.classList.add('bg-earth-light', 'text-gray-700');
    });
    this.classList.remove('bg-earth-light', 'text-gray-700');
    this.classList.add('bg-primary', 'text-white');
    generoActivo = this.dataset.gender;
    filtrarProductos();
  });
});

document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.filter-btn').forEach(b => {
      b.classList.remove('bg-primary', 'text-white');
      b.classList.add('bg-earth-light', 'text-gray-700');
    });
    this.classList.remove('bg-earth-light', 'text-gray-700');
    this.classList.add('bg-primary', 'text-white');
    categoriaActiva = this.dataset.filter;
    filtrarProductos();
  });
});

document.getElementById('search-input').addEventListener('input', function() {
  filtrarProductos();
});

function filtrarProductos() {
  const busqueda = document.getElementById('search-input').value.toLowerCase();
  const productos = document.querySelectorAll('.product-card');
  let visibles = 0;
  
  productos.forEach(producto => {
    const categoria = producto.dataset.category;
    const genero = producto.dataset.gender;
    const nombre = producto.dataset.name;
    
    const cumpleGenero = generoActivo === 'todos' || genero === generoActivo;
    const cumpleCategoria = categoriaActiva === 'todos' || categoria === categoriaActiva;
    const cumpleBusqueda = busqueda === '' || nombre.includes(busqueda);
    
    if (cumpleGenero && cumpleCategoria && cumpleBusqueda) {
      producto.style.display = 'block';
      visibles++;
    } else {
      producto.style.display = 'none';
    }
  });
  
  document.getElementById('product-count').textContent = visibles;
}
</script>
