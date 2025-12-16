<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php?page=checkout'); exit; }
if (empty($_SESSION['carrito'])) { $_SESSION['mensaje_error'] = 'Tu carrito está vacío'; header('Location: index.php?page=carrito'); exit; }

$nombre = sanitize($_POST['nombre'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$telefono = sanitize($_POST['telefono'] ?? '');
$ciudad = sanitize($_POST['ciudad'] ?? '');
$direccion_envio = sanitize($_POST['direccion_envio'] ?? '');
$notas = sanitize($_POST['notas'] ?? '');

if (isLoggedIn()) {
  $nombre = !empty($nombre) ? $nombre : getUserName();
  $email = !empty($email) ? $email : getUserEmail();
}

if (empty($nombre) || empty($email) || empty($telefono) || empty($direccion_envio) || empty($ciudad)) {
  $_SESSION['mensaje_error'] = 'Completa todos los campos requeridos';
  header('Location: index.php?page=checkout'); exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $_SESSION['mensaje_error'] = 'Email no válido';
  header('Location: index.php?page=checkout'); exit;
}

$direccion_completa = $direccion_envio . ", " . $ciudad;

try {
  $pdo = getDB();
  $pdo->beginTransaction();
  
  $total = 0;
  foreach ($_SESSION['carrito'] as $item) $total += $item['precio'] * $item['cantidad'];
  
  $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, total, direccion_envio, telefono, notas, estado) VALUES (?, ?, ?, ?, ?, 'pendiente')");
  $stmt->execute([isLoggedIn() ? getUserId() : null, $total, $direccion_completa, $telefono, $notas]);
  $pedido_id = $pdo->lastInsertId();
  
  $stmt_detalle = $pdo->prepare("INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
  $stmt_stock = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
  
  foreach ($_SESSION['carrito'] as $item) {
    $subtotal = $item['precio'] * $item['cantidad'];
    $stmt_detalle->execute([$pedido_id, $item['id'], $item['cantidad'], $item['precio'], $subtotal]);
    $stmt_stock->execute([$item['cantidad'], $item['id']]);
  }
  
  $pdo->commit();
  $_SESSION['ultimo_pedido_id'] = $pedido_id;
  $_SESSION['carrito'] = [];
  $_SESSION['mensaje_exito'] = '¡Pedido realizado con éxito!';
  header('Location: index.php?page=confirmacion_pedido'); exit;
  
} catch (PDOException $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  $_SESSION['mensaje_error'] = 'Error al procesar tu pedido: ' . $e->getMessage();
  header('Location: index.php?page=checkout'); exit;
}
