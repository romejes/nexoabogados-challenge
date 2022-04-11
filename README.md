# Nexo Abogados

## Requisitos previos
Tener instalado lo siguiente
- PHP >= 7.2
- MariaDB >= 10.4
- Composer >= 2.3

## Instalación
- Clonar el repositorio.
- Copiar el archivo .env.example y renombrarlo como .env. 
- Crear una base de datos en MySQL con el nombre que desee.
- Configure en el archivo .env los parametros de conexion con la base de datos.
- Ejecutar ```composer install``` para instalar los paquetes del proyecto.
- Ejecutar las migraciones y seeders con el comando ```php artisan migrate --seed```.
- Para poner en marcha localmente y ejecutar los endpoints en un cliente REST ejecute el comando  ```php artisan serve```
- Para poner en marcha el funcionamiento de los pagos recurrentes ejecute los comandos ```php artisan schedule:work``` para el Task Scheduling y ```php artisan queue:work``` para el manejo de colas y ejecucion de procesos en segundo plano.


## Estructura del proyecto
Las carpetas mas importantes donde reside la funcionalidad del proyecto son las siguientes.

#### Http/Controllers
Donde se encuentran los controladores, punto de partida de 
cada endpoint.

#### Services
Donde se encuentran los servicios, en los que se encuentra la lógica de la aplicación

#### Repositories
Donde se encuentran los repositorios, que se encargan de obtener informacion de la base de datos

#### Models
Donde se encuentran los modelos, unidad basica del proyecto

----
Adicionalmente tambien tenemos:

#### Jobs
Donde se encuentran las tareas que se van a ejecutar en segundo plano.

#### Http/Requests
Donde se encuentran las clases que representan los form request para algunos endpoints en el que tambien definimos las reglas de validacion iniciales.

#### Http/Resources
Aqui se define la plantilla del response de cada endpoint
