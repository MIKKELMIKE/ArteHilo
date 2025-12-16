<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Verificar que sea administrador
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../index.php?page=login');
    exit;
}

$seccion = $_GET['seccion'] ?? 'dashboard';
$accion = $_GET['accion'] ?? 'listar';
$id = $_GET['id'] ?? null;

// Procesar acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion_post = $_POST['accion'] ?? '';
    
    // USUARIOS
    if ($accion_post === 'crear_usuario') {
        $nombre = sanitize($_POST['nombre']);
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        $rol = $_POST['rol'];
        $telefono = sanitize($_POST['telefono'] ?? '');
        
        $password_hash = hashPassword($password);
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password_hash, rol, telefono, activo) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->execute([$nombre, $email, $password_hash, $rol, $telefono]);
        $_SESSION['admin_msg'] = 'Usuario creado exitosamente';
        header('Location: index.php?seccion=usuarios');
        exit;
    }
    
    if ($accion_post === 'editar_usuario') {
        $id = $_POST['id'];
        $nombre = sanitize($_POST['nombre']);
        $email = sanitize($_POST['email']);
        $rol = $_POST['rol'];
        $telefono = sanitize($_POST['telefono'] ?? '');
        $activo = isset($_POST['activo']) ? 1 : 0;
        
        if (!empty($_POST['password'])) {
            $password_hash = hashPassword($_POST['password']);
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, email=?, password_hash=?, rol=?, telefono=?, activo=? WHERE id=?");
            $stmt->execute([$nombre, $email, $password_hash, $rol, $telefono, $activo, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, email=?, rol=?, telefono=?, activo=? WHERE id=?");
            $stmt->execute([$nombre, $email, $rol, $telefono, $activo, $id]);
        }
        $_SESSION['admin_msg'] = 'Usuario actualizado';
        header('Location: index.php?seccion=usuarios');
        exit;
    }
    
    if ($accion_post === 'eliminar_usuario') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ? AND id != ?");
        $stmt->execute([$id, $_SESSION['usuario_id']]);
        $_SESSION['admin_msg'] = 'Usuario eliminado';
        header('Location: index.php?seccion=usuarios');
        exit;
    }
    
    // PEDIDOS
    if ($accion_post === 'actualizar_pedido') {
        $id = $_POST['id'];
        $estado = $_POST['estado'];
        $codigo_rastreo = sanitize($_POST['codigo_rastreo'] ?? '');
        $notas = sanitize($_POST['notas'] ?? '');
        
        $stmt = $pdo->prepare("UPDATE pedidos SET estado=?, codigo_rastreo=?, notas=? WHERE id=?");
        $stmt->execute([$estado, $codigo_rastreo, $notas, $id]);
        
        if ($estado === 'enviado' && !empty($codigo_rastreo)) {
            $stmt = $pdo->prepare("UPDATE pedidos SET fecha_envio = NOW() WHERE id = ?");
            $stmt->execute([$id]);
        }
        if ($estado === 'completado') {
            $stmt = $pdo->prepare("UPDATE pedidos SET fecha_entrega = NOW() WHERE id = ?");
            $stmt->execute([$id]);
        }
        
        $_SESSION['admin_msg'] = 'Pedido actualizado';
        header('Location: index.php?seccion=pedidos');
        exit;
    }
    
    if ($accion_post === 'eliminar_pedido') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM pedidos WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['admin_msg'] = 'Pedido eliminado';
        header('Location: index.php?seccion=pedidos');
        exit;
    }
    
    // PRODUCTOS
    if ($accion_post === 'crear_producto') {
        $nombre = sanitize($_POST['nombre']);
        $descripcion = sanitize($_POST['descripcion']);
        $precio = floatval($_POST['precio']);
        $categoria = $_POST['categoria'];
        $genero = $_POST['genero'];
        $stock = intval($_POST['stock']);
        $destacado = isset($_POST['destacado']) ? 1 : 0;
        $imagen_url = sanitize($_POST['imagen_url'] ?? 'img/placeholder.jpg');
        
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, imagen_url, categoria, genero, stock, destacado, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([$nombre, $descripcion, $precio, $imagen_url, $categoria, $genero, $stock, $destacado]);
        $_SESSION['admin_msg'] = 'Producto creado';
        header('Location: index.php?seccion=productos');
        exit;
    }
    
    if ($accion_post === 'editar_producto') {
        $id = $_POST['id'];
        $nombre = sanitize($_POST['nombre']);
        $descripcion = sanitize($_POST['descripcion']);
        $precio = floatval($_POST['precio']);
        $categoria = $_POST['categoria'];
        $genero = $_POST['genero'];
        $stock = intval($_POST['stock']);
        $destacado = isset($_POST['destacado']) ? 1 : 0;
        $activo = isset($_POST['activo']) ? 1 : 0;
        $imagen_url = sanitize($_POST['imagen_url']);
        
        $stmt = $pdo->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, imagen_url=?, categoria=?, genero=?, stock=?, destacado=?, activo=? WHERE id=?");
        $stmt->execute([$nombre, $descripcion, $precio, $imagen_url, $categoria, $genero, $stock, $destacado, $activo, $id]);
        $_SESSION['admin_msg'] = 'Producto actualizado';
        header('Location: index.php?seccion=productos');
        exit;
    }
    
    if ($accion_post === 'eliminar_producto') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE productos SET activo = 0 WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['admin_msg'] = 'Producto desactivado';
        header('Location: index.php?seccion=productos');
        exit;
    }
}

// Obtener mensaje
$mensaje = $_SESSION['admin_msg'] ?? '';
unset($_SESSION['admin_msg']);

// Estadísticas para dashboard
$stats = [];
if ($seccion === 'dashboard') {
    $stats['usuarios'] = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    $stats['productos'] = $pdo->query("SELECT COUNT(*) FROM productos WHERE activo=1")->fetchColumn();
    $stats['pedidos'] = $pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();
    $stats['pedidos_pendientes'] = $pdo->query("SELECT COUNT(*) FROM pedidos WHERE estado='pendiente'")->fetchColumn();
    $stats['ventas_total'] = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM pedidos WHERE estado='completado'")->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Arte Hilo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { light: '#d4956f', DEFAULT: '#c17654', dark: '#a55a3a' },
                        secondary: '#d4a574',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white">
            <div class="p-4 border-b border-gray-700">
                <h1 class="text-xl font-bold">Arte Hilo</h1>
                <p class="text-sm text-gray-400">Panel de Admin</p>
            </div>
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="index.php?seccion=dashboard" class="block px-4 py-2 rounded <?= $seccion === 'dashboard' ? 'bg-primary' : 'hover:bg-gray-700' ?>">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="index.php?seccion=usuarios" class="block px-4 py-2 rounded <?= $seccion === 'usuarios' ? 'bg-primary' : 'hover:bg-gray-700' ?>">
                            Usuarios
                        </a>
                    </li>
                    <li>
                        <a href="index.php?seccion=productos" class="block px-4 py-2 rounded <?= $seccion === 'productos' ? 'bg-primary' : 'hover:bg-gray-700' ?>">
                            Productos
                        </a>
                    </li>
                    <li>
                        <a href="index.php?seccion=pedidos" class="block px-4 py-2 rounded <?= $seccion === 'pedidos' ? 'bg-primary' : 'hover:bg-gray-700' ?>">
                            Pedidos
                        </a>
                    </li>
                </ul>
                <div class="mt-8 pt-4 border-t border-gray-700">
                    <a href="../index.php" class="block px-4 py-2 text-gray-400 hover:text-white">
                        Volver al sitio
                    </a>
                    <a href="../index.php?page=logout" class="block px-4 py-2 text-red-400 hover:text-red-300">
                        Cerrar Sesión
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <?php if ($mensaje): ?>
            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                <?= e($mensaje) ?>
            </div>
            <?php endif; ?>

            <?php if ($seccion === 'dashboard'): ?>
            <!-- DASHBOARD -->
            <h2 class="text-2xl font-bold mb-6">Dashboard</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-gray-500 text-sm">Usuarios</h3>
                    <p class="text-3xl font-bold text-gray-800"><?= $stats['usuarios'] ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-gray-500 text-sm">Productos</h3>
                    <p class="text-3xl font-bold text-gray-800"><?= $stats['productos'] ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-gray-500 text-sm">Pedidos Totales</h3>
                    <p class="text-3xl font-bold text-gray-800"><?= $stats['pedidos'] ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-gray-500 text-sm">Pendientes</h3>
                    <p class="text-3xl font-bold text-orange-500"><?= $stats['pedidos_pendientes'] ?></p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-gray-500 text-sm">Ventas Totales (Completados)</h3>
                <p class="text-4xl font-bold text-green-600">$<?= number_format($stats['ventas_total'], 2) ?> MXN</p>
            </div>

            <?php elseif ($seccion === 'usuarios'): ?>
            <!-- USUARIOS -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Gestión de Usuarios</h2>
                <a href="index.php?seccion=usuarios&accion=crear" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">
                    + Nuevo Usuario
                </a>
            </div>

            <?php if ($accion === 'crear' || $accion === 'editar'): ?>
            <?php 
            $usuario = null;
            if ($accion === 'editar' && $id) {
                $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
                $stmt->execute([$id]);
                $usuario = $stmt->fetch();
            }
            ?>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-bold mb-4"><?= $accion === 'crear' ? 'Nuevo Usuario' : 'Editar Usuario' ?></h3>
                <form method="POST">
                    <input type="hidden" name="accion" value="<?= $accion === 'crear' ? 'crear_usuario' : 'editar_usuario' ?>">
                    <?php if ($usuario): ?><input type="hidden" name="id" value="<?= $usuario['id'] ?>"><?php endif; ?>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Nombre</label>
                            <input type="text" name="nombre" required value="<?= e($usuario['nombre'] ?? '') ?>" class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Email</label>
                            <input type="email" name="email" required value="<?= e($usuario['email'] ?? '') ?>" class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Contraseña <?= $usuario ? '(dejar vacío para no cambiar)' : '' ?></label>
                            <input type="password" name="password" <?= !$usuario ? 'required' : '' ?> class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Teléfono</label>
                            <input type="text" name="telefono" value="<?= e($usuario['telefono'] ?? '') ?>" class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Rol</label>
                            <select name="rol" class="w-full border rounded px-3 py-2">
                                <option value="cliente" <?= ($usuario['rol'] ?? '') === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                                <option value="admin" <?= ($usuario['rol'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                            </select>
                        </div>
                        <?php if ($usuario): ?>
                        <div class="flex items-center">
                            <input type="checkbox" name="activo" id="activo" <?= $usuario['activo'] ? 'checked' : '' ?> class="mr-2">
                            <label for="activo">Usuario Activo</label>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-6 flex gap-4">
                        <button type="submit" class="bg-primary text-white px-6 py-2 rounded hover:bg-primary-dark">Guardar</button>
                        <a href="index.php?seccion=usuarios" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">Cancelar</a>
                    </div>
                </form>
            </div>

            <?php else: ?>
            <?php $usuarios = $pdo->query("SELECT * FROM usuarios ORDER BY id DESC")->fetchAll(); ?>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">ID</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Nombre</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Email</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Rol</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Estado</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($usuarios as $u): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3"><?= $u['id'] ?></td>
                            <td class="px-4 py-3 font-medium"><?= e($u['nombre']) ?></td>
                            <td class="px-4 py-3"><?= e($u['email']) ?></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs <?= $u['rol'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' ?>">
                                    <?= $u['rol'] ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs <?= $u['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $u['activo'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="index.php?seccion=usuarios&accion=editar&id=<?= $u['id'] ?>" class="text-blue-600 hover:underline mr-2">Editar</a>
                                <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
                                <form method="POST" class="inline" onsubmit="return confirm('¿Eliminar usuario?')">
                                    <input type="hidden" name="accion" value="eliminar_usuario">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <?php elseif ($seccion === 'productos'): ?>
            <!-- PRODUCTOS -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Gestión de Productos</h2>
                <a href="index.php?seccion=productos&accion=crear" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">
                    + Nuevo Producto
                </a>
            </div>

            <?php if ($accion === 'crear' || $accion === 'editar'): ?>
            <?php 
            $producto = null;
            if ($accion === 'editar' && $id) {
                $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
                $stmt->execute([$id]);
                $producto = $stmt->fetch();
            }
            ?>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-bold mb-4"><?= $accion === 'crear' ? 'Nuevo Producto' : 'Editar Producto' ?></h3>
                <form method="POST">
                    <input type="hidden" name="accion" value="<?= $accion === 'crear' ? 'crear_producto' : 'editar_producto' ?>">
                    <?php if ($producto): ?><input type="hidden" name="id" value="<?= $producto['id'] ?>"><?php endif; ?>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium mb-1">Nombre</label>
                            <input type="text" name="nombre" required value="<?= e($producto['nombre'] ?? '') ?>" class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium mb-1">Descripción</label>
                            <textarea name="descripcion" rows="3" class="w-full border rounded px-3 py-2"><?= e($producto['descripcion'] ?? '') ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Precio (MXN)</label>
                            <input type="number" name="precio" step="0.01" required value="<?= $producto['precio'] ?? '' ?>" class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Stock</label>
                            <input type="number" name="stock" required value="<?= $producto['stock'] ?? 0 ?>" class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Categoría</label>
                            <select name="categoria" class="w-full border rounded px-3 py-2">
                                <?php foreach (['Pulsera', 'Collar', 'Llavero', 'Tobillera', 'Otro'] as $cat): ?>
                                <option value="<?= $cat ?>" <?= ($producto['categoria'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Género</label>
                            <select name="genero" class="w-full border rounded px-3 py-2">
                                <?php foreach (['Dama', 'Caballero', 'Unisex'] as $gen): ?>
                                <option value="<?= $gen ?>" <?= ($producto['genero'] ?? '') === $gen ? 'selected' : '' ?>><?= $gen ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">URL Imagen</label>
                            <input type="text" name="imagen_url" value="<?= e($producto['imagen_url'] ?? 'img/placeholder.jpg') ?>" class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="destacado" <?= ($producto['destacado'] ?? 0) ? 'checked' : '' ?> class="mr-2">
                                Destacado
                            </label>
                            <?php if ($producto): ?>
                            <label class="flex items-center">
                                <input type="checkbox" name="activo" <?= $producto['activo'] ? 'checked' : '' ?> class="mr-2">
                                Activo
                            </label>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex gap-4">
                        <button type="submit" class="bg-primary text-white px-6 py-2 rounded hover:bg-primary-dark">Guardar</button>
                        <a href="index.php?seccion=productos" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">Cancelar</a>
                    </div>
                </form>
            </div>

            <?php else: ?>
            <?php $productos = $pdo->query("SELECT * FROM productos ORDER BY id DESC")->fetchAll(); ?>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">ID</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Producto</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Precio</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Stock</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Categoría</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Estado</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($productos as $p): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3"><?= $p['id'] ?></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <img src="../<?= e($p['imagen_url']) ?>" alt="" class="w-10 h-10 object-cover rounded mr-3">
                                    <span class="font-medium"><?= e($p['nombre']) ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-3">$<?= number_format($p['precio'], 2) ?></td>
                            <td class="px-4 py-3"><?= $p['stock'] ?></td>
                            <td class="px-4 py-3"><?= $p['categoria'] ?></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs <?= $p['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $p['activo'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="index.php?seccion=productos&accion=editar&id=<?= $p['id'] ?>" class="text-blue-600 hover:underline mr-2">Editar</a>
                                <form method="POST" class="inline" onsubmit="return confirm('¿Desactivar producto?')">
                                    <input type="hidden" name="accion" value="eliminar_producto">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="text-red-600 hover:underline">Desactivar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <?php elseif ($seccion === 'pedidos'): ?>
            <!-- PEDIDOS -->
            <h2 class="text-2xl font-bold mb-6">Gestión de Pedidos</h2>

            <?php if ($accion === 'ver' && $id): ?>
            <?php 
            $stmt = $pdo->prepare("SELECT p.*, u.nombre as cliente_nombre, u.email as cliente_email 
                                   FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = ?");
            $stmt->execute([$id]);
            $pedido = $stmt->fetch();
            
            $stmt = $pdo->prepare("SELECT dp.*, pr.nombre as producto_nombre, pr.imagen_url 
                                   FROM detalle_pedidos dp JOIN productos pr ON dp.producto_id = pr.id 
                                   WHERE dp.pedido_id = ?");
            $stmt->execute([$id]);
            $detalles = $stmt->fetchAll();
            ?>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-xl font-bold">Pedido #<?= $pedido['id'] ?></h3>
                        <p class="text-gray-500">Fecha: <?= date('d/m/Y H:i', strtotime($pedido['fecha'])) ?></p>
                    </div>
                    <span class="px-3 py-1 rounded text-sm font-medium
                        <?php
                        switch($pedido['estado']) {
                            case 'pendiente': echo 'bg-yellow-100 text-yellow-800'; break;
                            case 'procesando': echo 'bg-blue-100 text-blue-800'; break;
                            case 'enviado': echo 'bg-purple-100 text-purple-800'; break;
                            case 'completado': echo 'bg-green-100 text-green-800'; break;
                            case 'cancelado': echo 'bg-red-100 text-red-800'; break;
                        }
                        ?>">
                        <?= ucfirst($pedido['estado']) ?>
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <h4 class="font-bold mb-2">Cliente</h4>
                        <p><?= e($pedido['cliente_nombre']) ?></p>
                        <p class="text-gray-500"><?= e($pedido['cliente_email']) ?></p>
                        <p class="text-gray-500"><?= e($pedido['telefono']) ?></p>
                    </div>
                    <div>
                        <h4 class="font-bold mb-2">Dirección de Envío</h4>
                        <p><?= e($pedido['direccion_envio']) ?></p>
                    </div>
                </div>
                
                <h4 class="font-bold mb-2">Productos</h4>
                <table class="w-full mb-6">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Producto</th>
                            <th class="px-4 py-2 text-center">Cantidad</th>
                            <th class="px-4 py-2 text-right">Precio</th>
                            <th class="px-4 py-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($detalles as $d): ?>
                        <tr>
                            <td class="px-4 py-2">
                                <div class="flex items-center">
                                    <img src="../<?= e($d['imagen_url']) ?>" class="w-10 h-10 object-cover rounded mr-2">
                                    <?= e($d['producto_nombre']) ?>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-center"><?= $d['cantidad'] ?></td>
                            <td class="px-4 py-2 text-right">$<?= number_format($d['precio_unitario'], 2) ?></td>
                            <td class="px-4 py-2 text-right">$<?= number_format($d['subtotal'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold">
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right">Total:</td>
                            <td class="px-4 py-2 text-right">$<?= number_format($pedido['total'], 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
                
                <form method="POST" class="border-t pt-6">
                    <input type="hidden" name="accion" value="actualizar_pedido">
                    <input type="hidden" name="id" value="<?= $pedido['id'] ?>">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Estado</label>
                            <select name="estado" class="w-full border rounded px-3 py-2">
                                <?php foreach (['pendiente', 'procesando', 'enviado', 'completado', 'cancelado'] as $est): ?>
                                <option value="<?= $est ?>" <?= $pedido['estado'] === $est ? 'selected' : '' ?>><?= ucfirst($est) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Código de Rastreo</label>
                            <input type="text" name="codigo_rastreo" value="<?= e($pedido['codigo_rastreo'] ?? '') ?>" class="w-full border rounded px-3 py-2" placeholder="Número de guía">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium mb-1">Notas</label>
                            <textarea name="notas" rows="2" class="w-full border rounded px-3 py-2"><?= e($pedido['notas'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex gap-4">
                        <button type="submit" class="bg-primary text-white px-6 py-2 rounded hover:bg-primary-dark">Actualizar Pedido</button>
                        <a href="index.php?seccion=pedidos" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">Volver</a>
                    </div>
                </form>
            </div>

            <?php else: ?>
            <?php $pedidos = $pdo->query("SELECT p.*, u.nombre as cliente FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.fecha DESC")->fetchAll(); ?>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">ID</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Cliente</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Total</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Estado</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Fecha</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($pedidos as $ped): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">#<?= $ped['id'] ?></td>
                            <td class="px-4 py-3"><?= e($ped['cliente']) ?></td>
                            <td class="px-4 py-3 font-medium">$<?= number_format($ped['total'], 2) ?></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded text-xs
                                    <?php
                                    switch($ped['estado']) {
                                        case 'pendiente': echo 'bg-yellow-100 text-yellow-800'; break;
                                        case 'procesando': echo 'bg-blue-100 text-blue-800'; break;
                                        case 'enviado': echo 'bg-purple-100 text-purple-800'; break;
                                        case 'completado': echo 'bg-green-100 text-green-800'; break;
                                        case 'cancelado': echo 'bg-red-100 text-red-800'; break;
                                    }
                                    ?>">
                                    <?= ucfirst($ped['estado']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500"><?= date('d/m/Y', strtotime($ped['fecha'])) ?></td>
                            <td class="px-4 py-3">
                                <a href="index.php?seccion=pedidos&accion=ver&id=<?= $ped['id'] ?>" class="text-blue-600 hover:underline mr-2">Ver/Editar</a>
                                <form method="POST" class="inline" onsubmit="return confirm('¿Eliminar pedido?')">
                                    <input type="hidden" name="accion" value="eliminar_pedido">
                                    <input type="hidden" name="id" value="<?= $ped['id'] ?>">
                                    <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <?php endif; ?>
        </main>
    </div>
</body>
</html>
