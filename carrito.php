<?php
if (!isset($_SESSION['carrito'])) {
  $_SESSION['carrito'] = [];
}

$carrito = $_SESSION['carrito'];
$total = 0;

foreach ($carrito as $item) {
  $total += $item['precio'] * $item['cantidad'];
}
?>

<div class="container mx-auto px-6 py-12">
  
  <h1 class="text-4xl font-bold text-center mb-8">Mi Carrito de Compras</h1>
  
  <?php if (isset($_SESSION['mensaje_exito'])): ?>
  <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md mb-6 animate-fadeIn">
    <p class="text-green-700"><?= e($_SESSION['mensaje_exito'] ?? '') ?></p>
  </div>
  <?php unset($_SESSION['mensaje_exito']); endif; ?>
  
  <?php if (isset($_SESSION['mensaje_error'])): ?>
  <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md mb-6 animate-fadeIn">
    <p class="text-red-700"><?= e($_SESSION['mensaje_error'] ?? '') ?></p>
  </div>
  <?php unset($_SESSION['mensaje_error']); endif; ?>
  
  <?php if (empty($carrito)): ?>
    <div class="text-center py-16">
      <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
      </svg>
      <h2 class="text-2xl font-semibold text-gray-700 mb-2">Tu carrito está vacío</h2>
      <p class="text-gray-500 mb-6">¡Explora nuestras creaciones y encuentra algo que te guste!</p>
      <a href="index.php?page=creaciones_dinamico" class="inline-block bg-primary text-white font-semibold py-3 px-8 rounded-lg hover:bg-primary-dark shadow-md hover:shadow-lg transition-all duration-300">
        Ver Productos
      </a>
    </div>
  
  <?php else: ?>
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Producto</th>
              <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Precio</th>
              <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Cantidad</th>
              <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Subtotal</th>
              <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php foreach ($carrito as $item): ?>
            <tr class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <?php $imagen = $item['imagen'] ?? $item['imagen_url'] ?? 'img/logo.png'; ?>
                  <img src="<?= e($imagen) ?>" alt="<?= e($item['nombre'] ?? 'Producto') ?>" 
                     class="h-16 w-16 object-cover rounded-lg mr-4 bg-gray-100" onerror="this.src='img/logo.png'">
                  <span class="font-medium text-gray-800"><?= e($item['nombre'] ?? 'Producto') ?></span>
                </div>
              </td>
              <td class="px-6 py-4 text-center text-gray-700">$<?= number_format($item['precio'], 2) ?></td>
              <td class="px-6 py-4">
                <form method="POST" class="flex items-center justify-center gap-2">
                  <input type="hidden" name="accion_carrito" value="actualizar">
                  <input type="hidden" name="producto_id" value="<?= $item['id'] ?>">
                  <input type="number" name="cantidad" value="<?= $item['cantidad'] ?>" min="1" max="<?= $item['stock_disponible'] ?? 99 ?>"
                      class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-center focus:outline-none focus:ring-2 focus:ring-primary">
                  <button type="submit" class="bg-earth-light text-gray-700 px-3 py-2 rounded-lg hover:bg-secondary transition-colors text-sm">Actualizar</button>
                </form>
              </td>
              <td class="px-6 py-4 text-center font-bold text-primary">$<?= number_format($item['precio'] * $item['cantidad'], 2) ?></td>
              <td class="px-6 py-4 text-center">
                <form method="POST" class="inline">
                  <input type="hidden" name="accion_carrito" value="eliminar">
                  <input type="hidden" name="producto_id" value="<?= $item['id'] ?>">
                  <button type="submit" onclick="return confirm('¿Eliminar este producto del carrito?')"
                      class="text-red-600 hover:text-red-800 font-medium transition-colors"> Eliminar</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="md:col-span-2 space-y-4">
        <a href="index.php?page=creaciones_dinamico" class="block w-full text-center bg-earth-light text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-secondary transition-colors">
          ← Seguir Comprando
        </a>
        <form method="POST" class="w-full">
          <input type="hidden" name="accion_carrito" value="vaciar">
          <button type="submit" onclick="return confirm('¿Vaciar todo el carrito?')"
              class="w-full bg-red-100 text-red-700 font-semibold py-3 px-6 rounded-lg hover:bg-red-200 transition-colors">
             Vaciar Carrito
          </button>
        </form>
      </div>
      
      <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold mb-4">Resumen del Pedido</h3>
        <div class="space-y-2 mb-4">
          <div class="flex justify-between text-gray-600"><span>Subtotal:</span><span>$<?= number_format($total, 2) ?></span></div>
          <div class="flex justify-between text-gray-600"><span>Envío:</span><span>A calcular</span></div>
          <hr class="my-2">
          <div class="flex justify-between text-xl font-bold text-primary"><span>Total:</span><span>$<?= number_format($total, 2) ?> MXN</span></div>
        </div>
        <a href="index.php?page=checkout" class="block w-full text-center bg-primary text-white font-semibold py-3 px-6 rounded-lg hover:bg-primary-dark shadow-md hover:shadow-lg transition-all duration-300">
          Proceder al Pago
        </a>
        <p class="text-xs text-gray-500 text-center mt-4">🔒 Compra segura y protegida</p>
      </div>
    </div>
  <?php endif; ?>
  
</div>
