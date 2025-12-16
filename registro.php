<?php
if (isLoggedIn()) {
  header('Location: index.php');
  exit;
}

$error = $_SESSION['registro_error'] ?? '';
unset($_SESSION['registro_error']);
?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-secondary/10 to-accent/10 py-12 px-4 sm:px-6 lg:px-8">
  <div class="max-w-md w-full space-y-8">
    
    <div class="text-center">
      <img src="img/logo.png" alt="Arte Hilo Logo" class="mx-auto h-24 w-auto">
      <h2 class="mt-6 text-3xl font-bold text-gray-900">Crear Cuenta</h2>
      <p class="mt-2 text-sm text-gray-600">O <a href="index.php?page=login" class="font-medium text-primary hover:text-primary-dark">inicia sesión si ya tienes cuenta</a></p>
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
          <p class="text-sm text-red-700"><?= $error ?></p>
        </div>
      </div>
    </div>
    <?php endif; ?>
    
    <form action="index.php?page=registro" method="POST" class="mt-8 space-y-6 bg-white shadow-2xl rounded-2xl p-8" novalidate>
      <div class="space-y-4">
        <div>
          <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo</label>
          <input id="nombre" name="nombre" type="text" autocomplete="name" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-300" placeholder="Tu nombre completo">
        </div>
        
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
          <input id="email" name="email" type="email" autocomplete="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-300" placeholder="tu@email.com">
        </div>
        
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
          <input id="password" name="password" type="password" autocomplete="new-password" required class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-300" placeholder="Mínimo 6 caracteres">
        </div>
        
        <div>
          <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Contraseña</label>
          <input id="password_confirm" name="password_confirm" type="password" autocomplete="new-password" required class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition duration-300" placeholder="Repite tu contraseña">
        </div>
      </div>
      
      <div class="flex items-start">
        <input id="terminos" name="terminos" type="checkbox" required class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded mt-1">
        <label for="terminos" class="ml-2 block text-sm text-gray-900">
          Acepto los <a href="#" class="text-primary hover:text-primary-dark underline">términos y condiciones</a>
        </label>
      </div>
      
      <div>
        <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition duration-300 shadow-md hover:shadow-lg">
          Crear Cuenta
        </button>
      </div>
      
      <div class="text-center mt-4">
        <p class="text-sm text-gray-600">
          ¿Ya tienes cuenta? 
          <a href="index.php?page=login" class="font-medium text-primary hover:text-primary-dark transition">Inicia sesión aquí</a>
        </p>
      </div>
    </form>
    
  </div>
</div>
