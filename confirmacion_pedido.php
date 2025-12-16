<?php
if (!isset($_SESSION['ultimo_pedido_id'])) {
  header('Location: index.php');
  exit;
}

$pedido_id = $_SESSION['ultimo_pedido_id'];
$pedido = null;
$detalles = [];

try {
  $pdo = getDB();
  
  if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT p.*, u.nombre, u.email FROM pedidos p LEFT JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = ?");
    $stmt->execute([$pedido_id]);
  } else {
    $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id = ?");
    $stmt->execute([$pedido_id]);
  }
  $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
  
  if ($pedido) {
    $stmt = $pdo->prepare("SELECT dp.*, pr.nombre, pr.imagen_url FROM detalle_pedidos dp JOIN productos pr ON dp.producto_id = pr.id WHERE dp.pedido_id = ?");
    $stmt->execute([$pedido_id]);
    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
} catch (PDOException $e) {
  $pedido = null;
}

unset($_SESSION['ultimo_pedido_id']);

if (!$pedido) {
  header('Location: index.php');
  exit;
}
?>

<div class="container mx-auto px-6 py-12">
  <div class="max-w-3xl mx-auto">
    
    <div class="text-center mb-8 animate-fadeIn">
      <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
        <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
      </div>
      <h1 class="text-4xl font-bold text-gray-900 mb-2">¡Pedido Confirmado!</h1>
      <?php $primerNombre = !empty($pedido['nombre_cliente']) ? explode(' ', $pedido['nombre_cliente'])[0] : 'Cliente'; ?>
      <p class="text-xl text-gray-600">Gracias por tu compra, <?= e($primerNombre) ?></p>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
      <div class="grid grid-cols-2 gap-4 mb-6">
        <div>
          <p class="text-sm text-gray-500 mb-1">Número de Pedido</p>
          <p class="text-lg font-bold text-primary">#<?= str_pad($pedido['id'], 6, '0', STR_PAD_LEFT) ?></p>
        </div>
        <div>
          <p class="text-sm text-gray-500 mb-1">Fecha</p>
          <p class="text-lg font-semibold"><?= date('d/m/Y H:i', strtotime($pedido['fecha_pedido'] ?? $pedido['fecha'] ?? 'now')) ?></p>
        </div>
        <div>
          <p class="text-sm text-gray-500 mb-1">Estado</p>
          <span class="inline-block bg-yellow-100 text-yellow-800 text-sm px-3 py-1 rounded-full font-semibold"> <?= ucfirst($pedido['estado']) ?></span>
        </div>
        <div>
          <p class="text-sm text-gray-500 mb-1">Total</p>
          <p class="text-2xl font-bold text-primary">$<?= number_format($pedido['total'], 2) ?> MXN</p>
        </div>
      </div>
      
      <hr class="my-4">
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <p class="text-sm font-semibold text-gray-700 mb-2"> Email de Confirmación</p>
          <p class="text-gray-600"><?= e($pedido['email_cliente'] ?? $pedido['email'] ?? '') ?></p>
        </div>
        <div>
          <p class="text-sm font-semibold text-gray-700 mb-2">📞 Teléfono de Contacto</p>
          <p class="text-gray-600"><?= e($pedido['telefono']) ?></p>
        </div>
        <div class="md:col-span-2">
          <p class="text-sm font-semibold text-gray-700 mb-2"> Dirección de Envío</p>
          <p class="text-gray-600"><?= e($pedido['direccion_envio']) ?></p>
        </div>
        <?php if (!empty($pedido['notas'])): ?>
        <div class="md:col-span-2">
          <p class="text-sm font-semibold text-gray-700 mb-2"> Notas</p>
          <p class="text-gray-600"><?= e($pedido['notas']) ?></p>
        </div>
        <?php endif; ?>
      </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
      <h2 class="text-2xl font-bold mb-4"> Productos</h2>
      <div class="space-y-4">
        <?php foreach ($detalles as $detalle): ?>
        <div class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg">
          <img src="<?= e($detalle['imagen_url']) ?>" alt="<?= e($detalle['nombre']) ?>" class="h-20 w-20 object-cover rounded-lg">
          <div class="flex-1">
            <h3 class="font-semibold text-gray-800"><?= e($detalle['nombre']) ?></h3>
            <p class="text-sm text-gray-500">Cantidad: <?= $detalle['cantidad'] ?> × $<?= number_format($detalle['precio_unitario'], 2) ?></p>
          </div>
          <div class="text-right">
            <p class="text-lg font-bold text-primary">$<?= number_format($detalle['subtotal'], 2) ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-md mb-6">
      <p class="text-blue-700"><strong> ¿Qué sigue?</strong> Recibirás un correo de confirmación con los detalles de tu pedido y las instrucciones de pago.</p>
    </div>
    
    <div class="flex gap-4">
      <a href="index.php?page=inicio" class="flex-1 text-center bg-earth-light text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-secondary transition-colors">
        Volver al Inicio
      </a>
      <?php if (isLoggedIn()): ?>
      <a href="index.php?page=mis_pedidos" class="flex-1 text-center bg-primary text-white font-semibold py-3 px-6 rounded-lg hover:bg-primary-dark transition-colors">
        Ver Mis Pedidos
      </a>
      <?php endif; ?>
    </div>
    
  </div>
</div>
