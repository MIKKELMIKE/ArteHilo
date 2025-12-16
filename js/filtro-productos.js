document.addEventListener('DOMContentLoaded', function() {
    const genderButtons = document.querySelectorAll('.gender-filter-btn');
    const categoryButtons = document.querySelectorAll('.filter-btn');
    const productCards = document.querySelectorAll('.product-card');
    const searchInput = document.getElementById('search-input');
    
    if (productCards.length === 0) return;
    
    let generoActivo = 'todos';
    let categoriaActiva = 'todos';
    let busquedaActiva = '';
    
    genderButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            generoActivo = this.getAttribute('data-gender');
            genderButtons.forEach(function(btn) {
                btn.classList.remove('bg-primary', 'text-white');
                btn.classList.add('bg-earth-light', 'text-gray-700');
            });
            this.classList.remove('bg-earth-light', 'text-gray-700');
            this.classList.add('bg-primary', 'text-white');
            filtrarProductos();
        });
    });
    
    categoryButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            categoriaActiva = this.getAttribute('data-filter');
            categoryButtons.forEach(function(btn) {
                btn.classList.remove('bg-primary', 'text-white');
                btn.classList.add('bg-earth-light', 'text-gray-700');
            });
            this.classList.remove('bg-earth-light', 'text-gray-700');
            this.classList.add('bg-primary', 'text-white');
            filtrarProductos();
        });
    });
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            busquedaActiva = this.value.toLowerCase().trim();
            filtrarProductos();
        });
    }
    
    function filtrarProductos() {
        let visibles = 0;
        productCards.forEach(function(card) {
            var cardGender = card.getAttribute('data-gender') || '';
            var cardCategory = card.getAttribute('data-category') || '';
            var cardName = (card.getAttribute('data-name') || '').toLowerCase();
            
            var cumpleGenero = (generoActivo === 'todos' || cardGender === generoActivo);
            var cumpleCategoria = (categoriaActiva === 'todos' || cardCategory === categoriaActiva);
            var cumpleBusqueda = (busquedaActiva === '' || cardName.indexOf(busquedaActiva) !== -1);
            
            if (cumpleGenero && cumpleCategoria && cumpleBusqueda) {
                card.style.display = 'block';
                visibles++;
            } else {
                card.style.display = 'none';
            }
        });
        
        var countEl = document.getElementById('product-count');
        if (countEl) countEl.textContent = visibles;
        
        mostrarMensajeSinResultados(visibles === 0);
    }
    
    function mostrarMensajeSinResultados(mostrar) {
        var mensaje = document.getElementById('no-results-message');
        if (mostrar && !mensaje) {
            var container = document.querySelector('.grid');
            if (container) {
                mensaje = document.createElement('div');
                mensaje.id = 'no-results-message';
                mensaje.className = 'col-span-full text-center py-12';
                mensaje.innerHTML = '<p class="text-xl text-gray-600">No se encontraron productos</p><p class="text-gray-500 mt-2">Intenta con otros filtros</p>';
                container.appendChild(mensaje);
            }
        } else if (!mostrar && mensaje) {
            mensaje.remove();
        }
    }
});
