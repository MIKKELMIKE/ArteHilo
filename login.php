<?php
if (isLoggedIn()) {
  header('Location: index.php');
  exit;
}

$error = $_SESSION['login_error'] ?? '';
$success = $_SESSION['registro_success'] ?? '';
unset($_SESSION['login_error']);
unset($_SESSION['registro_success']);

if (isset($_SESSION['mensaje_error'])) {
  $error = $_SESSION['mensaje_error'];
  unset($_SESSION['mensaje_error']);
}
if (isset($_SESSION['mensaje_exito'])) {
  $success = $_SESSION['mensaje_exito'];
  unset($_SESSION['mensaje_exito']);
}

if (isset($_SESSION['registro_exitoso'])) {
  $nombre_usuario = $_SESSION['registro_exitoso'];
  $success = "¡Bienvenido/a, $nombre_usuario! Tu cuenta ha sido creada exitosamente.";
  unset($_SESSION['registro_exitoso']);
}
?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary/10 to-secondary/10 py-12 px-4 sm:px-6 lg:px-8">
  <div class="max-w-md w-full space-y-8">
    
    <div class="text-center">
      <img src="img/logo.png" alt="Arte Hilo Logo" class="mx-auto h-24 w-auto">
      <h2 class="mt-6 text-3xl font-bold text-gray-900">Iniciar Sesión</h2>
      <p class="mt-2 text-sm text-gray-600">O <a href="index.php?page=registro" class="font-medium text-primary hover:text-primary-dark">crea una cuenta nueva</a></p>
    </div>
    
    <?php if ($error): ?>
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md animate-fadeIn">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-red-700"><?= htmlspecialchars($error) ?></p>
        </div>
      </div>
    </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md animate-fadeIn shadow-lg">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-6 w-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-green-700"><?= htmlspecialchars($success) ?></p>
        </div>
      </div>
    </div>
    <?php endif; ?>
    
    <form class="mt-8 space-y-6 bg-white p-8 rounded-xl shadow-xl" method="POST" action="">
      <div class="space-y-4">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
          <input id="email" name="email" type="email" autocomplete="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-300" placeholder="tu@email.com">
        </div>
        
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
          <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-300" placeholder="••••••••">
        </div>
      </div>
      
      <div>
        <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition duration-300 shadow-md hover:shadow-lg">
          Iniciar Sesión
        </button>
      </div>
      
      <div class="text-center mt-4">
        <p class="text-sm text-gray-600">
          ¿No tienes cuenta? 
          <a href="index.php?page=registro" class="font-medium text-primary hover:text-primary-dark transition">Regístrate aquí</a>
        </p>
      </div>
    </form>
    
  </div>
</div>
