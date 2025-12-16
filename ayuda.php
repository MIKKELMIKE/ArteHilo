<?php
requireLogin('Debes iniciar sesión para acceder al centro de ayuda');

$nombre = getUserName();
$email = getUserEmail();

$pedidosRecientes = [];
try {
  $pdo = getDB();
  $stmt = $pdo->prepare("SELECT id, total, estado, fecha FROM pedidos WHERE usuario_id = ? ORDER BY id DESC LIMIT 5");
  $stmt->execute([getUserId()]);
  $pedidosRecientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $pedidosRecientes = [];
}

$mensajeEnviado = isset($_GET['ayuda_enviada']) && $_GET['ayuda_enviada'] == '1';
?>

<?php if ($mensajeEnviado): ?>
<div class="max-w-3xl mx-auto mb-8">
  <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-lg">
    <h3 class="text-lg font-bold text-green-800 mb-2">Solicitud Recibida</h3>
    <p class="text-green-700">Estamos procesando tu solicitud. Nos pondremos en contacto contigo a la brevedad posible.</p>
  </div>
</div>
<?php endif; ?>

<section class="help-header mb-10" aria-labelledby="help-heading">
  <h1 id="help-heading" class="text-4xl md:text-5xl font-bold text-center text-gray-900 mb-4">
    Centro de Ayuda
  </h1>
  <p class="text-center text-gray-600 max-w-2xl mx-auto text-lg">
    Hola <strong><?= e($nombre) ?></strong>, ¿en qué podemos ayudarte hoy?
  </p>
</section>

<section class="quick-options mb-10">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
    
    <a href="index.php?page=mis_pedidos" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 text-center">
      <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
      </div>
      <h3 class="text-lg font-bold text-gray-800 mb-2">Mis Pedidos</h3>
      <p class="text-sm text-gray-600">Ver estado de tus pedidos</p>
    </a>
    
    <a href="index.php?page=contacto" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 text-center">
      <div class="bg-amber-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
        </svg>
      </div>
      <h3 class="text-lg font-bold text-gray-800 mb-2">Pedido Especial</h3>
      <p class="text-sm text-gray-600">Solicitar pieza personalizada</p>
    </a>
    
    <a href="#faq" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 text-center">
      <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <h3 class="text-lg font-bold text-gray-800 mb-2">Preguntas Frecuentes</h3>
      <p class="text-sm text-gray-600">Respuestas rápidas</p>
    </a>
    
  </div>
</section>

<section class="support-form-section mb-10">
  <div class="max-w-3xl mx-auto bg-white p-8 md:p-12 rounded-xl shadow-2xl">
    
    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Enviar Solicitud de Ayuda</h2>
    
    <form action="enviar_formulario.php" method="POST" id="helpForm">
      <input type="hidden" name="tipo_formulario" value="ayuda">
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
          <input type="text" name="nombre" value="<?= e($nombre) ?>" readonly
              class="w-full p-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-600">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input type="email" name="correo" value="<?= e($email) ?>" readonly
              class="w-full p-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-600">
        </div>
      </div>
      
      <div class="mb-4">
        <label for="asunto" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Consulta <span class="text-red-500">*</span></label>
        <select id="asunto" name="asunto" required
            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
          <option value="">-- Selecciona una opción --</option>
          <option value="estado_pedido">Estado de mi pedido</option>
          <option value="modificar_pedido">Modificar mi pedido</option>
          <option value="cancelar_pedido">Cancelar mi pedido</option>
          <option value="devolucion">Devolución o cambio</option>
          <option value="problema_pago">Problema con el pago</option>
          <option value="cuenta">Problema con mi cuenta</option>
          <option value="sugerencia">Sugerencia o comentario</option>
          <option value="otro">Otro</option>
        </select>
      </div>
      
      <?php if (!empty($pedidosRecientes)): ?>
      <div class="mb-4">
        <label for="pedido_relacionado" class="block text-sm font-medium text-gray-700 mb-1">Pedido Relacionado (opcional)</label>
        <select id="pedido_relacionado" name="pedido_relacionado"
            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
          <option value="">-- Ninguno / No aplica --</option>
          <?php foreach ($pedidosRecientes as $pedido): ?>
          <option value="<?= $pedido['id'] ?>">
            Pedido #<?= str_pad($pedido['id'], 6, '0', STR_PAD_LEFT) ?> - $<?= number_format($pedido['total'], 2) ?> - <?= $pedido['estado'] ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php endif; ?>
      
      <div class="mb-6">
        <label for="mensaje" class="block text-sm font-medium text-gray-700 mb-1">Describe tu consulta <span class="text-red-500">*</span></label>
        <textarea id="mensaje" name="comentarios" rows="5" required minlength="10" maxlength="1000"
             placeholder="Cuéntanos con detalle cómo podemos ayudarte..."
             class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary resize-none"></textarea>
      </div>
      
      <button type="submit" 
          class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-4 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
        Enviar Solicitud
      </button>
      
    </form>
    
  </div>
</section>

<section id="faq" class="faq-section mb-10">
  <div class="max-w-3xl mx-auto">
    
    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Preguntas Frecuentes</h2>
    
    <div class="space-y-4">
      
      <details class="bg-white rounded-xl shadow-md overflow-hidden group">
        <summary class="p-5 cursor-pointer font-semibold text-gray-800 hover:bg-gray-50 flex justify-between items-center">
          ¿Cuánto tarda en llegar mi pedido?
          <svg class="w-5 h-5 text-primary group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </summary>
        <div class="p-5 pt-0 text-gray-600">
          Los pedidos regulares se envían en 3-5 días hábiles después de confirmado el pago. 
          Los pedidos personalizados pueden tardar 7-14 días dependiendo de la complejidad.
        </div>
      </details>
      
      <details class="bg-white rounded-xl shadow-md overflow-hidden group">
        <summary class="p-5 cursor-pointer font-semibold text-gray-800 hover:bg-gray-50 flex justify-between items-center">
          ¿Puedo modificar mi pedido después de realizarlo?
          <svg class="w-5 h-5 text-primary group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </summary>
        <div class="p-5 pt-0 text-gray-600">
          Sí, puedes solicitar modificaciones mientras el pedido esté en estado "Pendiente". 
          Una vez que pase a "Procesando", ya no es posible modificarlo.
        </div>
      </details>
      
      <details class="bg-white rounded-xl shadow-md overflow-hidden group">
        <summary class="p-5 cursor-pointer font-semibold text-gray-800 hover:bg-gray-50 flex justify-between items-center">
          ¿Qué métodos de pago aceptan?
          <svg class="w-5 h-5 text-primary group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </summary>
        <div class="p-5 pt-0 text-gray-600">
          Aceptamos transferencia bancaria y depósito en OXXO. 
          Al finalizar tu pedido recibirás los datos para realizar el pago.
        </div>
      </details>
      
      <details class="bg-white rounded-xl shadow-md overflow-hidden group">
        <summary class="p-5 cursor-pointer font-semibold text-gray-800 hover:bg-gray-50 flex justify-between items-center">
          ¿Tienen política de devoluciones?
          <svg class="w-5 h-5 text-primary group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </summary>
        <div class="p-5 pt-0 text-gray-600">
          Sí. Si tu producto llega dañado o con defectos, tienes 7 días para solicitar un cambio o devolución.
          Los productos personalizados no son reembolsables a menos que tengan defectos de fabricación.
        </div>
      </details>
      
      <details class="bg-white rounded-xl shadow-md overflow-hidden group">
        <summary class="p-5 cursor-pointer font-semibold text-gray-800 hover:bg-gray-50 flex justify-between items-center">
          ¿Cómo cuido mis accesorios de macramé?
          <svg class="w-5 h-5 text-primary group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </summary>
        <div class="p-5 pt-0 text-gray-600">
          - Evita el contacto prolongado con agua<br>
          - Guárdalos en un lugar seco y sin humedad<br>
          - No uses productos químicos cerca de ellos<br>
          - Evita exponer las piedras naturales al sol directo
        </div>
      </details>
      
    </div>
    
  </div>
</section>

<section class="direct-contact">
  <div class="max-w-3xl mx-auto bg-gradient-to-r from-primary to-secondary rounded-xl p-8 text-white text-center">
    <h3 class="text-2xl font-bold mb-4">¿Necesitas ayuda urgente?</h3>
    <p class="mb-6">Contáctanos directamente por WhatsApp para una respuesta más rápida</p>
    <a href="https://wa.me/524491234567?text=Hola,%20necesito%20ayuda%20con%20mi%20pedido" 
      target="_blank"
      class="inline-flex items-center gap-2 bg-white text-primary font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition-colors">
      <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
      </svg>
      Chatear por WhatsApp
    </a>
  </div>
</section>
