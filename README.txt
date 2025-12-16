PROYECTO: SISTEMA DE CAJA DE AHORRO (SETDITSX)
---------------------------------------------------
REQUISITOS:
---------------------------------------------------
- Servidor local (Laragon, XAMPP o WAMP).
- PHP 7.4 o superior.
- MySQL / MariaDB 8.0.
- Docker (Aplicación de Escritorio)
---------------------------------------------------
PASOS PARA INSTALAR:
---------------------------------------------------
1. BASE DE DATOS:
- Abra phpMyAdmin.
- Cree una base de datos llamada: sistema_caja
- Importe el archivo 'sistema_caja.sql' que se anexa en este paquete.
2. DOCKER:
- Ejecutar "docker-compoe.yml" desde VS code (extensión de Docker descargada).
- Abrir la aplicación de escritorio y ejecutar docker "control_caja_ahorro"
3. CÓDIGO:
- Copie la carpeta 'ControlCajadeAhorro' dentro de su carpeta 'www' o 'htdocs'.
- Configure el archivo 'includes/conexion.php' si su contraseña de MySQL no es vacía.
4. ACCESO:
- Abra el navegador en: localhost/ControlCajadeAhorro
---------------------------------------------------
CREDENCIALES DE PRUEBA (USUARIOS):
---------------------------------------------------
ROL: SUPERUSUARIO
Correo: superadmin@itsx.edu.mx
Pass: super123

Administrador
admin@itsx.edu.mx
12345

Ahorrador
juan321@itsx.edu.mx

user123
