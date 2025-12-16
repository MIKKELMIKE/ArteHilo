<section class="contact-header mb-10" aria-labelledby="contact-heading">
  <h1 id="contact-heading" class="text-4xl md:text-5xl font-bold text-center text-gray-900 mb-4">
     Pedidos Especiales
  </h1>
  <p class="text-center text-gray-600 max-w-2xl mx-auto text-lg">
    ¿Tienes una idea única en mente? Creamos piezas personalizadas especialmente para ti. 
    Llena el formulario y nos pondremos en contacto para hacer realidad tu diseño.
  </p>
</section>

<section class="contact-form-section" aria-label="Formulario de contacto">
  <div class="max-w-3xl mx-auto bg-white p-8 md:p-12 rounded-lg shadow-2xl">
    
    <form action="enviar_formulario.php" method="POST" id="contactForm" novalidate aria-label="Formulario de pedido personalizado">
      
      <div class="mb-4">
        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo <span class="text-red-500">*</span></label>
        <input type="text" id="nombre" name="nombre" required minlength="3" maxlength="100" placeholder="Ej. María García López"
            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all" aria-required="true">
        <span id="nombre-error" class="text-red-500 text-sm hidden" role="alert"></span>
      </div>

      <div class="mb-4">
        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono (WhatsApp) <span class="text-red-500">*</span></label>
        <input type="tel" id="telefono" name="telefono" required pattern="[0-9]{10}" placeholder="Ej. 5512345678 (10 dígitos)"
            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all" aria-required="true">
        <span id="telefono-error" class="text-red-500 text-sm hidden" role="alert"></span>
      </div>

      <div class="mb-4">
        <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico <span class="text-red-500">*</span></label>
        <input type="email" id="correo" name="correo" required placeholder="Ej. maria@ejemplo.com"
            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all" aria-required="true">
        <span id="correo-error" class="text-red-500 text-sm hidden" role="alert"></span>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
          <label for="producto" class="block text-sm font-medium text-gray-700 mb-1">Producto de interés <span class="text-red-500">*</span></label>
          <select id="producto" name="producto" required
              class="w-full h-[50px] px-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-primary focus:border-primary transition-all appearance-none"
              style="background-image: url('data:image/svg+xml;utf8,<svg fill=\"%23666\" height=\"20\" viewBox=\"0 0 24 24\" width=\"20\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>'); background-repeat: no-repeat; background-position: right 0.7rem center; background-size: 1.5rem;">
            <option value="">-- Selecciona una opción --</option>
            <option value="pulsera">Pulsera</option>
            <option value="collar">Collar</option>
            <option value="llavero">Llavero</option>
            <option value="tobillera">Tobillera</option>
            <option value="otro">Otro (especificar abajo)</option>
          </select>
        </div>
        
        <div>
          <label for="cantidad" class="block text-sm font-medium text-gray-700 mb-1">Cantidad <span class="text-red-500">*</span></label>
          <input type="number" id="cantidad" name="cantidad" value="1" min="1" max="50" required
              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all">
        </div>
      </div>

      <div class="mb-4">
        <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color preferido</label>
        <select id="color" name="color"
            class="w-full h-[50px] px-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-primary focus:border-primary transition-all appearance-none"
            style="background-image: url('data:image/svg+xml;utf8,<svg fill=\"%23666\" height=\"20\" viewBox=\"0 0 24 24\" width=\"20\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>'); background-repeat: no-repeat; background-position: right 0.7rem center; background-size: 1.5rem;">
          <option value="">-- Selecciona un color --</option>
          <option value="Negro">Negro</option>
          <option value="Café">Café</option>
          <option value="Beige">Beige</option>
          <option value="Blanco">Blanco</option>
          <option value="Rojo">Rojo</option>
          <option value="Azul">Azul</option>
          <option value="Verde">Verde</option>
          <option value="Morado">Morado</option>
          <option value="Rosa">Rosa</option>
          <option value="Turquesa">Turquesa</option>
          <option value="Multicolor">Multicolor</option>
          <option value="Otro">Otro (especificar en comentarios)</option>
        </select>
      </div>

      <div class="mb-4">
        <fieldset>
          <legend class="block text-sm font-medium text-gray-700 mb-2">Tipo de pedido <span class="text-red-500">*</span></legend>
          <div class="space-y-2">
            <label class="flex items-center cursor-pointer">
              <input type="radio" name="tipo_pedido" value="catalogo" checked class="mr-2 text-primary focus:ring-primary">
              <span class="text-gray-700">Del catálogo (producto existente)</span>
            </label>
            <label class="flex items-center cursor-pointer">
              <input type="radio" name="tipo_pedido" value="personalizado" class="mr-2 text-primary focus:ring-primary">
              <span class="text-gray-700">Personalizado (diseño único)</span>
            </label>
          </div>
        </fieldset>
      </div>

      <div class="mb-4">
        <label for="comentarios" class="block text-sm font-medium text-gray-700 mb-1">Comentarios o especificaciones <span class="text-red-500">*</span></label>
        <textarea id="comentarios" name="comentarios" rows="5" required minlength="10" maxlength="1000"
             placeholder="Ej. Me gustaría una pulsera con hilo negro y una piedra de obsidiana, con cierre ajustable..."
             class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all resize-none"></textarea>
        <span id="comentarios-error" class="text-red-500 text-sm hidden" role="alert"></span>
        <p class="text-xs text-gray-500 mt-1">Mínimo 10 caracteres</p>
      </div>

      <div class="mb-6">
        <label class="flex items-start cursor-pointer">
          <input type="checkbox" id="terminos" name="terminos" required class="mr-3 mt-1 text-primary focus:ring-primary">
          <span class="text-sm text-gray-700">Acepto los términos y condiciones. Autorizo el uso de mis datos para contacto. <span class="text-red-500">*</span></span>
        </label>
        <span id="terminos-error" class="text-red-500 text-sm hidden" role="alert"></span>
      </div>

      <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-3 px-6 rounded-lg shadow-lg transition-all duration-300 transform hover:scale-[1.02] text-lg">
        📩 Enviar Solicitud
      </button>
      
    </form>
    
  </div>
</section>

<section class="max-w-3xl mx-auto mt-12 mb-8">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
    <div class="bg-white p-6 rounded-lg shadow-md">
      <div class="text-4xl mb-3"></div>
      <h3 class="font-semibold text-gray-800 mb-2">Email</h3>
      <a href="mailto:correo@artehilo.com" class="text-primary hover:underline">correo@artehilo.com</a>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md">
      <div class="text-4xl mb-3"></div>
      <h3 class="font-semibold text-gray-800 mb-2">WhatsApp</h3>
      <a href="https://wa.me/525512345678" target="_blank" class="text-primary hover:underline">55 1234 5678</a>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md">
      <div class="text-4xl mb-3"></div>
      <h3 class="font-semibold text-gray-800 mb-2">Ubicación</h3>
      <p class="text-gray-600">Aguascalientes, México</p>
    </div>
  </div>
</section>
