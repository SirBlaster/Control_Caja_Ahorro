document.addEventListener('DOMContentLoaded', function() {
            // Validar que las contraseñas coincidan
            document.getElementById('formEditar').addEventListener('submit', function(e) {
                const password = document.querySelector('input[name="password"]').value;
                const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
                
                // Si se ingresó una contraseña, validar
                if (password || confirmPassword) {
                    if (password !== confirmPassword) {
                        e.preventDefault();
                        alert('Las contraseñas no coinciden. Por favor, verifique.');
                        return false;
                    }
                    
                    if (password.length < 8) {
                        e.preventDefault();
                        alert('La contraseña debe tener al menos 8 caracteres.');
                        return false;
                    }
                }
                
                // Confirmación de cambios importantes
                const rolSelect = document.querySelector('select[name="id_rol"]');
                const estadoSelect = document.querySelector('select[name="habilitado"]');
                
                const rolOriginal = rolSelect.value;
                const estadoOriginal = estadoSelect.value;
                
                if (rolSelect.value !== rolOriginal) {
                    const nuevoRol = rolSelect.options[rolSelect.selectedIndex].text;
                    if (!confirm(`¿Está seguro de cambiar el rol a "${nuevoRol}"?`)) {
                        e.preventDefault();
                        return false;
                    }
                }
                
                if (estadoSelect.value !== estadoOriginal) {
                    const nuevoEstado = estadoSelect.value == '1' ? 'Habilitado' : 'Deshabilitado';
                    if (!confirm(`¿Está seguro de cambiar el estado a "${nuevoEstado}"?`)) {
                        e.preventDefault();
                        return false;
                    }
                }
                
                return true;
            });
        });