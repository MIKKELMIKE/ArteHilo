<?php
requireLogin();

$pedidos = [];
try {
  $pdo = getDB();
  $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY fecha DESC, id DESC");
  $stmt->execute([getUserId()]);
  $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $pedidos = [];
}

function getDetallesPedido($pedido_id) {
  $pdo = getDB();
  try {
    $stmt = $pdo->prepare("SELECT dp.*, pr.nombre, pr.imagen_url FROM detalle_pedidos dp JOIN productos pr ON dp.producto_id = pr.id WHERE dp.pedido_id = ?");
    $stmt->execute([$pedido_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    return [];
  }
}

$estados_config = [
  'Pendiente' => ['color' => 'yellow', 'emoji' => ''],
  'Procesando' => ['color' => 'blue', 'emoji' => ''],
  'Enviado' => ['color' => 'purple', 'emoji' => ''],
  'Completado' => ['color' => 'green', 'emoji' => ''],
  'Cancelado' => ['color' => 'red', 'emoji' => ''],
];
?>

<div class="container mx-auto px-6 py-12">
  
  <h1 class="text-4xl font-bold text-center mb-8">Mis Pedidos</h1>
  
  <?php if (empty($pedidos)): ?>
    <div class="text-center py-16">
      <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
      </svg>
      <h2 class="text-2xl font-semibold text-gray-700 mb-2">Aún no has realizado pedidos</h2>
      <p class="text-gray-500 mb-6">¡Explora nuestro catálogo y haz tu primera compra!</p>
      <a href="index.php?page=creaciones_dinamico" class="inline-block bg-primary text-white font-semibold py-3 px-8 rounded-lg hover:bg-primary-dark shadow-md hover:shadow-lg transition-all duration-300">
        Ver Productos
      </a>
    </div>
  
  <?php else: ?>
    <div class="space-y-6">
      <?php foreach ($pedidos as $pedido): 
        $estado = $pedido['estado'] ?? 'Pendiente';
        $estado_config = $estados_config[$estado] ?? $estados_config['Pendiente'];
        $detalles = getDetallesPedido($pedido['id']);
      ?>
      
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-primary to-secondary p-6 text-white">
          <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
              <h2 class="text-2xl font-bold">Pedido #<?= str_pad($pedido['id'], 6, '0', STR_PAD_LEFT) ?></h2>
              <p class="text-sm opacity-90">📅 <?= date('d/m/Y H:i', strtotime($pedido['fecha_pedido'] ?? $pedido['fecha'] ?? 'now')) ?></p>
            </div>
            <div class="text-right">
              <span class="inline-block bg-<?= $estado_config['color'] ?>-100 text-<?= $estado_config['color'] ?>-800 text-sm px-4 py-2 rounded-full font-semibold">
                <?= $estado_config['emoji'] ?> <?= $estado ?>
              </span>
              <p class="text-2xl font-bold mt-2">$<?= number_format($pedido['total'], 2) ?> MXN</p>
            </div>
          </div>
        </div>
        
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
              <p class="text-sm font-semibold text-gray-700 mb-1"> Dirección de Envío</p>
              <p class="text-gray-600"><?= e($pedido['direccion_envio']) ?></p>
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-700 mb-1">📞 Teléfono</p>
              <p class="text-gray-600"><?= e($pedido['telefono']) ?></p>
            </div>
          </div>
          
          <?php if (!empty($pedido['notas'])): ?>
          <div class="mb-6">
            <p class="text-sm font-semibold text-gray-700 mb-1"> Notas</p>
            <p class="text-gray-600"><?= e($pedido['notas']) ?></p>
          </div>
          <?php endif; ?>
          
          <div class="border-t border-gray-200 pt-4">
            <p class="text-sm font-semibold text-gray-700 mb-3">Productos (<?= count($detalles) ?>)</p>
            <div class="space-y-3">
              <?php foreach ($detalles as $detalle): ?>
              <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                <img src="<?= e($detalle['imagen_url']) ?>" alt="<?= e($detalle['nombre']) ?>" class="h-16 w-16 object-cover rounded-lg">
                <div class="flex-1">
                  <h3 class="font-semibold text-gray-800"><?= e($detalle['nombre']) ?></h3>
                  <p class="text-sm text-gray-500">Cantidad: <?= $detalle['cantidad'] ?> × $<?= number_format($detalle['precio_unitario'], 2) ?></p>
                </div>
                <div class="text-right">
                  <p class="font-bold text-primary">$<?= number_format($detalle['subtotal'], 2) ?></p>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
      
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  
</div>
