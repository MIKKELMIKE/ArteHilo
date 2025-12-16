function agregarAlCarrito(productoId, productoNombre, productoPrecio) {
    const btn = event.target;
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<span class="loading-spinner"></span> Agregando...';
    btn.disabled = true;

    const formData = new FormData();
    formData.append('action', 'agregar');
    formData.append('producto_id', productoId);
    formData.append('cantidad', 1);
    formData.append('ajax', '1');

    fetch('api_carrito.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            actualizarContadorCarrito(data.total_items);
            mostrarNotificacion('success', productoNombre + ' agregado al carrito', productoPrecio);
            btn.innerHTML = 'Agregado';
            btn.classList.add('bg-green-600');
            btn.classList.remove('bg-primary');
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.classList.remove('bg-green-600');
                btn.classList.add('bg-primary');
                btn.disabled = false;
            }, 2000);
        } else {
            mostrarNotificacion('error', data.message || 'Error al agregar');
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }
    })
    .catch(error => {
        mostrarNotificacion('error', 'Error de conexión');
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    });
}

function actualizarContadorCarrito(totalItems) {
    const contador = document.getElementById('cart-counter');
    const badge = document.getElementById('cart-badge');
    if (contador) contador.textContent = totalItems;
    if (badge) {
        if (totalItems > 0) badge.classList.remove('hidden');
        else badge.classList.add('hidden');
    }
}

function mostrarNotificacion(type, message, precio) {
    const toast = document.createElement('div');
    toast.className = 'toast-notification ' + (type === 'success' ? 'bg-green-50 border-green-500' : 'bg-red-50 border-red-500') + ' border-l-4 p-4 rounded-lg shadow-xl animate-fadeIn fixed top-20 right-4 z-50';
    
    const icon = type === 'success' 
        ? '<svg class="h-6 w-6 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
        : '<svg class="h-6 w-6 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>';
    
    let precioHTML = '';
    if (precio !== undefined && precio !== null) {
        precioHTML = '<p class="text-xs text-gray-600 mt-1">Precio: $' + parseFloat(precio).toFixed(2) + ' MXN</p>';
    }
    
    toast.innerHTML = '<div class="flex items-start"><div class="flex-shrink-0">' + icon + '</div><div class="ml-3 flex-1"><p class="text-sm font-semibold ' + (type === 'success' ? 'text-green-800' : 'text-red-800') + '">' + message + '</p>' + precioHTML + (type === 'success' ? '<div class="mt-2"><a href="index.php?page=carrito" class="text-xs font-semibold text-green-700 hover:text-green-900 underline">Ver Carrito →</a></div>' : '') + '</div><button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-gray-400 hover:text-gray-600"></button></div>';
    
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 5000);
}

function compraRapida(productoId) {
    const formData = new FormData();
    formData.append('action', 'agregar');
    formData.append('producto_id', productoId);
    formData.append('cantidad', 1);
    formData.append('ajax', '1');
    
    fetch('api_carrito.php', { method: 'POST', body: formData })
    .then(response => response.json())
    .then(data => {
        if (data.success) window.location.href = 'index.php?page=checkout';
        else mostrarNotificacion('error', data.message || 'Error');
    })
    .catch(() => mostrarNotificacion('error', 'Error de conexión'));
}

document.addEventListener('DOMContentLoaded', function() {
    fetch('api_carrito.php?action=get_count')
        .then(response => response.json())
        .then(data => { if (data.success) actualizarContadorCarrito(data.total_items); })
        .catch(() => {});
});

window.agregarAlCarrito = agregarAlCarrito;
window.compraRapida = compraRapida;
window.mostrarNotificacion = mostrarNotificacion;
