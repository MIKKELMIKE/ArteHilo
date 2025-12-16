<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'inicio';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'login') {
  $email = sanitize($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  
  if (!empty($email) && !empty($password)) {
    try {
      $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1 LIMIT 1");
      $stmt->execute([$email]);
      $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if ($usuario && verifyPassword($password, $usuario['password_hash'])) {
        loginUser($usuario);
        header('Location: ' . getFullRedirectURL());
        exit;
      } else {
        $_SESSION['login_error'] = 'Email o contraseña incorrectos';
      }
    } catch (PDOException $e) {
      $_SESSION['login_error'] = 'Error al procesar login';
    }
  } else {
    $_SESSION['login_error'] = 'Por favor, completa todos los campos';
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'registro') {
  $nombre = sanitize($_POST['nombre'] ?? '');
  $email = sanitize($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $password_confirm = $_POST['password_confirm'] ?? '';
  $registro_error = '';
  
  if (empty($nombre) || empty($email) || empty($password)) {
    $registro_error = 'Por favor, completa todos los campos';
  } elseif (strlen($nombre) < 3) {
    $registro_error = 'El nombre debe tener al menos 3 caracteres';
  } elseif (!validateEmail($email)) {
    $registro_error = 'El formato del email no es válido';
  } elseif (strlen($password) < 6) {
    $registro_error = 'La contraseña debe tener al menos 6 caracteres';
  } elseif ($password !== $password_confirm) {
    $registro_error = 'Las contraseñas no coinciden';
  } else {
    try {
      $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
      $stmt->execute([$email]);
      
      if ($stmt->fetch()) {
        $registro_error = 'Este email ya está registrado';
      } else {
        $password_hash = hashPassword($password);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password_hash, rol, activo) VALUES (?, ?, ?, 'cliente', 1)");
        $stmt->execute([$nombre, $email, $password_hash]);
        $_SESSION['registro_success'] = 'Cuenta creada exitosamente. ¡Inicia sesión!';
        header('Location: index.php?page=login');
        exit;
      }
    } catch (PDOException $e) {
      $registro_error = 'Error al crear cuenta';
    }
  }
  if ($registro_error) {
    $_SESSION['registro_error'] = $registro_error;
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_id']) && !isset($_POST['accion_carrito'])) {
  $producto_id = intval($_POST['producto_id'] ?? 0);
  
  if ($producto_id > 0) {
    try {
      $stmt = $pdo->prepare("SELECT id, nombre, precio, imagen_url, stock FROM productos WHERE id = ? AND activo = 1 LIMIT 1");
      $stmt->execute([$producto_id]);
      $producto = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if ($producto && $producto['stock'] > 0) {
        if (!isset($_SESSION['carrito'])) {
          $_SESSION['carrito'] = [];
        }
        
        if (isset($_SESSION['carrito'][$producto_id])) {
          if ($_SESSION['carrito'][$producto_id]['cantidad'] < $producto['stock']) {
            $_SESSION['carrito'][$producto_id]['cantidad']++;
            $_SESSION['mensaje_exito'] = 'Cantidad actualizada en el carrito';
          } else {
            $_SESSION['mensaje_error'] = 'No hay más stock disponible';
          }
        } else {
          $_SESSION['carrito'][$producto_id] = [
            'id' => $producto['id'],
            'nombre' => $producto['nombre'],
            'precio' => floatval($producto['precio']),
            'imagen_url' => $producto['imagen_url'],
            'cantidad' => 1,
            'stock_disponible' => intval($producto['stock'])
          ];
          $_SESSION['mensaje_exito'] = 'Producto agregado al carrito';
        }
      } else {
        $_SESSION['mensaje_error'] = 'Producto no disponible o sin stock';
      }
    } catch (PDOException $e) {
      $_SESSION['mensaje_error'] = 'Error al agregar producto';
    }
  }
  header('Location: index.php?page=creaciones');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion_carrito'])) {
  if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
  }
  
  $accion = $_POST['accion_carrito'];
  
  if ($accion === 'actualizar') {
    $producto_id = intval($_POST['producto_id'] ?? 0);
    $cantidad = intval($_POST['cantidad'] ?? 1);
    
    if ($producto_id > 0 && $cantidad > 0) {
      foreach ($_SESSION['carrito'] as $key => &$item) {
        if ($item['id'] == $producto_id) {
          $item['cantidad'] = $cantidad;
          $_SESSION['mensaje_exito'] = 'Cantidad actualizada';
          break;
        }
      }
    }
    header('Location: index.php?page=carrito');
    exit;
  }
  
  if ($accion === 'eliminar') {
    $producto_id = intval($_POST['producto_id'] ?? 0);
    if ($producto_id > 0) {
      foreach ($_SESSION['carrito'] as $key => $item) {
        if ($item['id'] == $producto_id) {
          unset($_SESSION['carrito'][$key]);
          $_SESSION['carrito'] = array_values($_SESSION['carrito']);
          $_SESSION['mensaje_exito'] = 'Producto eliminado del carrito';
          break;
        }
      }
    }
    header('Location: index.php?page=carrito');
    exit;
  }
  
  if ($accion === 'vaciar') {
    $_SESSION['carrito'] = [];
    $_SESSION['mensaje_exito'] = 'Carrito vaciado';
    header('Location: index.php?page=carrito');
    exit;
  }
}

include 'header.php';
?>
<main class="container mx-auto px-6 py-12" role="main">
<?php
  $page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);
  $paginas_permitidas = ['inicio','creaciones','creaciones_dinamico','nosotros','contacto','login','registro','carrito','checkout','confirmacion_pedido','mis_pedidos','ayuda'];
  
  if (in_array($page, $paginas_permitidas)) {
    $archivo = $page . '.php';
    if (file_exists($archivo)) {
      include $archivo;
    } else {
      echo '<div class="text-center py-20"><h1 class="text-6xl font-bold text-gray-300 mb-4">404</h1><h2 class="text-3xl font-bold text-gray-700 mb-4">Página No Encontrada</h2><a href="index.php?page=inicio" class="bg-primary hover:bg-primary-dark text-white font-bold py-3 px-6 rounded-lg">Volver al Inicio</a></div>';
    }
  } else {
    echo '<div class="text-center py-20"><h2 class="text-3xl font-bold text-orange-600 mb-4">Acceso No Permitido</h2><a href="index.php?page=inicio" class="bg-primary hover:bg-primary-dark text-white font-bold py-3 px-6 rounded-lg">Volver al Inicio</a></div>';
  }
?>
</main>
<?php include 'footer.php'; ?>
