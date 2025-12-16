document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    const nombreInput = document.getElementById('nombre');
    const telefonoInput = document.getElementById('telefono');
    const correoInput = document.getElementById('correo');
    const comentariosInput = document.getElementById('comentarios');
    const terminosInput = document.getElementById('terminos');
    const submitBtn = document.getElementById('submitBtn');
    
    if (!form || !nombreInput || !telefonoInput || !correoInput || !comentariosInput || !terminosInput) return;

    function mostrarError(input, mensaje) {
        const errorSpan = document.getElementById(input.id + '-error');
        if (errorSpan) {
            errorSpan.textContent = mensaje;
            errorSpan.classList.remove('hidden');
            input.classList.add('border-red-500');
            input.classList.remove('border-gray-300');
        }
    }

    function ocultarError(input) {
        const errorSpan = document.getElementById(input.id + '-error');
        if (errorSpan) {
            errorSpan.textContent = '';
            errorSpan.classList.add('hidden');
            input.classList.remove('border-red-500');
            input.classList.add('border-gray-300');
        }
    }

    function validarNombre() {
        const valor = nombreInput.value.trim();
        if (valor === '') { mostrarError(nombreInput, 'El nombre es obligatorio'); return false; }
        if (valor.length < 3) { mostrarError(nombreInput, 'Mínimo 3 caracteres'); return false; }
        ocultarError(nombreInput); return true;
    }

    function validarTelefono() {
        const valor = telefonoInput.value.trim();
        if (valor === '') { mostrarError(telefonoInput, 'El teléfono es obligatorio'); return false; }
        if (!/^[0-9]{10}$/.test(valor)) { mostrarError(telefonoInput, 'Teléfono válido: 10 dígitos'); return false; }
        ocultarError(telefonoInput); return true;
    }

    function validarCorreo() {
        const valor = correoInput.value.trim();
        if (valor === '') { mostrarError(correoInput, 'El correo es obligatorio'); return false; }
        if (!/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(valor)) { mostrarError(correoInput, 'Correo inválido'); return false; }
        ocultarError(correoInput); return true;
    }

    function validarComentarios() {
        const valor = comentariosInput.value.trim();
        if (valor === '') { mostrarError(comentariosInput, 'Los comentarios son obligatorios'); return false; }
        if (valor.length < 10) { mostrarError(comentariosInput, 'Mínimo 10 caracteres'); return false; }
        ocultarError(comentariosInput); return true;
    }

    function validarTerminos() {
        const errorSpan = document.getElementById('terminos-error');
        if (!terminosInput.checked) {
            if (errorSpan) { errorSpan.textContent = 'Debes aceptar los términos'; errorSpan.classList.remove('hidden'); }
            return false;
        }
        if (errorSpan) { errorSpan.textContent = ''; errorSpan.classList.add('hidden'); }
        return true;
    }

    nombreInput.addEventListener('blur', validarNombre);
    telefonoInput.addEventListener('blur', validarTelefono);
    correoInput.addEventListener('blur', validarCorreo);
    comentariosInput.addEventListener('blur', validarComentarios);
    terminosInput.addEventListener('change', validarTerminos);

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        if (validarNombre() && validarTelefono() && validarCorreo() && validarComentarios() && validarTerminos()) {
            if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = 'Enviando...'; }
            form.submit();
        } else {
            const primerError = form.querySelector('.border-red-500');
            if (primerError) { primerError.scrollIntoView({ behavior: 'smooth', block: 'center' }); primerError.focus(); }
        }
    });
});
