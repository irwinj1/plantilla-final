# LARAVEL API TEMPLATE

## Tecnologías utilizadas
1. Laravel 12 ó superior
2. PHP 8.3 o superior
3. Postgresql 16
4. Redis
5. MongoDB

## Guía de instalación
Como primer paso se debe realizar la copia del archivo **".env.example"** a **".env"** y configurar las variables de entorno que se van a ocupar, como por ejemplo la conexion a la base de datos, la copia del archivo se puede realizar ejecutando el siguiente comando:
````
cp .env.example .env;
````
Ademas se ha establecido un comando para el inicio del proyecto, teniendo en cuenta que se haya realizado el paso anterior, éste permite generar las claves de la aplicación, generar las migraciones, seeders y jwt key.

Este comando solo se debe ejecutar la primera vez que se instale la aplicación.
````
composer init-project;
````
## Convenciones de la plantilla
1. Las tablas de base de datos usan **snake_case** para sus nombres y deben ser creadas basadas en un módulo, es decir [Nombre modulo]_[Nombre de la tabla], *por ejemplo "proyecto_configuraciones", "proyecto_participantes"*.
   
2. Los modelos se construyen a partir del nombre de la tabla usando **CamelCase**.
   
3. Para nombre de controladores se debe establecer en CamelCase y debe tener como subfijo la palabra **"Controller"**.
   
4. Las rutas para la API se colocaran en el archivo **api.php**.
   
5. Las rutas usan el formato **kebab-case**, solo si es necesario, de otro modo, se debe mantener lo más posible la convención de uso REST, es decir, las rutas en lo posible no deben contener verbos y las acciones deben ser bien establecidas dados los métodos REST (POST, GET, PUT, DELETE).
   
6. Los recursos a usar de manera estática (como imágenes) se deben agregar a la carpeta **public/** y según el tipo en **/documents** o **/images**

## Comandos útiles

````
# Generar un nuevo modelo
php artisan make:model User

# Generar un nuevo controlador
php artisan make:controller UserController

# Ver toda la información de un modelo y agregar los modelos de los logs dentro de la carpeta Log
php artisan model:show User

# Generar migraciones
php artisan make:migration

# Generar request
php artisan make:request  EjemploUpdateRequest
````

## Documentación con scramble swagger
Scramble (Dedoc/Scramble) es un generador automático de documentación OpenAPI/Swagger para Laravel. Su función principal es analizar tus rutas, controladores y validaciones para generar una documentación Swagger que puedas consultar desde un panel interactivo.

para ver la documentacion:
http://127.0.0.1:8000/docs/api

Agregar nombre descriptivo al endpoint
    /**
     *
     * @operationId login
     */
Mas información en https://scramble.dedoc.co/

## conventional commit 
1. Ejecutar npm install
2. Guardar cambios con git add .
3. ejecutar comando "npx cz" y seguir los pasos para crear el convetional commit

## Ejemplos de usos de plantilla

### Uso de controlador
Revisar controlador UseController metodo index para ejemplo de consulta con cache y redis y sin cache para los metodos get.

### Ejemplo modelo para logs
1. Se recomienda crear un modelo para cada logs que desea guardar
2. Revisar ejemplo de modelo que esta en carpeta Model/Logs 
3. Para guardar los logs revisar metodo createUser dentro de UseController.