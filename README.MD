# Diseño de Arquitecturas Modulares Funcionales en Aplicaciones Web con PHP y MySQL

Este proyecto es un sistema de gestión de categorías, desarrollado utilizando PHP, JavaScript (jQuery), Bootstrap y DataTables. Permite crear, editar y eliminar categorías a través de una interfaz web.

## Estructura del Proyecto

- **index.php**: Archivo principal que redirige a la página de categorías.
- **categorias/index.html**: Página principal del módulo de categorías.
- **categorias/categorias.js**: Archivo JavaScript que maneja las operaciones AJAX y la manipulación de la tabla de categorías.
- **php/categorias.php**: Archivo PHP que gestiona las operaciones CRUD con la base de datos.

## Requisitos

- PHP 7.4 o superior
- Servidor Web (Apache, Nginx, etc.)
- Base de datos MySQL
- jQuery, Bootstrap, DataTables

## Instalación

1. Clona este repositorio en tu servidor local o de desarrollo.
2. Configura tu servidor web para que apunte al directorio donde se encuentra `index.php`.
3. Crea una base de datos en MySQL y ejecuta el script SQL para crear la tabla de categorías (incluido en `database/schema.sql`).
4. Actualiza el archivo de configuración de la base de datos en `config/database.php` con tus credenciales de MySQL.
5. Asegúrate de que los archivos de configuración (como `database.php`) estén protegidos y no accesibles públicamente.

## Uso

1. Navega a la página principal del módulo de categorías.
2. Utiliza la interfaz para crear, editar o eliminar categorías.
3. Los cambios se reflejarán inmediatamente en la tabla de categorías.

## Contribuciones

Si deseas contribuir al proyecto, por favor realiza un fork del repositorio, realiza tus cambios y envía un pull request.

## Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo LICENSE para más detalles.
Realizado por <a href="mailto:tabindev@gmail.com">Ing Gustavo Arias </a>
