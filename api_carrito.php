<?php
ini_set('display_errors', 0);
error_reporting(0);

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

try {
  
  $method = $_SERVER['REQUEST_METHOD'];
  $action = $_REQUEST['action'] ?? '';

  if ($method === 'POST') {
    switch ($action) {
      case 'agregar': agregarProducto($pdo); break;
      case 'eliminar': eliminarProducto(); break;
      case 'actualizar': actualizarCantidad(); break;
      case 'vaciar': vaciarCarrito(); break;
      default: echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    }
  } else {
    echo json_encode(['success' => true, 'total_items' => contarItemsCarrito()]);
  }
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Error de conexión']);
}

function agregarProducto($pdo) {
  $id = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
  $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT) ?: 1;
  
  if (!$id || $cantidad < 1) { echo json_encode(['success' => false, 'message' => 'Datos inválidos']); return; }

  foreach ($_SESSION['carrito'] as &$item) {
    if ($item['id'] == $id) {
      $item['cantidad'] += $cantidad;
      echo json_encode(['success' => true, 'message' => 'Cantidad actualizada', 'total_items' => contarItemsCarrito()]);
      return;
    }
  }
  
  $stmt = $pdo->prepare("SELECT id, nombre, precio, imagen_url FROM productos WHERE id = ? AND activo = 1");
  $stmt->execute([$id]);
  $producto = $stmt->fetch(PDO::FETCH_ASSOC);
  
  if ($producto) {
    $_SESSION['carrito'][] = ['id' => (int)$producto['id'], 'nombre' => $producto['nombre'], 'precio' => (float)$producto['precio'], 'imagen' => $producto['imagen_url'], 'cantidad' => $cantidad];
    echo json_encode(['success' => true, 'message' => 'Producto agregado', 'total_items' => contarItemsCarrito()]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
  }
}

function eliminarProducto() {
  $id = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
  foreach ($_SESSION['carrito'] as $key => $item) {
    if ($item['id'] == $id) { unset($_SESSION['carrito'][$key]); $_SESSION['carrito'] = array_values($_SESSION['carrito']); break; }
  }
  echo json_encode(['success' => true, 'message' => 'Eliminado', 'total_items' => contarItemsCarrito()]);
}

function actualizarCantidad() {
  $id = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
  $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);
  if ($cantidad < 1) { echo json_encode(['success' => false, 'message' => 'Cantidad inválida']); return; }
  foreach ($_SESSION['carrito'] as &$item) { if ($item['id'] == $id) { $item['cantidad'] = $cantidad; break; } }
  echo json_encode(['success' => true, 'message' => 'Actualizado', 'total_items' => contarItemsCarrito()]);
}

function vaciarCarrito() {
  $_SESSION['carrito'] = [];
  echo json_encode(['success' => true, 'message' => 'Carrito vaciado', 'total_items' => 0]);
}

function contarItemsCarrito() {
  $total = 0;
  foreach ($_SESSION['carrito'] ?? [] as $item) $total += $item['cantidad'];
  return $total;
}
