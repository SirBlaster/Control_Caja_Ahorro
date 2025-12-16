function recargarDatos() {
            location.reload();
        }
        
        function insertarDatosPrueba() {
            if (confirm('¿Insertar datos de prueba en la auditoría?')) {
                fetch('../includes/test_auditoria.php')
                    .then(response => response.text())
                    .then(data => {
                        alert('Datos de prueba insertados. Recargando...');
                        location.reload();
                    })
                    .catch(error => {
                        alert('Error: ' + error);
                    });
            }
        }
        
        function exportarCSV() {
            // Implementación simple de CSV
            const rows = document.querySelectorAll('table tbody tr');
            let csv = [];
            
            // Encabezados
            const headers = [];
            document.querySelectorAll('thead th').forEach(th => {
                headers.push(th.textContent.trim());
            });
            csv.push(headers.join(','));
            
            // Datos
            rows.forEach(row => {
                const cols = row.querySelectorAll('td');
                const rowData = Array.from(cols).map(col => {
                    let text = col.textContent.trim();
                    text = text.replace(/\n/g, ' ');
                    text = text.replace(/\s+/g, ' ');
                    return `"${text}"`;
                }).join(',');
                csv.push(rowData);
            });
            
            const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'auditoria_' + new Date().toISOString().slice(0,10) + '.csv';
            a.click();
        }