<?php
if (empty($_SESSION['carrito'])) {
  $_SESSION['mensaje_error'] = 'Tu carrito está vacío';
  header('Location: index.php?page=carrito');
  exit;
}

$carrito = $_SESSION['carrito'];
$total = 0;
foreach ($carrito as $item) {
  $total += $item['precio'] * $item['cantidad'];
}

// Obtener datos del último pedido si el usuario está logueado
$datosGuardados = null;
if (isLoggedIn()) {
  try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT telefono, direccion_envio FROM pedidos WHERE usuario_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([getUserId()]);
    $datosGuardados = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    $datosGuardados = null;
  }
}

// Valores para el formulario
$nombre = getUserName();
$email = getUserEmail();
$telefono = $datosGuardados['telefono'] ?? '';
$direccion = $datosGuardados['direccion_envio'] ?? '';

// Separar ciudad de dirección si existe
$ciudad = '';
if (!empty($direccion) && strpos($direccion, ',') !== false) {
  $partes = explode(',', $direccion);
  $ciudad = trim(array_pop($partes));
  $direccion = trim(implode(',', $partes));
}
?>

<div class="container mx-auto px-6 py-12">
    <?php if (!empty($_SESSION['mensaje_error'])): ?>
      <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <?= e($_SESSION['mensaje_error']); unset($_SESSION['mensaje_error']); ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['mensaje_exito'])): ?>
      <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        <?= e($_SESSION['mensaje_exito']); unset($_SESSION['mensaje_exito']); ?>
      </div>
    <?php endif; ?>
  <div class="max-w-4xl mx-auto">
    
    <h1 class="text-4xl font-bold text-center mb-8">Finalizar Compra</h1>
    
    <div class="flex items-center justify-center mb-8 text-sm text-gray-600">
      <span>Carrito</span>
      <svg class="h-4 w-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
      </svg>
      <span class="font-semibold text-primary">Checkout</span>
      <svg class="h-4 w-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
      </svg>
      <span>Confirmación</span>
    </div>
    
    <form method="POST" action="procesar_pedido.php" class="space-y-6">
      
      <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-4"> Información del Pedido</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo *</label>
            <input type="text" name="nombre" value="<?= e($nombre) ?>" required minlength="3"
                placeholder="Ej. Juan Pérez García"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
            <input type="email" name="email" value="<?= e($email) ?>" required
                placeholder="Ej. usuario@ejemplo.com"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono *</label>
            <input type="tel" name="telefono" value="<?= e($telefono) ?>" required pattern="[0-9]{10}" placeholder="10 dígitos"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Ciudad *</label>
            <input type="text" name="ciudad" value="<?= e($ciudad) ?>" required placeholder="Ej: Aguascalientes"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
          </div>
        </div>
        
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Dirección de Envío Completa *</label>
          <textarea name="direccion_envio" required rows="3" placeholder="Calle, número, colonia, código postal, estado"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"><?= e($direccion) ?></textarea>
        </div>
        
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Notas del Pedido (opcional)</label>
          <textarea name="notas" rows="2" placeholder="Instrucciones especiales de entrega, preferencias de colores, etc."
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
        </div>
      </div>
      
      <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-4"> Resumen del Pedido</h2>
        
        <div class="space-y-3">
          <?php foreach ($carrito as $item): ?>
            <div class="flex items-center justify-between py-2 border-b border-gray-200">
              <div class="flex items-center flex-1">
                <img src="<?= e($item['imagen'] ?? $item['imagen_url'] ?? 'img/logo.png') ?>" 
                   alt="<?= e($item['nombre'] ?? 'Producto') ?>" class="h-12 w-12 object-cover rounded-lg mr-3">
                <div>
                  <p class="font-medium text-gray-800"><?= e($item['nombre'] ?? 'Producto') ?></p>
                  <p class="text-sm text-gray-500">Cantidad: <?= $item['cantidad'] ?></p>
                </div>
              </div>
              <span class="font-semibold text-primary">$<?= number_format($item['precio'] * $item['cantidad'], 2) ?></span>
            </div>
          <?php endforeach; ?>
        </div>
        
        <div class="mt-4 pt-4 border-t-2 border-gray-300">
          <div class="flex justify-between text-lg mb-2"><span class="text-gray-600">Subtotal:</span><span class="font-semibold">$<?= number_format($total, 2) ?></span></div>
          <div class="flex justify-between text-lg mb-2"><span class="text-gray-600">Envío:</span><span class="text-sm text-gray-500">A confirmar</span></div>
          <div class="flex justify-between text-2xl font-bold text-primary mt-4"><span>Total:</span><span>$<?= number_format($total, 2) ?> MXN</span></div>
        </div>
      </div>
      
      <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-4"> Método de Pago</h2>
        <div class="space-y-3">
          <label class="flex items-center p-4 border-2 border-primary rounded-lg cursor-pointer">
            <input type="radio" name="metodo_pago" value="transferencia" checked class="h-4 w-4 text-primary focus:ring-primary">
            <span class="ml-3 font-medium">🏦 Transferencia Bancaria</span>
          </label>
          <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-primary transition-colors">
            <input type="radio" name="metodo_pago" value="efectivo" class="h-4 w-4 text-primary focus:ring-primary">
            <span class="ml-3 font-medium">💵 Pago contra entrega</span>
          </label>
          <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-primary transition-colors">
            <input type="radio" name="metodo_pago" value="oxxo" class="h-4 w-4 text-primary focus:ring-primary">
            <span class="ml-3 font-medium">🏪 Pago en OXXO</span>
          </label>
        </div>
      </div>
      
      <div class="bg-gray-50 rounded-lg p-4">
        <label class="flex items-start">
          <input type="checkbox" required class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded mt-1">
          <span class="ml-3 text-sm text-gray-700">
            Acepto los <a href="#" class="text-primary underline">términos y condiciones</a> y la 
            <a href="#" class="text-primary underline">política de privacidad</a>.
          </span>
        </label>
      </div>
      
      <div class="flex gap-4">
        <a href="index.php?page=carrito" class="flex-1 text-center bg-gray-300 text-gray-700 font-semibold py-4 px-6 rounded-lg hover:bg-gray-400 transition-colors">
          ← Volver al Carrito
        </a>
        <button type="submit" class="flex-1 bg-primary text-white font-semibold py-4 px-6 rounded-lg hover:bg-primary-dark shadow-lg hover:shadow-xl transition-all duration-300">
          Confirmar Pedido ✓
        </button>
      </div>
      
    </form>
  </div>
</div>
