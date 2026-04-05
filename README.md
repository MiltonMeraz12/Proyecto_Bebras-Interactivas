# 🍯 Bebras MX - Laboratorio Interactivo

> Plataforma educativa para competencias de pensamiento computacional basada en Bebras (Informatics Challenges for All).

## 📋 Descripción del Proyecto

**Bebras MX** es una aplicación web interactiva diseñada para que estudiantes resuelvan desafíos de programación y pensamiento computacional. La plataforma cuenta con:

- **27 preguntas/desafíos** de la competencia Bebras
- **7 tipos de interacción** diferentes (selección simple, múltiple, ordenamiento, emparejamiento, rellenado, etc.)
- **Sistema de roles** (administrador y usuario)
- **Seguimiento de progreso** de usuarios
- **Autenticación segura** con 2FA
- **Dashboard administrativo** para monitoreo

## 🛠️ Stack Tecnológico

| Tecnología       | Versión | Uso                    |
| ---------------- | ------- | ---------------------- |
| **PHP**          | 8.2+    | Backend                |
| **Laravel**      | 12.0    | Framework principal    |
| **MySQL**        | 8.0     | Base de datos          |
| **Node.js**      | 18+     | Build tools            |
| **Vite**         | 7.0+    | Asset bundler          |
| **Tailwind CSS** | 4.0+    | Utilidad CSS           |
| **Livewire**     | 3.x     | Componentes dinámicos  |
| **Volt**         | 1.7+    | Componentes full-stack |
| **Docker**       | Latest  | Containerización       |
| **Laravel Sail** | 1.41+   | Orquestación Docker    |

## 🚀 Inicio Rápido

### Requisitos Previos

- Docker y Docker Compose instalados
- Git
- Editor de código (VSCode)

### 1. Clonar el repositorio

```bash
git clone <tu-repositorio>
cd bebras-mx
```

### 2. Configurar variables de entorno

```bash
cp .env.example .env
```

Editar `.env` si es necesario (por defecto usa SQLite para desarrollo):

```env
APP_NAME=Bebras\ Lab
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

# Para usar MySQL en Docker (opcional):
# DB_CONNECTION=mysql
# DB_HOST=mysql
# DB_DATABASE=bebras
# DB_USERNAME=sail
# DB_PASSWORD=password
```

### 3. Iniciar con Laravel Sail

```bash
# Primer inicio (descarga imágenes Docker)
./vendor/bin/sail up -d

# O si prefieres una línea:
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd)":/var/www/html \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

### 4. Generar clave de aplicación e inicializar BD

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

### 5. Instalar dependencias de Node.js y compilar assets

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

### 6. Acceder a la aplicación

- **Aplicación**: [http://localhost](http://localhost)
- **Admin Dashboard**: [http://localhost/admin/dashboard](http://localhost/admin/dashboard)

**Credenciales de ejemplo:**

- Email Admin: `admin@brebas.com` (cuenta admin creada por seeder)
- Email Alumno: `alumno@brebas.com`
- Contraseña: `admin123`- `alumno123`

## 📁 Estructura del Proyecto

```
bebras-mx/
├── app/
│   ├── Actions/              # Actions de Fortify
│   ├── Enums/
│   │   └── TipoInteraccion.php  # Tipos de interacción disponibles
│   ├── Http/
│   │   ├── Controllers/       # Controladores principales
│   │   │   ├── PreguntaController.php     # Lógica de preguntas
│   │   │   └── Admin/DashboardController.php  # Admin panel
│   │   └── Middleware/        # Middleware (admin, etc.)
│   ├── Livewire/              # Componentes Livewire
│   │   └── Actions/
│   └── Models/
│       ├── Pregunta.php           # Modelo de preguntas
│       ├── ProgresoUsuario.php    # Seguimiento de progreso
│       └── User.php               # Modelo de usuarios
│
├── database/
│   ├── migrations/            # Migraciones de BD
│   ├── seeders/               # Seeders (datos iniciales)
│   └── factories/             # Factories para testing
│
├── resources/
│   ├── css/
│   │   └── app.css            # Estilos principales
│   ├── js/
│   │   └── app.js             # JavaScript principal
│   └── views/
│       ├── preguntas/         # Vistas de preguntas
│       │   ├── tipos/         # Vistas por tipo de interacción
│       │   ├── scripts/       # Scripts de validación
│       │   └── show.blade.php # Vista principal
│       ├── admin/             # Vistas administrativas
│       ├── layouts/           # Layouts base
│       ├── livewire/          # Componentes Volt
│       └── welcome.blade.php  # Página principal
│
├── routes/
│   ├── web.php                # Rutas web principales
│   └── console.php            # Comandos Artisan
│
├── tests/                     # Tests (Pest PHP)
│   ├── Feature/
│   └── Unit/
│
├── config/
│   ├── app.php                # Configuración general
│   ├── auth.php               # Autenticación
│   ├── database.php           # Base de datos
│   ├── fortify.php            # Configuración Fortify (2FA)
│   └── ...
│
├── compose.yaml               # Configuración Docker
├── package.json               # Dependencias frontend
├── composer.json              # Dependencias backend
├── vite.config.js             # Configuración Vite
└── phpunit.xml                # Configuración Pest
```

## 🗄️ Base de Datos

### Modelos Principales

#### **User**

- `id` (PK)
- `name`, `email`, `password`
- `role` ('admin' | 'user') - Agregado reciente
- `two_factor_secret`, `two_factor_recovery_codes` (Fortify)
- Relación: `hasMany(ProgresoUsuario)`

#### **Pregunta**

- `id` (PK)
- `numero` (1-27)
- `titulo`, `descripcion`, `pregunta` (contenido)
- `imagen_descripcion`, `imagen_pregunta`, `imagen_respuesta` (URIs)
- `tipo_interaccion` (tipo de pregunta)
- `configuracion` (JSON - parámetros específicos)
- `respuesta_correcta` (JSON - respuesta esperada)
- `explicacion` (retroalimentación)
- `nivel`, `dificultad`, `pais_origen`, `codigo_tarea` (metadata)
- `activa` (boolean) - Agregado reciente
- Relación: `hasMany(ProgresoUsuario)`

#### **ProgresoUsuario**

- `id` (PK)
- `user_id` (FK → User)
- `pregunta_id` (FK → Pregunta)
- `respuesta_usuario` (JSON - respuesta enviada)
- `correcta` (boolean)
- `intentos` (int)
- `tiempo_completado` (timestamp)
- Relaciones: `belongsTo(User)`, `belongsTo(Pregunta)`

### Migraciones

| Archivo                                                       | Descripción                  | Estado |
| ------------------------------------------------------------- | ---------------------------- | ------ |
| `0001_01_01_000000_create_users_table.php`                    | Tabla de usuarios base       | ✅     |
| `0001_01_01_000001_create_cache_table.php`                    | Caché de Laravel             | ✅     |
| `0001_01_01_000002_create_jobs_table.php`                     | Queue jobs                   | ✅     |
| `2025_09_02_075243_add_two_factor_columns_to_users_table.php` | 2FA (Fortify)                | ✅     |
| `2025_11_12_141658_preguntas_table.php`                       | Tabla principal de preguntas | ✅     |
| `2025_11_14_034334_add_role_to_users_table.php`               | Roles de usuario             | ✅     |
| `2025_11_14_034349_add_activa_to_preguntas_table.php`         | Status de preguntas          | ✅     |

## 🎯 Tipos de Interacción

### Implementados (7 tipos)

| #   | Tipo                 | Preguntas | Estado | Descripción                         |
| --- | -------------------- | --------- | ------ | ----------------------------------- |
| 1   | `seleccion_simple`   | 13        | ✅     | Elegir una opción correcta          |
| 2   | `seleccion_multiple` | 2         | ✅     | Elegir múltiples opciones correctas |
| 3   | `ordenar`            | 4         | ✅     | Ordenar elementos (SortableJS)      |
| 4   | `grid_seleccion`     | 2         | ✅     | Seleccionar celdas en grid          |
| 5   | `emparejar`          | 1         | ✅     | Emparejar elementos                 |
| 6   | `rellenar`           | 1         | ✅     | Rellenar espacios en blanco         |
| 7   | `texto_libre`        | 1         | ✅     | Respuesta texto abierta             |

**Total preguntas implementadas: 24/27 (89%)**

### Sin Implementar (3 preguntas)

#### ❌ Pregunta 15 - Panal de Abejas

- **Tipo sugerido**: `colocar_piezas` (nuevo) o adaptar `grid_seleccion`
- **Descripción**: Colocar abejas en un panal hexagonal
- **Configuración**: Grid hexagonal con 19 celdas, 7 abejas
- **Respuesta**: Array de objetos con posiciones
- **Prioridad**: Media

#### ❌ Pregunta 21 - Hexágonos de Colores

- **Tipo sugerido**: Adaptar `rellenar` para estructura piramidal
- **Descripción**: Colorear hexágonos en pirámide
- **Configuración**: Estructura 5 filas, 3 colores
- **Respuesta**: Array de objetos con posiciones y colores
- **Prioridad**: Media

#### ❌ Pregunta 26 - Tejiendo Alfombras

- **Tipo sugerido**: Adaptar `grid_seleccion` para múltiples símbolos
- **Descripción**: Rellenar grid 6x6 con símbolos
- **Configuración**: Grid 6x6, 4 símbolos (Morado, Rojo, Amarillo, Verde)
- **Respuesta**: Array 2D con símbolos
- **Prioridad**: Media

## 📊 Arquitectura de Rutas

### Rutas Públicas

```
GET  /                   → Página de bienvenida
```

### Rutas Autenticadas

```
GET  /dashboard          → Redirige según rol (admin o usuario)
GET  /preguntas          → Listado de preguntas para resolver
GET  /preguntas/{id}     → Vista de pregunta específica
POST /preguntas/{id}/verificar → Validar respuesta

GET  /settings/profile   → Perfil de usuario
GET  /settings/password  → Cambiar contraseña
GET  /settings/two-factor → Configurar 2FA
```

### Rutas Administrativas (solo admin)

```
GET  /admin/dashboard                → Dashboard principal
GET  /admin/alumno/{id}/progreso     → Ver progreso de estudiante
POST /admin/preguntas/{id}/toggle    → Activar/desactivar pregunta
```

## 🔐 Autenticación y Seguridad

### Sistemas Implementados

- **Fortify**: Autenticación completa (registro, login, 2FA)
- **2-Factor Authentication**: SMS/TOTP (configurable)
- **Email Verification**: Verificación de correo necesaria
- **Roles**: Admin y Usuario
- **Middleware**: Protección de rutas (`auth`, `verified`, `admin`)

### Configuración en `config/fortify.php`

- ✅ Registro de usuarios
- ✅ Reset de contraseñas
- ✅ Verificación de email
- ✅ Two-Factor Auth
- ❌ Actualizaciones de perfil (deshabilitado)

## 🐳 Docker y Desarrollo

### Laravel Sail

Sail es la abstracción Docker ligera de Laravel:

```bash
# Iniciar servicios
./vendor/bin/sail up -d

# Iniciar en primer plano (ver logs)
./vendor/bin/sail up

# Detener servicios
./vendor/bin/sail down

# Ver logs
./vendor/bin/sail logs -f

# Ejecutar comandos en el contenedor
./vendor/bin/sail artisan <comando>
./vendor/bin/sail npm <comando>
```

### Servicios Incluidos

- **laravel.test** (PHP 8.4 + Apache): Aplicación principal
- **mysql** (8.0): Base de datos

### Configuración

- Puerto HTTP: `APP_PORT` (default: 80)
- Puerto Vite: `VITE_PORT` (default: 5173)
- Base de datos MySQL: puerto `FORWARD_DB_PORT` (default: 3306)

## 📦 Compilación de Assets

### Desarrollo (watch mode)

```bash
./vendor/bin/sail npm run dev
```

Inicia Vite en modo desarrollo con hot reload.

### Producción

```bash
./vendor/bin/sail npm run build
```

Compila y minifica assets para producción.

### Assets Monitoreados

- `resources/css/app.css` → `public/build/assets/app.css`
- `resources/js/app.js` → `public/build/assets/app.js`
- Componentes Blade/Volt

## 🧪 Testing

El proyecto usa **Pest PHP** para testing:

```bash
# Ejecutar todos los tests
./vendor/bin/sail artisan test

# Con output detallado
./vendor/bin/sail artisan test --verbose

# Filtrar tests
./vendor/bin/sail artisan test --filter NombreDel Test

# Coverage de código
./vendor/bin/sail artisan test --coverage
```

### Ubicación de Tests

- `/tests/Feature/` - Tests de funcionalidad
- `/tests/Unit/` - Tests unitarios
- `/tests/TestCase.php` - Clase base

## 📝 Comandos Útiles

### Artisan

```bash
# Configuración
./vendor/bin/sail artisan key:generate           # Generar APP_KEY
./vendor/bin/sail artisan config:clear           # Limpiar caché de config
./vendor/bin/sail artisan cache:clear            # Limpiar caché general

# Base de Datos
./vendor/bin/sail artisan migrate                # Ejecutar migraciones
./vendor/bin/sail artisan migrate:rollback       # Revertir última migración
./vendor/bin/sail artisan migrate:refresh        # Reset + migraciones
./vendor/bin/sail artisan db:seed                # Ejecutar seeders
./vendor/bin/sail artisan migrate:fresh --seed   # Limpiar + migrar + seed

# Views y Cache
./vendor/bin/sail artisan view:clear             # Limpiar caché de vistas
./vendor/bin/sail artisan route:clear            # Limpiar caché de rutas
./vendor/bin/sail artisan optimize:clear         # Limpiar todo

# Tinker (REPL)
./vendor/bin/sail artisan tinker                 # Consola interactiva

# Pint (Linting)
./vendor/bin/sail pint                           # Verificar estilo
./vendor/bin/sail pint --fix                     # Arreglar estilo
```

### npm

```bash
./vendor/bin/sail npm run dev           # Desarrollo (watch)
./vendor/bin/sail npm run build         # Producción
./vendor/bin/sail npm install           # Instalar dependencias
./vendor/bin/sail npm update            # Actualizar dependencias
```

## 🔧 Configuración en Producción

### variables de entorno a cambiar

Consulta [LARAVEL_CLOUD_SETUP.md](LARAVEL_CLOUD_SETUP.md) para instrucciones específicas de Laravel Cloud.

**Configuración mínima recomendada:**

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=<generar con php artisan key:generate>
APP_URL=https://tu-dominio.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=<tu-host>
DB_PORT=3306
DB_DATABASE=bebras
DB_USERNAME=<usuario>
DB_PASSWORD=<contraseña-segura>

CACHE_STORE=redis
SESSION_DRIVER=cookie
SESSION_ENCRYPT=true
QUEUE_CONNECTION=database

# Email
MAIL_DRIVER=smtp
MAIL_HOST=<host-smtp>
MAIL_PORT=587
MAIL_USERNAME=<usuario>
MAIL_PASSWORD=<password>
MAIL_FROM_ADDRESS=noreply@tu-dominio.com
```

### Pasos de Despliegue

1. Clonar repo en servidor
2. Configurar `.env` con valores de producción
3. Ejecutar `composer install --no-dev`
4. Ejecutar `php artisan optimize:cache`
5. Ejecutar `npm run build`
6. Ejecutar `php artisan migrate --force`
7. Configurar `public/storage` → `storage/app/public` (symlink)
8. Configurar permisos: `chown -R www-data:www-data storage bootstrap/cache`
9. Configurar nginx con `public/index.php` como entry point
10. Configurar cron job: `* * * * * cd /path-to-app && php artisan schedule:run >> /dev/null 2>&1`

## ⚠️ Problemas Detectados y TODOs

### Errores Actuales

#### 1. Error en Vista de Preguntas Sin Tipo

**Archivo**: [resources/views/preguntas/show.blade.php](resources/views/preguntas/show.blade.php) (línea 83)

- **Problema**: Si `tipo_interaccion` está vacío, genera error al incluir archivo vacío
- **Impacto**: Preguntas 15, 21, 26 causarán error
- **Solución recomendada**: Agregar validación

```php
@if(!empty($pregunta->tipo_interaccion))
    @include('preguntas.tipos.' . $pregunta->tipo_interaccion)
@else
    <div class="alert alert-warning">
        Esta pregunta aún no tiene tipo de interacción implementado.
    </div>
@endif
```

### Mejoras Pendientes

| Prioridad | Tarea                          | Descripción                  | Archivos                                                              |
| --------- | ------------------------------ | ---------------------------- | --------------------------------------------------------------------- |
| 🔴 Alta   | Implemente 3 tipos faltantes   | Preguntas 15, 21, 26         | `app/Enums/TipoInteraccion.php`, `resources/views/preguntas/tipos/**` |
| 🟡 Media  | Valide tipo_interaccion en BD  | Evitar tipos inválidos       | Migración, modelo                                                     |
| 🟡 Media  | Agregue seeders detallados     | Datos de prueba completos    | `database/seeders/PreguntasSeeder.php`                                |
| 🟡 Media  | Implemente tests completos     | Coverage >80%                | `tests/Feature/`, `tests/Unit/`                                       |
| 🟢 Baja   | Agregue comentarios en seeders | Mejorar mantenibilidad       | `database/seeders/`                                                   |
| 🟢 Baja   | Documente API de interacciones | Para futuros desarrolladores | Wiki/Docs                                                             |

## 📚 Recursos Adicionales

- [Laravel 12 Documentation](https://laravel.com/docs/12.x)
- [Livewire 3 Documentation](https://livewire.laravel.com)
- [Volt Documentation](https://livewire.laravel.com/docs/volt)
- [Tailwind CSS Documentation](https://tailwindcss.com)
- [Docker Documentation](https://docs.docker.com)
- [Bebras International](https://www.bebras.org/)

## 💬 Convenciones del Proyecto

### Nomenclatura

- **Modelos**: PascalCase (`Pregunta`, `ProgresoUsuario`)
- **Controladores**: PascalCase + "Controller" (`PreguntaController`)
- **Vistas**: snake_case + `.blade.php` (`show.blade.php`)
- **Rutas**: kebab-case (`preguntas`, `admin-dashboard`)
- **Métodos**: camelCase (`verificarRespuesta()`)
- **BD**: snake_case (`tipo_interaccion`, `respuesta_correcta`)

### Estructura de Vistas

```
resources/views/preguntas/
├── show.blade.php           # Vista principal
├── tipos/                   # Tipos de interacción
│   ├── seleccion_simple.blade.php
│   ├── seleccion_multiple.blade.php
│   └── ...
└── scripts/                 # Validación y lógica
    ├── seleccion_simple.blade.php
    ├── seleccion_multiple.blade.php
    └── ...
```

Cada tipo tiene:

1. **Vista HTML**: Renderiza la pregunta
2. **Script JS**: Valida y procesa respuesta

### Validación de Respuestas

En `PreguntaController@verificar()`:

```php
// Método privado para cada tipo
private function validarSeleccionSimple($respuesta, $correcta) { ... }
private function validarSeleccionMultiple($respuesta, $correcta) { ... }
```

## 🤝 Contribuciones

### Para agregar una nueva pregunta:

1. Crear entrada en `PreguntasSeeder`
2. Asignar `tipo_interaccion` válido
3. Crear/verificar vista en `resources/views/preguntas/tipos/`
4. Crear/verificar script en `resources/views/preguntas/scripts/`
5. Verificar método de validación en `PreguntaController`

### Para agregar nuevo tipo de interacción:

1. Agregar entrada en `app/Enums/TipoInteraccion.php`
2. Crear vista en `resources/views/preguntas/tipos/{tipo}.blade.php`
3. Crear script en `resources/views/preguntas/scripts/{tipo}.blade.php`
4. Implementar método de validación en `PreguntaController`
5. Agregar tests en `tests/Feature/`

## 📞 Soporte

Para problemas específicos de despliegue en Laravel Cloud, consulta:

- [LARAVEL_CLOUD_SETUP.md](LARAVEL_CLOUD_SETUP.md)
- [RESUMEN_TIPOS_INTERACCION.md](RESUMEN_TIPOS_INTERACCION.md)

---

**Última actualización**: Abril 2026  
**Estado**: En desarrollo activo ✅
