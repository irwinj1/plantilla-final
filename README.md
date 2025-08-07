# TEMPLATE LARAVEL STANDARD

## Requisitos

### Tecnologías

- PHP 8.4 o superior
- Composer
- PostgreSQL
- MongoDB
- Redis (Opcionalmente)

### Extensiones de PHP

- `pgsql`
- `pdo_pgsql`
- `mongodb`
- `gd`
- `mbstring`
- `fileinfo`

## Guía de instalación para entorno productivo o desarrollo

### 1. Variables de entorno
Generar una copia del archivo `.env.example`

```shell
cp .env.example .env
```

Configurar parámetros principales en archivo `.env`

- `APP_URL`: URL en la que se sirve la aplicación
- Configuración de base de datos **Postgres**
  - `DB_HOST`
  - `DB_PORT`
  - `DB_DATABASE`
  - `DB_USERNAME`
  - `DB_PASSWORD`
- `MONGODB_URI`: Conexión a base de datos mongo en formato URI
- `MONGODB_DATABASE`: Base de datos Mongo a utilizar
- `QUEUE_CONNECTION`: Según el alcance del proyecto se puede usar
  - `database` para proyectos pequeños
  - `mongodb` para proyectos medianos
  - `redis` para proyectos grandes
- Parámetros de conexión para base de datos Redis (si `QUEUE_CONNECTION` tiene el valor `redis`)
  - `REDIS_CLIENT`
  - `REDIS_HOST`
  - `REDIS_PASSWORD`
  - `REDIS_PORT`
- Configuración de SMTP para envío de correos
  - `MAIL_HOST`: Para Google se usa smtp.gmail.com
  - `MAIL_PORT`: 465 para Google
  - `MAIL_USERNAME`: Correo saliente
  - `MAIL_PASSWORD`: Contraseña de correo saliente
  - `MAIL_FROM_ADDRESS`: Correo que se le mostrará al usuario como remitente, puede ser `MAIL_USERNAME`
  - `MAIL_FROM_NAME`: Nombre del remitente, este se visualiza como sujeto en el correo recibido
- Las configuraciones Docker solo se deben modificar en entorno de desarrollo si se desea

### 1.1 Variables de entorno testing
```shell
cp .env.testing.example .env.testing
```
Configurar parámetros 

- `APP_URL`: URL en la que se sirve la aplicación
- Configuración de base de datos **Postgres**
  - `DB_HOST`
  - `DB_PORT`
  - `DB_DATABASE`
  - `DB_USERNAME`
  - `DB_PASSWORD`

### 2. Uso de Docker Compose

> 💡 Usa esto solo si es entorno de desarrollo, sino, pasa al paso 3

#### 2.1 Primera ejecución del proyecto

> 💡 Debes haber finalizado el paso 1 para ejecutar con éxito el proyecto

```shell
# Construcción de las imágenes y volúmenes
docker compose build --no-cache

# Ejecución del proyecto
docker compose up -d
```

#### 2.2 Comandos útiles

```shell
# Detener el proyecto
docker compose down

# Si has realizados cambios en el Dockerfile o docker-compose.yml
docker compose up -d --build

# 🔴 Detener el proyecto y eliminar los volumenes, esto incluye las bases de datos
docker compose down -v
```

### 3. Instalación de paquetes

> 💡 Si estás usando Docker, pasa al paso 4

```shell
composer install
```
```shell
npm install
```
### 4. JWT y llave de aplicación

> 💡 Si estás usando Docker, estos comandos deben estructurarse de la siguiente manera:
> `docker-compose exec <nombre-contenedor> <comando>`. Por ejemplo:
> `docker-compose exec laravel-app php artisan l5-swagger:generate`

Para generar la llave de aplicación y JWT se deben ejecutar los siguientes comandos:

```shell
php artisan key:generate

php artisan jwt:secret
```

### 5. Generación de documentación de API

Se instaló scramble doc, para generar la documentación de la API.
Para cambia nombre que se mostrara del metodo se puede utilizar el siguiente comentario antes de cada metodo:
      /**
     
     *
     * @operationId nombre a mostrar
     */

### 6. Migraciones y seeders

```shell
php artisan migrate --seed
```

### 7. Trabajos programados y colas de trabajo

#### 7.1 Trabajos programados en producción

1. Verificar la sincronización del tiempo con `timedatectl`
2. Configurar el nuevo trabajo programado con `crontab -e` con la siguiente línea

```shell
# Ejecutar las tareas programadas cada minuto
# Artisan se encargará de analizar según la programación los trabajos que debería ejecutar
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

#### 7.2 Colas de trabajo en producción

Dentro de la carpeta del proyecto ejecutar el siguiente comando

```shell
# Uso de nohup para mantener el proceso en ejecución en segundo plano indefinidamente
nohup php artisan queue:work >> storage/logs/laravel.log &
```

#### 7.3 Trabajos programados en desarrollo

Para facilitar la ejecución en desarrollo se han agregado los siguientes comandos de composer

```shell
# Para iniciar la ejecución de los trabajos programados en background
# El comando mantiene los logs en /storage/logs/schedule.log
composer schedules-work

# Detiene la ejecución de los trabajos programados
composer schedules-stop
```

#### 7.4 Colas de trabajo en desarrollo

Para facilitar la ejecución en desarrollo se han agregado los siguientes comandos de composer

```shell
# Para iniciar la cola de trabajo en un proceso en background
# El comando mantiene los logs en /storage/logs/queue.log
composer queues-work

# Reinicia las colas de trabajo después de terminar el último en cola
# Se usa caso de que existan cambios de código que involucren las colas de trabajo
composer queues-restart

# Detiene las colas de trabajo
composer queues-stop
```

## 8. Convenciones de la Plantilla

### 1. Tablas de Base de Datos

- Los nombres de las tablas deben seguir el formato **snake_case**.
- Las palabras deben estar en singular, por ejemplo: persona, usuario, perfil
- Cada tabla debe estar asociada a un módulo, utilizando el prefijo del módulo seguido del nombre de la tabla.
  - **Ejemplos:** `persona`, `persona_documento`.
- La llave primaria de las tablas debe tener el formato: `id_<nombre_tabla>`, por ejemplo: `id_persona`, `id_persona_documento`. También es válido: `id_per_doc`, siempre y cuando sea interprete fácilmente la tabla origen.

### 2. Modelos

- Se debe crear **una carpeta para cada módulo**, esto basado en la primer palabra de la tabla en base de datos, usar el formato **CamelCase** para el nombre de la carpeta.
- Nombres en formato **CamelCase**, basada en el nombre de la tabla correspondiente.
- Si son tablas diferentes a catálogos o tablas bitácora se deben configurar con `SoftDeletes`
- Se debe configurar las propiedades:
  - `protected $table`
  - `public $timestamps`
  - `protected $fillable`
  - `protected $hidden` de ser necesario, por ejemplo el campo `password`
- Se deben agregar todas las relaciones.

### 3. Controladores

- Se debe crear **una carpeta para cada módulo**, esto basado en la naturaleza del módulo, por ejemplo: Si se trata de temas de autenticación o autorización, entonces `Auth`, si es sobre la gestión de inventario `Inventario`. Usar el formato **CamelCase** para el nombre de la carpeta.
- Los nombres de los controladores deben usar la convención **CamelCase** y terminar con el sufijo **Controller**.

### 4. Rutas de la API

- Las rutas se dividen en dos tipos:

  - **Rutas públicas:** Se definen en el archivo `/routes/public.php`.
  - **Rutas protegidas:** Se configuran en el archivo `/routes/protected.php`.

- Convenciones para las rutas:
  - En la medida de lo posible, adherirse a la convención REST, evitando incluir verbos en los nombres de las rutas. Las acciones deben definirse adecuadamente mediante los métodos HTTP (POST, GET, PUT, DELETE). Por ejemplo:
    - POST usuario - Crear un nuevo usuario
    - GET usuarios - Obtener todos los usuarios
    - GET usuario/{id_usuario} - Obtener un usuario a través de su ID
    - PUT usuario/{id_usuario} - Actualizar los datos de un usuario según su ID
    - DELETE usuario/{id_usuario} - Eliminar un usuario por su ID
  - Utilizar **kebab-case** solo cuando sea estrictamente necesario. Por ejemplo:
    - PUT inventario/{id_inventario}/balance-existencia
  - De preferencia no usar más de tres divisiones en la ruta

- Las rutas deben ser nombradas ya que al usarse en la ejecución de las pruebas unitarias será necesario este nombre, por ejemplo, la ruta `/auth/login` posee el nombre `public.auth.login`
  - `public.` es el nombre del grupo configurado en `bootstrap\app.php`
  - `auth.` es el grupo donde se importan en `routes\public.php`
  - `login` es el nombre de la ruta específica existente en `routes\public\auth.php`

### 5. Almacenamiento

- Los recursos estáticos, como imágenes, deben almacenarse en la carpeta `public`. Según el tipo de recurso, utilizar las subcarpetas correspondientes:
  - `/documents`: Para documentos.
  - `/images`: Para imágenes.
- Todos los archivos que derivan de interacción con la aplicación deben almacenarse en `/storage/app`, tomando en cuenta la naturaleza de los archivos se debe crear una subcarpeta.

### 6. Código

- **Clases**

  - **PascalCase**: Cada palabra inicia con mayúscula sin guiones ni subrayados.

    ```java
    class UsuarioAdministrador { … }
    ```

- **Métodos y funciones**

  - **camelCase**: La primera palabra en minúscula, las siguientes con inicial en mayúscula.

    ```js
    function calcularTotalVenta() { … }
    ```

- **Variables**

  - **camelCase**

    ```js
    let edadUsuario = 30;
    ```

- **Constantes**

  - **UPPER\_SNAKE\_CASE**: Todo en mayúscula, palabras separadas por “\_”.

    ```c
    const int MAX_INTENTOS = 3;
    ```

- **Indentación y segmentación de código**

  - **4 espacios** en lugar de tabuladores.
  - **Líneas en blanco** para separar bloques lógicos (grupo de declaraciones, sección de validaciones, retorno).
  - **Una responsabilidad por función**, por ejemplo, una función no debería tener dos bucles

- **Iteraciones**

  - Uso de índices significativos:

    ```js
    for (let i = 0; i < usuarios.length; i++) { … }
    ```

- **Comentarios**

  - **Comentarios en línea** (`//`) solo para aclarar lógica compleja, no para explicar “qué hace” el código que ya es legible.
  - **Comentarios en bloque** (`/* … */`) para documentar la interfaz de clases y métodos.

- **Longitud de línea**

  - Máximo **120 caracteres**, para mantener legibilidad en pantallas pequeñas y revisiones de código.

- **Organización de imports/dependencias**

  - Agrupar y ordenar alfabéticamente, separando librerías externas de módulos internos.

- **Manejo de errores y validaciones**

  - **Early return**: validar primero y salir pronto de la función si hay error.

    ```js
    if (!usuario) return null;
    procesar(usuario);
    ```

- **Otras recomendaciones**

  - Evitar **“magic numbers”**: definirlos como constantes con nombre descriptivo.
  - Aplicar **Single Responsibility Principle**: cada clase o módulo debe tener una sola responsabilidad.

## Nomenclaturas

- **Tablas:** Formato `snake_case`, nombres en singular, prefijo de módulo, llave primaria como `id_<nombre_tabla>`.
- **Modelos:** Carpeta por módulo en `CamelCase`, nombre en `CamelCase`, configuración de propiedades y relaciones, uso de `SoftDeletes` para tablas transaccionales.
- **Controladores:** Carpeta por módulo en `CamelCase`, nombre en `CamelCase` con sufijo `Controller`.
- **Rutas:** Separación en públicas (`/routes/public.php`) y protegidas (`/routes/protected.php`), convención REST, evitar verbos en rutas, usar `kebab-case` para rutas de más de una palabra, máximo tres divisiones por ruta.
- **Almacenamiento:** Recursos estáticos en `public` con subcarpetas (`documents`, `images`), archivos de la aplicación en `/storage/app` con subcarpetas según tipo.

# Testing
## comandos a ejecutar
> 💡 Si estás usando Docker, ejecuta estos comandos:
```shell
# Si esta usando docker ejecuta los siguientes comandos
docker-compose exec app php artisan key:generate --env=testing 
docker-compose exec app php artisan  jwt:secret --env=testing
docker-compose exec app php artisan test

```
> 💡 Si no usas docker, ejecuta estos comandos:
```shell
# Si no usas docker, ejecuta los siguientes comandos
php artisan key:generate --env=testing 
php artisan  jwt:secret --env=testing
php artisan test

```
 

## Nomenclatura y lineamientos

Este documento establece los lineamientos y estándares para escribir tests en este proyecto Laravel API. Todos los desarrolladores deben seguir estas reglas para mantener consistencia y calidad en el código de testing.

## Estructura de Archivos

### Organización de Directorios

```
tests/
├── Feature/           # Tests de integración (HTTP, APIs)
│   └── Public/
│       └── Auth/
├── Unit/             # Tests unitarios (lógica de negocio)
│   └── Public/
│       └── Auth/
├── TestCase.php      # Clase base para todos los tests
└── CreatesApplication.php
```

### Convenciones de Nomenclatura

- **Archivos de test**: Terminan en `Test.php` (ej: `AuthTest.php`)
- **Métodos de test**: Prefijo `test_` seguido de descripción en snake_case
- **Clases de test**: Terminan en `Test` y están en el namespace correspondiente

**Ejemplos:**
```php
// ✅ Correcto
public function test_login_with_valid_credentials_returns_token(): void

// ❌ Incorrecto
public function testLogin(): void
public function loginTest(): void
```

## Estructura de Tests

### Patrón AAA (Arrange-Act-Assert)

Todos los tests deben seguir el patrón **Arrange-Act-Assert** con comentarios explícitos:

```php
public function test_login_with_valid_credentials_returns_token(): void
{
    // Arrange: Preparar datos y estado inicial
    AuthUsuario::create([
        'username' => 'usuario_test',
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $credentials = [
        'email' => 'test@example.com',
        'password' => 'password123',
    ];

    // Act: Ejecutar la acción que se está probando
    $response = $this->postJson(route('login'), $credentials);

    // Assert: Verificar el resultado esperado
    $response->assertStatus(200)
        ->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
}
```

### Documentación con PHPDoc

Cada método de test debe incluir un comentario descriptivo:

```php
/**
 * Test successful login with valid credentials
 */
public function test_login_with_valid_credentials_returns_token(): void
{
    // Implementación del test
}

/**
 * Test login with invalid email returns 422 validation error
 */
public function test_login_with_invalid_email_returns_validation_error(): void
{
    // Implementación del test
}
```

## Tipos de Tests

### Tests Feature (Integración)

- **Propósito**: Probar endpoints completos, flujos de usuario
- **Ubicación**: `tests/Feature/`
- **Características**:
  - Usan `RefreshDatabase`
  - Prueban respuestas HTTP completas
  - Verifican estructura JSON

```php
<?php

namespace Tests\Feature\Public\Auth;

use App\Models\Auth\AuthUsuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials_returns_token(): void
    {
        // Arrange
        AuthUsuario::create([
            'username' => 'usuario_test',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Act
        $response = $this->postJson(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type', 
                'expires_in',
            ]);
    }
}
```

### Tests Unit (Unitarios)

- **Propósito**: Probar funcionalidades específicas aisladas
- **Ubicación**: `tests/Unit/`
- **Características**:
  - Más simples y directos
  - Prueban lógica específica

```php
<?php

namespace Tests\Unit\Public\Auth;

use App\Models\Auth\AuthUsuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials_returns_token(): void
    {
        // Arrange
        AuthUsuario::create([
            'username' => 'usuario_test',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Act
        $response = $this->postJson(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
            ]);
    }
}
```

## Casos de Test Obligatorios

### Para Endpoints de API

Cada endpoint debe tener tests para los siguientes escenarios:

1. **Caso exitoso** - Happy path
2. **Validación de campos requeridos** - Campos faltantes
3. **Validación de formato** - Formatos inválidos
4. **Autenticación/Autorización** - Credenciales inválidas
5. **Casos límite** - Datos vacíos, nulos, etc.

**Ejemplo completo:**

```php
// ✅ Caso exitoso
public function test_login_with_valid_credentials_returns_token(): void

// ✅ Validación - email faltante
public function test_login_without_email_returns_validation_error(): void

// ✅ Validación - password faltante  
public function test_login_without_password_returns_validation_error(): void

// ✅ Validación - formato de email inválido
public function test_login_with_invalid_email_format_returns_validation_error(): void

// ✅ Autenticación - email inexistente
public function test_login_with_invalid_email_returns_validation_error(): void

// ✅ Autenticación - password incorrecta
public function test_login_with_wrong_password_returns_unauthorized(): void

// ✅ Casos límite - credenciales vacías
public function test_login_with_empty_credentials_returns_validation_errors(): void
```

## Assertions y Verificaciones

### Verificaciones de Respuesta HTTP

```php
// Estado HTTP
$response->assertStatus(200);
$response->assertStatus(422);
$response->assertStatus(401);

// Estructura JSON
$response->assertJsonStructure([
    'access_token',
    'token_type',
    'expires_in',
]);

// Contenido específico
$response->assertJson([
    'token_type' => 'bearer',
]);

// Errores de validación
$response->assertJsonValidationErrors(['email']);
$response->assertJsonValidationErrors(['email', 'password']);
```

### Verificaciones de Datos

```php
// Verificar tipos de datos
$this->assertIsString($response->json('access_token'));
$this->assertIsInt($response->json('expires_in'));
$this->assertIsNumeric($responseData['expires_in']);

// Verificar valores no vacíos
$this->assertNotEmpty($response->json('access_token'));

// Verificar rangos
$this->assertGreaterThan(0, $response->json('expires_in'));

// Verificar patrones (ej: JWT)
$this->assertMatchesRegularExpression(
    '/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', 
    $responseData['access_token']
);
```

## Configuración de Base de Datos

### RefreshDatabase

Usar `RefreshDatabase` en tests que modifiquen la base de datos:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    
    // Tests...
}
```

### Datos de Prueba

- **Usar datos descriptivos** que indiquen su propósito
- **Reutilizar datos similares** en tests relacionados
- **Hash passwords** correctamente

```php
// ✅ Datos descriptivos
AuthUsuario::create([
    'username' => 'usuario_test',
    'email' => 'test@example.com',
    'password' => Hash::make('password123'),
]);

// ✅ Para tests de administrador
AuthUsuario::create([
    'username' => 'admin_user',
    'email' => 'admin@example.com',
    'password' => Hash::make('password123'),
]);
```

## Checklist para Revisión de Tests

Antes de hacer commit, verificar:

- [ ] Tests siguen nomenclatura `test_description_in_snake_case`
- [ ] Incluyen documentación PHPDoc
- [ ] Siguen patrón AAA con comentarios
- [ ] Verifican casos exitosos y de error
- [ ] Incluyen assertions apropiadas
- [ ] Usan `RefreshDatabase` cuando sea necesario
- [ ] Datos de prueba son descriptivos
- [ ] Tests pasan correctamente

## Ejecución de pruebas en paralelo (Solo ambiente DEV)

> 💡 Para usar parallel debes tener activa la extensión `xdebug`, esto permite que los test se ejecuten paralelamente en lugar de hacerlo de forma secuencial
 
```shell
php artisan test --parallel
```