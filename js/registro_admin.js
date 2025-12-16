// Funciones de validación en tiempo real
        function validarNombre(input) {
            const errorSpan = document.getElementById('nombreError');
            if (input.value.trim().length < 2) {
                errorSpan.textContent = 'El nombre debe tener al menos 2 caracteres';
                errorSpan.className = 'text-danger';
                return false;
            } else {
                errorSpan.textContent = '✓ Válido';
                errorSpan.className = 'text-success';
                return true;
            }
        }

        function validarApellido(input) {
            const id = input.id;
            const errorSpan = document.getElementById(id + 'Error');
            if (input.value && input.value.trim().length < 2) {
                errorSpan.textContent = 'El apellido debe tener al menos 2 caracteres';
                errorSpan.className = 'text-danger';
                return false;
            } else if (input.value) {
                errorSpan.textContent = '✓ Válido';
                errorSpan.className = 'text-success';
                return true;
            } else {
                errorSpan.textContent = '';
                return true;
            }
        }

        function validarCorreoInstitucional(input) {
            const errorSpan = document.getElementById('correoInstError');
            const email = input.value.trim();
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!email) {
                errorSpan.textContent = 'Campo requerido';
                return false;
            }
            
            if (!regex.test(email)) {
                errorSpan.textContent = 'Formato de correo inválido';
                return false;
            }
            
            if (!email.toLowerCase().includes('itsx')) {
                errorSpan.textContent = 'Recomendado: usar correo institucional ITSX';
                errorSpan.className = 'text-warning';
                return true;
            } else {
                errorSpan.textContent = '✓ Válido';
                errorSpan.className = 'text-success';
                return true;
            }
        }

        function validarCorreoPersonal(input) {
            const errorSpan = document.getElementById('correoPersError');
            const email = input.value.trim();
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!email) {
                errorSpan.textContent = 'Campo requerido';
                return false;
            }
            
            if (!regex.test(email)) {
                errorSpan.textContent = 'Formato de correo inválido';
                return false;
            }
            
            errorSpan.textContent = '✓ Válido';
            errorSpan.className = 'text-success';
            return true;
        }

        function validarTelefono(input) {
            const errorSpan = document.getElementById('telefonoError');
            const telefono = input.value.replace(/\D/g, '');
            
            if (telefono && telefono.length < 10) {
                errorSpan.textContent = 'Mínimo 10 dígitos';
                return false;
            }
            
            errorSpan.textContent = '';
            return true;
        }

        function validarPassword(input) {
            const password = input.value;
            
            // Validar longitud
            document.getElementById('reqLongitud').style.color = password.length >= 8 ? 'green' : 'gray';
            
            // Validar mayúscula
            document.getElementById('reqMayuscula').style.color = /[A-Z]/.test(password) ? 'green' : 'gray';
            
            // Validar número
            document.getElementById('reqNumero').style.color = /\d/.test(password) ? 'green' : 'gray';
            
            return password.length >= 8 && /[A-Z]/.test(password) && /\d/.test(password);
        }

        function validarConfirmacionPassword(input) {
            const errorSpan = document.getElementById('confirmError');
            const password = document.getElementById('password').value;
            const confirmacion = input.value;
            
            if (password !== confirmacion) {
                errorSpan.textContent = 'Las contraseñas no coinciden';
                return false;
            }
            
            errorSpan.textContent = '✓ Coinciden';
            errorSpan.className = 'text-success';
            return true;
        }

        function validarFormulario() {
            const campos = [
                validarNombre(document.getElementById('nombre')),
                validarApellido(document.getElementById('paterno')),
                validarCorreoInstitucional(document.getElementById('correo_institucional')),
                validarCorreoPersonal(document.getElementById('correo_personal')),
                validarPassword(document.getElementById('password')),
                validarConfirmacionPassword(document.getElementById('confirm_password'))
            ];
            
            const terminos = document.getElementById('terminos').checked;
            if (!terminos) {
                alert('Debe aceptar los términos y condiciones');
                return false;
            }
            
            return campos.every(campo => campo === true);
        }

        // Inicializar tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Prevenir envío si hay campos inválidos
            document.getElementById('registroForm').addEventListener('submit', function(e) {
                if (!validarFormulario()) {
                    e.preventDefault();
                    alert('Por favor, corrija los errores en el formulario antes de enviar.');
                }
            });
        });