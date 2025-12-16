<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$items_carrito = 0;
if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
  foreach ($_SESSION['carrito'] as $item) {
    $items_carrito += $item['cantidad'];
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  
  <title>Arte Hilo: Tejidos del Alma - Accesorios Artesanales Hechos a Mano</title>
  <meta name="description" content="Arte Hilo es un taller familiar de accesorios artesanales 100% hechos a mano. Pulseras, collares, llaveros y tobilleras con hilo encerado y piedras naturales. Envíos a toda la República Mexicana.">
  <meta name="keywords" content="artesanías, accesorios hechos a mano, pulseras artesanales, collares macramé, joyería artesanal, piedras naturales, hilo encerado, arte hilo, tejidos">
  <meta name="author" content="Arte Hilo - Tejidos del Alma">
  <meta name="robots" content="index, follow">
  
  <meta property="og:title" content="Arte Hilo: Tejidos del Alma - Accesorios Artesanales">
  <meta property="og:description" content="Accesorios únicos hechos 100% a mano. Cada nudo y cada piedra llevan una intención. Descubre nuestras creaciones.">
  <meta property="og:image" content="img/banner-principal.jpg">
  <meta property="og:url" content="https://artehilo.com/">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="es_MX">
  
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Arte Hilo: Tejidos del Alma">
  <meta name="twitter:description" content="Accesorios artesanales únicos hechos a mano con amor y dedicación">
  <meta name="twitter:image" content="img/banner-principal.jpg">
  
  <link rel="icon" type="image/png" sizes="32x32" href="img/favicon-32.png">
  <link rel="icon" type="image/png" sizes="192x192" href="img/favicon.png">
  <link rel="apple-touch-icon" sizes="180x180" href="img/favicon.png">
  <link rel="shortcut icon" type="image/png" href="img/favicon-32.png">
  
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="css/style.css">
  
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'sans-serif'] },
          colors: {
            primary: { light: '#d4956f', DEFAULT: '#c17654', dark: '#a55a3a' },
            secondary: '#d4a574',
            accent: '#8b9169',
            earth: { light: '#e8d5c4', medium: '#c9a77c', dark: '#6b5344' }
          }
        }
      }
    }
  </script>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans text-gray-800 flex flex-col min-h-screen">

  <div id="toast-container" class="fixed top-20 right-4 z-50 space-y-2" role="alert" aria-live="polite"></div>

  <header class="bg-white shadow-md sticky top-0 z-50" role="banner">
    <nav class="container mx-auto px-6 py-4 flex justify-between items-center" role="navigation" aria-label="Menú principal">
      
      <div class="logo">
        <a href="index.php?page=inicio" class="flex items-center gap-3 text-2xl font-bold text-earth-dark hover:text-primary transition-colors duration-300" aria-label="Ir a página de inicio">
          <img src="img/logo.png" alt="Logo Arte Hilo" class="h-10 w-10 object-contain">
          <span>Arte Hilo</span>
        </a>
      </div>
      
      <?php $currentPage = isset($_GET['page']) ? $_GET['page'] : 'inicio'; ?>
      
      <ul class="hidden md:flex space-x-6" role="menubar">
        <li role="none">
          <a href="index.php?page=inicio" class="nav-link <?= ($currentPage == 'inicio') ? 'active' : '' ?>" role="menuitem" <?= ($currentPage == 'inicio') ? 'aria-current="page"' : '' ?>>Inicio</a>
        </li>
        <li role="none">
          <a href="index.php?page=creaciones_dinamico" class="nav-link <?= ($currentPage == 'creaciones' || $currentPage == 'creaciones_dinamico') ? 'active' : '' ?>" role="menuitem">Nuestras Creaciones</a>
        </li>
        <li role="none">
          <a href="index.php?page=nosotros" class="nav-link <?= ($currentPage == 'nosotros') ? 'active' : '' ?>" role="menuitem">Nosotros</a>
        </li>
        <li role="none">
          <a href="index.php?page=contacto" class="nav-link <?= ($currentPage == 'contacto') ? 'active' : '' ?>" role="menuitem">Pedidos Especiales</a>
        </li>
      </ul>
      
      <div class="flex items-center space-x-4">
        
        <a href="index.php?page=carrito" id="cart-icon" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-primary transition-colors duration-300" aria-label="Ver carrito de compras">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          <span id="cart-badge" class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-primary rounded-full <?= $items_carrito > 0 ? '' : 'hidden' ?>">
            <span id="cart-counter"><?= $items_carrito ?></span>
          </span>
        </a>
        
        <?php if (isLoggedIn()): ?>
          <div class="relative group">
            <button class="flex items-center space-x-2 px-4 py-2 text-sm font-medium text-gray-700 hover:text-primary transition-colors duration-300">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              <?php 
                $nombre = getUserName();
                $primerNombre = !empty($nombre) ? explode(' ', $nombre)[0] : 'Usuario';
              ?>
              <span>Hola, <?= e($primerNombre) ?></span>
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            
            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
              <div class="py-2">
                <a href="index.php?page=mis_pedidos" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-primary transition-colors">Mis Pedidos</a>
                <a href="index.php?page=ayuda" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-primary transition-colors">Ayuda</a>
                <?php if (isAdmin()): ?>
                <a href="admin/" class="block px-4 py-2 text-sm font-semibold text-purple-700 hover:bg-purple-50 transition-colors">Panel Admin</a>
                <?php endif; ?>
                <hr class="my-2">
                <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">Cerrar Sesión</a>
              </div>
            </div>
          </div>
          
        <?php else: ?>
          <a href="index.php?page=login" class="px-4 py-2 text-sm font-medium text-primary hover:text-primary-dark transition-colors duration-300">Iniciar Sesión</a>
          <a href="index.php?page=registro" class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-dark rounded-lg shadow-md hover:shadow-lg transition-all duration-300">Registrarse</a>
        <?php endif; ?>
        
      </div>
      
    </nav>
  </header>
