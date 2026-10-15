# 📋 HackAccess API — Documentación Completa del Proyecto

> **API REST** para la gestión de participantes de un hackathon de accesibilidad.  
> Desarrollada con **Laravel 10** + **Laravel Sanctum** para autenticación basada en tokens.  
> Se conecta con un frontend Angular que corre en `http://localhost:4200`.

---

## 📑 Índice

1. [Visión General](#1-visión-general)
2. [Stack Tecnológico](#2-stack-tecnológico)
3. [Estructura del Proyecto](#3-estructura-del-proyecto)
4. [Configuración y Entorno](#4-configuración-y-entorno)
5. [Base de Datos — Migraciones](#5-base-de-datos--migraciones)
6. [Modelos (Eloquent)](#6-modelos-eloquent)
7. [Controladores](#7-controladores)
8. [API Resources](#8-api-resources)
9. [Rutas de la API](#9-rutas-de-la-api)
10. [Middleware y Kernel HTTP](#10-middleware-y-kernel-http)
11. [Configuración CORS](#11-configuración-cors)
12. [Configuración de Sanctum](#12-configuración-de-sanctum)
13. [Seeders (Datos de prueba)](#13-seeders-datos-de-prueba)
14. [Tabla de Endpoints](#14-tabla-de-endpoints)
15. [Puesta en Marcha](#15-puesta-en-marcha)

---

## 1. Visión General

**HackAccess** es una plataforma para organizar un hackathon centrado en la accesibilidad. La API permite:

- **Registrar** y **autenticar** usuarios (organizadores/administradores).
- **Gestionar participantes** del hackathon (CRUD completo).
- **Proteger** las rutas mediante tokens de autenticación (Sanctum).
- **Restringir** operaciones sensibles (editar/eliminar participantes) solo al usuario administrador (`admin@hackaccess.dev`).

La API está diseñada para ser consumida desde un frontend SPA (Angular) que se comunica vía peticiones HTTP con cabeceras `Authorization: Bearer <token>`.

---

## 2. Stack Tecnológico

| Tecnología | Versión | Función |
|---|---|---|
| **PHP** | ≥ 8.1 | Lenguaje del backend |
| **Laravel** | 10.x | Framework PHP |
| **Laravel Sanctum** | 3.x | Autenticación por tokens API |
| **MySQL** | 8.x | Base de datos relacional |
| **Composer** | 2.x | Gestor de dependencias PHP |
| **Angular** (frontend) | — | SPA que consume esta API |

---

## 3. Estructura del Proyecto

```
hackaccess-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php              ← Controlador base
│   │   │   ├── AuthController.php          ← Login, register, logout, me
│   │   │   └── Api/
│   │   │       └── ParticipantController.php ← CRUD de participantes
│   │   ├── Kernel.php                      ← Middleware global y grupos
│   │   ├── Middleware/                     ← Middleware de Laravel
│   │   └── Resources/
│   │       └── ParticipantResource.php     ← Transformador JSON
│   ├── Models/
│   │   ├── User.php                        ← Modelo de usuario
│   │   └── Participant.php                 ← Modelo de participante
│   └── Providers/
├── config/
│   ├── auth.php                            ← Configuración de autenticación
│   ├── cors.php                            ← Configuración CORS
│   ├── sanctum.php                         ← Configuración de Sanctum
│   └── ...
├── database/
│   ├── migrations/
│   │   ├── 2014_10_12_000000_create_users_table.php
│   │   ├── 2014_10_12_100000_create_password_reset_tokens_table.php
│   │   ├── 2019_12_14_000001_create_personal_access_tokens_table.php
│   │   └── 2026_05_18_135659_create_participants_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── ParticipantSeeder.php
├── routes/
│   ├── api.php                             ← Rutas de la API REST
│   └── web.php                             ← Rutas web (no usadas)
├── .env                                    ← Variables de entorno
├── composer.json                           ← Dependencias PHP
└── ...
```

---

## 4. Configuración y Entorno

### Fichero: `.env`

Este fichero contiene las variables de entorno que configuran toda la aplicación. Las más relevantes son:

```env
APP_NAME=HackAcces
APP_ENV=local
APP_DEBUG=true
APP_URL=http://hackacces-api.test

# Base de datos MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hackaccess-api
DB_USERNAME=root
DB_PASSWORD=

# Sanctum — Dominio del frontend (SPA stateful)
SANCTUM_STATEFUL_DOMAINS=localhost:4200

# URL del frontend
FRONTEND_URL=http://localhost:4200
```

**¿Por qué es importante?**  
Aquí se define la conexión a la base de datos, el dominio del frontend para CORS/Sanctum, y las credenciales del entorno. Sin este fichero correctamente configurado, la API no arranca.

---

### Fichero: `composer.json`

Define las dependencias PHP del proyecto:

```json
{
  "require": {
    "php": "^8.1",
    "guzzlehttp/guzzle": "^7.2",
    "laravel/framework": "^10.10",
    "laravel/sanctum": "^3.3",
    "laravel/tinker": "^2.8"
  }
}
```

**¿Por qué es importante?**  
- `laravel/framework` → el core de Laravel.
- `laravel/sanctum` → proporciona el sistema de autenticación por tokens que usamos en toda la API.
- `laravel/tinker` → consola interactiva para depuración.

---

## 5. Base de Datos — Migraciones

Las migraciones definen la estructura de las tablas en la base de datos.

---

### Fichero: `database/migrations/2014_10_12_000000_create_users_table.php`

Crea la tabla `users` para almacenar los usuarios que se registran e inician sesión.

| Columna | Tipo | Descripción |
|---|---|---|
| `id` | `bigint` (autoincrement) | Clave primaria |
| `name` | `string` | Nombre del usuario |
| `email` | `string` (unique) | Email del usuario (login) |
| `email_verified_at` | `timestamp` (nullable) | Fecha de verificación del email |
| `password` | `string` | Contraseña hasheada |
| `remember_token` | `string` | Token de "recordar sesión" |
| `created_at` / `updated_at` | `timestamps` | Fechas de creación y actualización |

**¿Por qué es importante?**  
Es la tabla de autenticación. Sin ella, no se pueden registrar ni logear usuarios.

---

### Fichero: `database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php`

Crea la tabla `personal_access_tokens` que usa **Sanctum** para almacenar los tokens de autenticación.

| Columna | Tipo | Descripción |
|---|---|---|
| `id` | `bigint` | Clave primaria |
| `tokenable_type` / `tokenable_id` | `morphs` | Relación polimórfica con `users` |
| `name` | `string` | Nombre del token (ej: `hackaccess-token`) |
| `token` | `string(64)` (unique) | Hash SHA-256 del token |
| `abilities` | `text` (nullable) | Permisos del token |
| `last_used_at` | `timestamp` (nullable) | Última vez que se usó |
| `expires_at` | `timestamp` (nullable) | Fecha de expiración |
| `created_at` / `updated_at` | `timestamps` | Timestamps |

**¿Por qué es importante?**  
Esta tabla es el corazón de la autenticación con Sanctum. Cada vez que un usuario hace login, se genera un registro aquí. El token generado se envía al frontend y se adjunta como `Bearer` en cada petición autenticada.

---

### Fichero: `database/migrations/2026_05_18_135659_create_participants_table.php`

Crea la tabla `participants` para almacenar los participantes del hackathon.

| Columna | Tipo | Descripción |
|---|---|---|
| `id` | `bigint` (autoincrement) | Clave primaria |
| `nom` | `string` | Nombre del participante |
| `cognom` | `string` | Apellido del participante |
| `email` | `string` (unique) | Email del participante |
| `tipus_entrada` | `enum('Presencial', 'Online', 'Mentor')` | Tipo de asistencia |
| `necessitats_especials` | `boolean` (default: false) | Si tiene necesidades especiales |
| `created_at` / `updated_at` | `timestamps` | Timestamps |

**¿Por qué es importante?**  
Es la tabla central del negocio. Contiene toda la información de los participantes inscritos al hackathon, con su tipo de asistencia y si tienen necesidades especiales de accesibilidad.

---

## 6. Modelos (Eloquent)

Los modelos representan las tablas de la base de datos en PHP y permiten interactuar con ellas de forma orientada a objetos.

---

### Fichero: `app/Models/User.php`

```php
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];
    protected $hidden   = ['password', 'remember_token'];
    protected $casts    = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];
}
```

**¿Qué hace y por qué es importante?**
- Usa el trait `HasApiTokens` de Sanctum → esto le da al modelo la capacidad de **crear y gestionar tokens** (`$user->createToken(...)`).
- `HasFactory` → permite usar factories para testing.
- `Notifiable` → permite enviar notificaciones.
- `$fillable` → campos que se pueden rellenar masivamente (para `register`).
- `$hidden` → campos que **nunca** se serializan en JSON (protege la contraseña).
- `$casts` → castea automáticamente `password` a hash y `email_verified_at` a `Carbon`.

---

### Fichero: `app/Models/Participant.php`

```php
class Participant extends Model
{
    protected $fillable = [
        'nom', 'cognom', 'email', 'tipus_entrada', 'necessitats_especials',
    ];

    protected $casts = [
        'necessitats_especials' => 'boolean',
    ];
}
```

**¿Qué hace y por qué es importante?**
- Define los campos que se pueden crear/actualizar masivamente con `$fillable`.
- Castea `necessitats_especials` a `boolean` para que siempre devuelva `true`/`false` en lugar de `0`/`1`.
- Es el modelo principal del dominio: representa un participante del hackathon.

---

## 7. Controladores

Los controladores manejan las peticiones HTTP, validan datos, ejecutan la lógica de negocio y devuelven respuestas JSON.

---

### Fichero: `app/Http/Controllers/Controller.php`

```php
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
```

**¿Qué hace?**  
Es el controlador base del que heredan todos los demás. Incorpora los traits de autorización y validación de Laravel.

---

### Fichero: `app/Http/Controllers/AuthController.php`

Gestiona toda la **autenticación** de la API. Contiene 4 métodos:

#### `login(Request $request)`
- **Ruta:** `POST /api/login`
- **Pública** (no requiere token).
- Valida `email` y `password`.
- Intenta autenticar con `Auth::attempt()`.
- Si las credenciales son correctas: genera un token Sanctum y lo devuelve junto con los datos del usuario.
- Si son incorrectas: devuelve `401` con mensaje `"Credencials incorrectes"`.

```json
// Respuesta exitosa:
{
  "token": "1|abc123...",
  "user": { "id": 1, "name": "Admin", "email": "admin@hackaccess.dev" }
}
```

#### `register(Request $request)`
- **Ruta:** `POST /api/register`
- **Pública** (no requiere token).
- Valida `name`, `email` (único) y `password` (mín. 8 caracteres).
- Crea el usuario con la contraseña hasheada (`bcrypt`).
- Genera un token automáticamente y lo devuelve.

#### `logout(Request $request)`
- **Ruta:** `POST /api/logout`
- **Protegida** (requiere token `auth:sanctum`).
- Elimina el token actual del usuario de la base de datos.
- Devuelve `"Sessió tancada"`.

#### `me(Request $request)`
- **Ruta:** `GET /api/user`
- **Protegida** (requiere token `auth:sanctum`).
- Devuelve los datos del usuario autenticado en JSON.

**¿Por qué es importante?**  
Es el controlador que maneja todo el flujo de autenticación. Sin él, no habría manera de obtener un token para acceder a las rutas protegidas.

---

### Fichero: `app/Http/Controllers/Api/ParticipantController.php`

Gestiona el **CRUD completo** de participantes. Se ubica en el namespace `Api` para separarlo de los controladores generales.

#### Constante `ADMIN_EMAIL`
```php
private const ADMIN_EMAIL = 'admin@hackaccess.dev';
```
Define qué email tiene privilegios de administrador. Solo este usuario puede **editar** y **eliminar** participantes.

#### Métodos privados auxiliares

- **`isAdmin(Request $request): bool`** → Comprueba si el usuario autenticado es el admin comparando su email.
- **`forbiddenResponse()`** → Devuelve una respuesta `403` con mensaje `"No tens permisos per realitzar aquesta acció."`.

#### `index()`
- **Ruta:** `GET /api/participants`
- **Protegida** (requiere token).
- Devuelve **todos** los participantes ordenados por fecha de creación (más recientes primero).
- Usa `ParticipantResource::collection()` para formatear la respuesta.

#### `show(Participant $participant)`
- **Ruta:** `GET /api/participants/{id}`
- **Protegida** (requiere token).
- Devuelve un participante por su ID (usando **Route Model Binding** de Laravel).
- Si el ID no existe, Laravel devuelve automáticamente `404`.

#### `store(Request $request)`
- **Ruta:** `POST /api/participants`
- **Protegida** (requiere token).
- Valida los campos:
  - `nom` → requerido, string, 2-100 caracteres.
  - `cognom` → requerido, string, 2-100 caracteres.
  - `email` → requerido, email válido, **único** en la tabla `participants`.
  - `tipus_entrada` → requerido, debe ser `Presencial`, `Online` o `Mentor`.
  - `necessitats_especials` → opcional, booleano.
- Crea el participante y devuelve `201 Created`.

#### `update(Request $request, Participant $participant)`
- **Ruta:** `PUT /api/participants/{id}`
- **Protegida** (requiere token).
- **Solo admin** → si el usuario no es `admin@hackaccess.dev`, devuelve `403`.
- Validación parcial con `sometimes|required` → solo valida los campos enviados.
- Excluye el propio email en la validación de unicidad (`unique:participants,email,{id}`).

#### `destroy(Request $request, Participant $participant)`
- **Ruta:** `DELETE /api/participants/{id}`
- **Protegida** (requiere token).
- **Solo admin** → si el usuario no es admin, devuelve `403`.
- Elimina el participante y devuelve `200` con mensaje de confirmación.

**¿Por qué es importante?**  
Es el controlador central de la aplicación. Implementa toda la lógica de negocio para gestionar participantes, incluyendo control de acceso basado en roles (admin vs. usuario normal).

---

## 8. API Resources

### Fichero: `app/Http/Resources/ParticipantResource.php`

```php
class ParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'nom'                   => $this->nom,
            'cognom'                => $this->cognom,
            'nom_complet'           => $this->nom . ' ' . $this->cognom,
            'email'                 => $this->email,
            'tipus_entrada'         => $this->tipus_entrada,
            'necessitats_especials' => $this->necessitats_especials,
            'registrat_el'          => $this->created_at->format('d/m/Y H:i'),
        ];
    }
}
```

**¿Qué hace y por qué es importante?**
- Actúa como una **capa de transformación** entre el modelo Eloquent y la respuesta JSON.
- Añade el campo calculado `nom_complet` (nombre + apellido) que no existe en la base de datos.
- Formatea la fecha `created_at` al formato europeo `d/m/Y H:i` con el alias `registrat_el`.
- Controla exactamente qué campos se envían al frontend, evitando exponer datos internos como `updated_at`.

---

## 9. Rutas de la API

### Fichero: `routes/api.php`

```php
// ─── RUTAS PÚBLICAS ───
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// ─── RUTAS PROTEGIDAS (requieren token Sanctum) ───
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user',    [AuthController::class, 'me']);

    Route::get('/participants',                 [ParticipantController::class, 'index']);
    Route::post('/participants',                [ParticipantController::class, 'store']);
    Route::get('/participants/{participant}',   [ParticipantController::class, 'show']);
    Route::put('/participants/{participant}',   [ParticipantController::class, 'update']);
    Route::delete('/participants/{participant}',[ParticipantController::class, 'destroy']);
});
```

**¿Por qué es importante?**  
Define todos los endpoints de la API. Las rutas están prefijadas automáticamente con `/api/` por Laravel. Se dividen en:
- **Públicas:** login y register (accesibles sin token).
- **Protegidas:** todo lo demás requiere un token `Bearer` válido en la cabecera `Authorization`.

---

## 10. Middleware y Kernel HTTP

### Fichero: `app/Http/Kernel.php`

Configura los **middleware** que se ejecutan en cada petición:

#### Middleware global (se ejecuta en TODAS las peticiones):
- `TrustProxies` → Gestiona proxies inversos.
- `HandleCors` → Aplica la configuración CORS.
- `PreventRequestsDuringMaintenance` → Bloquea peticiones si la app está en mantenimiento.
- `ValidatePostSize` → Valida el tamaño de las peticiones POST.
- `TrimStrings` → Elimina espacios en blanco de los strings.
- `ConvertEmptyStringsToNull` → Convierte strings vacíos en `null`.

#### Grupo `api` (solo rutas `/api/*`):
```php
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

- `EnsureFrontendRequestsAreStateful` → **Clave para Sanctum**: permite que las peticiones del frontend SPA sean stateful (con cookies de sesión).
- `ThrottleRequests` → Limita el número de peticiones por minuto (rate limiting).
- `SubstituteBindings` → Permite el **Route Model Binding** (que `{participant}` se resuelva automáticamente al modelo).

**¿Por qué es importante?**  
Sin `EnsureFrontendRequestsAreStateful` en el grupo `api`, Sanctum no puede autenticar las peticiones SPA. Sin `SubstituteBindings`, las rutas `{participant}` no resolverían automáticamente el modelo.

---

## 11. Configuración CORS

### Fichero: `config/cors.php`

```php
return [
    'paths'            => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_origins'  => ['http://localhost:4200'],
    'allowed_methods'  => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'allowed_headers'  => ['Content-Type', 'Authorization', 'Accept', 'X-Requested-With'],
    'supports_credentials' => false,
];
```

**¿Qué hace y por qué es importante?**
- `paths` → CORS solo se aplica a las rutas `api/*` y a la cookie CSRF de Sanctum.
- `allowed_origins` → **Solo permite peticiones desde `http://localhost:4200`** (el frontend Angular).
- `allowed_methods` → Permite todos los métodos HTTP necesarios para el CRUD.
- `allowed_headers` → Permite las cabeceras necesarias, incluyendo `Authorization` (para el token Bearer).

Sin esta configuración, el navegador bloquearía todas las peticiones del frontend a la API por política de Same-Origin.

---

## 12. Configuración de Sanctum

### Fichero: `config/sanctum.php`

```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1'
)),
'guard'      => ['web'],
'expiration' => null,   // Los tokens no expiran
```

**¿Qué hace y por qué es importante?**
- `stateful` → Define los dominios que usan autenticación stateful (cookie-based). El `.env` añade `localhost:4200`.
- `guard` → Usa el guard `web` (sesión + Eloquent) para autenticar.
- `expiration` → `null` significa que los tokens no tienen fecha de expiración (se mantienen activos hasta que el usuario hace logout).

---

## 13. Seeders (Datos de prueba)

### Fichero: `database/seeders/ParticipantSeeder.php`

Inserta **5 participantes de prueba** en la base de datos:

| Nom | Cognom | Email | Tipus Entrada | Necessitats Especials |
|---|---|---|---|---|
| Marta | Puig | marta@hackaccess.dev | Presencial | ❌ |
| Joan | Ferrer | joan@hackaccess.dev | Online | ❌ |
| Laia | Vidal | laia@hackaccess.dev | Presencial | ✅ |
| Arnau | Costa | arnau@hackaccess.dev | Mentor | ❌ |
| Nuria | Soler | nuria@hackaccess.dev | Online | ✅ |

**¿Por qué es importante?**  
Permite tener datos iniciales para probar la API sin necesidad de crear participantes manualmente.

---

### Fichero: `database/seeders/DatabaseSeeder.php`

```php
public function run(): void
{
    $this->call(ParticipantSeeder::class);
}
```

Es el seeder principal que Laravel ejecuta con `php artisan db:seed`. Llama al `ParticipantSeeder`.

---

## 14. Tabla de Endpoints

### Endpoints Públicos (sin autenticación)

| Método | Endpoint | Acción | Descripción |
|---|---|---|---|
| `POST` | `/api/login` | `AuthController@login` | Iniciar sesión y obtener token |
| `POST` | `/api/register` | `AuthController@register` | Registrar nuevo usuario |

### Endpoints Protegidos (requieren `Authorization: Bearer <token>`)

| Método | Endpoint | Acción | Descripción | Restricción |
|---|---|---|---|---|
| `POST` | `/api/logout` | `AuthController@logout` | Cerrar sesión (invalidar token) | Usuario autenticado |
| `GET` | `/api/user` | `AuthController@me` | Obtener datos del usuario actual | Usuario autenticado |
| `GET` | `/api/participants` | `ParticipantController@index` | Listar todos los participantes | Usuario autenticado |
| `POST` | `/api/participants` | `ParticipantController@store` | Crear un participante | Usuario autenticado |
| `GET` | `/api/participants/{id}` | `ParticipantController@show` | Ver un participante | Usuario autenticado |
| `PUT` | `/api/participants/{id}` | `ParticipantController@update` | Editar un participante | **Solo admin** |
| `DELETE` | `/api/participants/{id}` | `ParticipantController@destroy` | Eliminar un participante | **Solo admin** |

---

## 15. Puesta en Marcha

### Requisitos previos
- PHP ≥ 8.1
- Composer
- MySQL con una base de datos llamada `hackaccess-api`

### Pasos

```bash
# 1. Instalar dependencias
composer install

# 2. Copiar fichero de entorno (si no existe)
cp .env.example .env

# 3. Generar clave de aplicación
php artisan key:generate

# 4. Configurar la base de datos en .env
#    DB_DATABASE=hackaccess-api
#    DB_USERNAME=root
#    DB_PASSWORD=

# 5. Ejecutar migraciones
php artisan migrate

# 6. Ejecutar seeders (datos de prueba)
php artisan db:seed

# 7. Arrancar el servidor
php artisan serve
```

La API estará disponible en `http://127.0.0.1:8000/api/`.

---

## 📌 Resumen de Ficheros Modificados/Creados

| Fichero | Tipo | Descripción |
|---|---|---|
| `.env` | Configuración | Variables de entorno (BD, Sanctum, CORS) |
| `composer.json` | Dependencias | Dependencias PHP (Sanctum incluido) |
| `routes/api.php` | Rutas | Define todos los endpoints de la API |
| `app/Http/Kernel.php` | Middleware | Configura Sanctum en el grupo `api` |
| `config/cors.php` | Configuración | Permite peticiones del frontend Angular |
| `config/sanctum.php` | Configuración | Configura la autenticación con tokens |
| `config/auth.php` | Configuración | Define guards y providers de autenticación |
| `app/Models/User.php` | Modelo | Modelo de usuario con `HasApiTokens` (Sanctum) |
| `app/Models/Participant.php` | Modelo | Modelo de participante del hackathon |
| `app/Http/Controllers/Controller.php` | Controlador | Controlador base de Laravel |
| `app/Http/Controllers/AuthController.php` | Controlador | Login, register, logout y me |
| `app/Http/Controllers/Api/ParticipantController.php` | Controlador | CRUD de participantes con control admin |
| `app/Http/Resources/ParticipantResource.php` | Resource | Transforma el modelo Participant a JSON |
| `database/migrations/*_create_users_table.php` | Migración | Tabla `users` |
| `database/migrations/*_create_personal_access_tokens_table.php` | Migración | Tabla de tokens Sanctum |
| `database/migrations/*_create_participants_table.php` | Migración | Tabla `participants` |
| `database/seeders/DatabaseSeeder.php` | Seeder | Seeder principal |
| `database/seeders/ParticipantSeeder.php` | Seeder | 5 participantes de prueba |
