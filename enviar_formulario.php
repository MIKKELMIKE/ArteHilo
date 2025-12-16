<?php
session_start();

function sanitize($data) {
  if (!is_string($data)) return $data;
  return htmlspecialchars(trim(stripslashes($data)), ENT_QUOTES, 'UTF-8');
}

$tipo_formulario = $_POST['tipo_formulario'] ?? 'contacto';
$nombre = sanitize($_POST['nombre'] ?? '');
$correo = sanitize($_POST['correo'] ?? '');
$telefono = sanitize($_POST['telefono'] ?? '');
$comentarios = sanitize($_POST['comentarios'] ?? '');

// Campos específicos de contacto/pedidos especiales
$producto = sanitize($_POST['producto'] ?? '');
$cantidad = intval($_POST['cantidad'] ?? 1);
$color = sanitize($_POST['color'] ?? '');
$tipo_pedido = sanitize($_POST['tipo_pedido'] ?? '');

// Campos específicos de ayuda
$asunto = sanitize($_POST['asunto'] ?? '');
$pedido_relacionado = sanitize($_POST['pedido_relacionado'] ?? '');

if (empty($nombre) || empty($correo) || empty($comentarios)) {
  $_SESSION['mensaje_error'] = 'Por favor completa todos los campos requeridos';
  if ($tipo_formulario === 'ayuda') {
    header('Location: index.php?page=ayuda');
  } else {
    header('Location: index.php?page=contacto');
  }
  exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
  $_SESSION['mensaje_error'] = 'El correo electrónico no es válido';
  if ($tipo_formulario === 'ayuda') {
    header('Location: index.php?page=ayuda');
  } else {
    header('Location: index.php?page=contacto');
  }
  exit;
}

// Aquí se podría enviar un correo o guardar en base de datos
// mail($to, $subject, $message, $headers);

if ($tipo_formulario === 'ayuda') {
  header('Location: index.php?page=ayuda&ayuda_enviada=1');
} else {
  $_SESSION['mensaje_exito'] = 'Gracias por tu mensaje, ' . $nombre . '. Te contactaremos pronto.';
  header('Location: index.php?page=contacto');
}
exit;
