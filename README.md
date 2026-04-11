# 🍯 Bebras MX - Laboratorio Interactivo

> Aplicación Laravel para administrar y resolver desafíos Bebras con preguntas interactivas y seguimiento de progreso.

## 🎯 Propósito del proyecto

Bebras MX replica la dinámica del concurso internacional Bebras: el administrador sube PDFs con los problemas oficiales, crea conjuntos de preguntas digitales basados en esos PDFs, y los alumnos los resuelven en la plataforma. El sistema registra cada respuesta, calcula la puntuación y muestra el progreso de cada alumno.

## 🛠️ Stack tecnológico

| Capa        | Tecnología                   | Versión |
| ----------- | ---------------------------- | ------- |
| Backend     | Laravel                      | 12.x    |
| Frontend    | Livewire Flux + Tailwind CSS | Latest  |
| Auth        | Laravel Fortify + 2FA        | 1.30+   |
| DB          | MySQL 8                      | —       |
| Assets      | Vite                         | 7.x     |
| Drag & Drop | SortableJS (CDN)             | 1.15.2  |
| Contenedor  | Laravel Sail / Docker        | —       |

## 🚀 Instalación rápida

```bash
# 1. Clonar y entrar
git clone <repo> && cd bebras-mx

# 2. Variables de entorno
cp .env.example .env

# 3. Iniciar Docker (primer arranque descarga imágenes)
./vendor/bin/sail up -d

# 4. Instalar dependencias PHP
./vendor/bin/sail composer install

# 5. Generar clave, migrar y sembrar
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate:fresh --seed

# 6. Symlink de storage (PDFs e imágenes)
./vendor/bin/sail artisan storage:link

# 7. Assets frontend
./vendor/bin/sail npm install && ./vendor/bin/sail npm run build
```

**Credenciales de ejemplo:**

- Admin: `admin@bebras.mx` / `admin123`
- Alumno: `alumno@bebras.mx` / `alumno123`

## 👥 Roles y flujos

### Administrador (`role = 'admin'`)

Login → /admin/dashboard
├── Gestionar PDFs → /admin/pdfs
├── Gestionar Conjuntos → /admin/conjuntos
│ └── Ver conjunto → /admin/conjuntos/{id}
│ └── Agregar/Editar preguntas
└── Ver progreso alumno → /admin/alumnos/{id}/progreso

### Alumno (`role = 'alumno'`)

Login → /conjuntos
├── Ver conjunto → /conjuntos/{id}
├── Iniciar → POST /conjuntos/{id}/iniciar
├── Resolver → /conjuntos/{id}/preguntas/{pregunta_id}
├── Verificar → POST .../verificar
├── Finalizar → PATCH /conjuntos/{id}/finalizar
├── Ver resultados → /conjuntos/{id}/resultados

## 🗂 Arquitectura General

### Capas principales

- **`app/Models/`** → Modelos dominantes: `User`, `ArchivoPdf`, `Conjunto`, `Pregunta`, `SesionConjunto`, `ProgresoUsuario`
    - Define relaciones Eloquent: belongsTo, hasMany, etc.
    - Scopes para consultas comunes (activos, activas).
    - Casts para arrays JSON (configuracion, respuesta_correcta).
- **`app/Http/Controllers/`** → Controladores de usuario y admin
    - `ConjuntoController`: Gestión de sesiones de conjunto, navegación de preguntas.
    - `PreguntaController`: Validación de respuestas según tipo de interacción.
    - `Admin/*`: CRUD para PDFs, conjuntos y preguntas.
- **`app/Http/Middleware/`** → `AdminMiddleware` para proteger el área admin
- **`app/Enums/`** → `TipoInteraccion`: Enum con tipos de interacción soportados (aunque no todos implementados).
- **`routes/web.php`** → Rutas web con middleware `auth` y `admin`
    - Rutas públicas: home, welcome.
    - Autenticadas: conjuntos, preguntas, resultados.
    - Admin: dashboard, CRUD de recursos.
- **`resources/views/`** → Vistas Blade para alumnos y administración
    - `conjuntos/`: index, show, resultados.
    - `preguntas/`: show con tipos dinámicos, scripts JS.
    - `admin/`: dashboard, CRUD forms con diseño consistente.
- **`database/migrations/`** → Esquema de datos para usuarios, PDFs, conjuntos, preguntas y progreso
    - Relaciones foreign keys, JSON para configuraciones.
- **`database/seeders/`** → Seeders para datos iniciales
    - `UserSeeder`: Admin y alumnos de prueba.
    - `PdfSeeder`, `ConjuntoSeeder`, `PreguntasSeeder`: Datos Bebras.
- **`public/`** → Archivos estáticos accesibles
    - `index.php`: Punto de entrada Laravel.
    - `storage/`: Link simbólico a storage/app/public para archivos subidos (PDFs, imágenes).
    - `build/`: Assets compilados (CSS, JS) via Vite.
- **`storage/`** → Archivos privados y temporales
    - `app/public/`: PDFs e imágenes subidas por admin.
    - `logs/`: Registros de errores y actividad.

## 🚀 Flujo principal del alumno

1. Un alumno inicia sesión y visita `/conjuntos`
2. Visualiza conjuntos activos disponibles
3. Entra a un conjunto `/conjuntos/{conjunto}`
4. Inicia sesión de conjunto con `POST /conjuntos/{conjunto}/iniciar`
5. Resuelve preguntas en `/conjuntos/{conjunto}/preguntas/{pregunta}`
6. Envía respuesta a `/conjuntos/{conjunto}/preguntas/{pregunta}/verificar`
7. Finaliza con `PATCH /conjuntos/{conjunto}/finalizar`
8. Consulta resultados en `/conjuntos/{conjunto}/resultados`

## 🔐 Autenticación y roles

- Rutas de alumno protegidas por middleware `auth`
- Admin protegido por middleware `admin`
- El `dashboard` post-login redirige según rol:
    - admin → `admin.dashboard`
    - alumno → `conjuntos.index`

## �️ Base de datos

### Diagrama de relaciones

```
users (1) ──────────────────────────────────────────── (N) progreso_usuarios
│                                                           │
├── (1:N) sesiones_conjunto                          pregunta_id FK
├── (1:N) archivos_pdf [subido_por]                        │
└── (1:N) conjuntos [creado_por]                      preguntas (N)
│                                      conjunto_id FK
pdf_id FK                                           │
│                                      conjuntos (1)
archivos_pdf (1)
```

### Tablas

#### `users`

| Campo                       | Tipo      | Notas                   |
| --------------------------- | --------- | ----------------------- |
| `id`                        | bigint    | PK                      |
| `name`                      | string    | —                       |
| `email`                     | string    | unique                  |
| `role`                      | string    | `'admin'` \| `'alumno'` |
| `password`                  | string    | hashed                  |
| `two_factor_secret`         | text      | Fortify 2FA             |
| `two_factor_recovery_codes` | text      | Fortify 2FA             |
| `two_factor_confirmed_at`   | timestamp | nullable                |
| `remember_token`            | string    | nullable                |

#### `archivos_pdf`

| Campo             | Tipo   | Notas              |
| ----------------- | ------ | ------------------ |
| `id`              | bigint | PK                 |
| `nombre`          | string | Nombre legible     |
| `descripcion`     | string | nullable           |
| `nombre_original` | string | Nombre al subir    |
| `ruta`            | string | `pdfs/archivo.pdf` |
| `tamanio`         | bigint | Bytes              |
| `subido_por`      | bigint | FK → users         |

#### `conjuntos`

| Campo         | Tipo    | Notas                       |
| ------------- | ------- | --------------------------- |
| `id`          | bigint  | PK                          |
| `nombre`      | string  | —                           |
| `descripcion` | text    | nullable                    |
| `pdf_id`      | bigint  | FK → archivos_pdf, nullable |
| `creado_por`  | bigint  | FK → users                  |
| `activo`      | boolean | default true                |

#### `preguntas`

| Campo                | Tipo    | Notas                           |
| -------------------- | ------- | ------------------------------- |
| `id`                 | bigint  | PK                              |
| `conjunto_id`        | bigint  | FK → conjuntos (cascade delete) |
| `orden`              | int     | Posición en el conjunto         |
| `titulo`             | string  | —                               |
| `enunciado`          | text    | Texto completo del problema     |
| `imagen_enunciado`   | string  | Ruta en storage, nullable       |
| `tipo_interaccion`   | string  | Ver enum abajo                  |
| `configuracion`      | json    | Opciones, elementos, etc.       |
| `respuesta_correcta` | json    | Formato varía por tipo          |
| `explicacion`        | text    | Se muestra tras responder       |
| `imagen_explicacion` | string  | Ruta en storage, nullable       |
| `codigo_tarea`       | string  | Ej: `2022-DE-06`, nullable      |
| `pais_origen`        | string  | nullable                        |
| `nivel`              | string  | I, II, III, IV, V, VI           |
| `dificultad`         | string  | Baja, Media, Alta               |
| `activa`             | boolean | default true                    |

#### `sesiones_conjunto`

| Campo          | Tipo                       | Notas                          |
| -------------- | -------------------------- | ------------------------------ |
| `id`           | bigint                     | PK                             |
| `user_id`      | bigint                     | FK → users                     |
| `conjunto_id`  | bigint                     | FK → conjuntos                 |
| `iniciado_en`  | timestamp                  | —                              |
| `terminado_en` | timestamp                  | nullable                       |
| `puntuacion`   | int                        | nullable (al terminar)         |
| UNIQUE         | (`user_id`, `conjunto_id`) | Una sesión por alumno/conjunto |

#### `progreso_usuarios`

| Campo               | Tipo                       | Notas                             |
| ------------------- | -------------------------- | --------------------------------- |
| `id`                | bigint                     | PK                                |
| `user_id`           | bigint                     | FK → users                        |
| `pregunta_id`       | bigint                     | FK → preguntas                    |
| `respuesta_usuario` | json                       | Lo que envió el alumno            |
| `es_correcta`       | boolean                    | —                                 |
| `intentos`          | int                        | default 1                         |
| `completada_at`     | timestamp                  | —                                 |
| UNIQUE              | (`user_id`, `pregunta_id`) | Una respuesta por alumno/pregunta |

## 🧠 Controladores clave

### `App\Http\Controllers\ConjuntoController`

- **`index()`** → Lista conjuntos activos con PDF y conteo de preguntas activas. Incluye sesiones del alumno para mostrar estado (no iniciado, en progreso, completado).
- **`show()`** → Detalle del conjunto antes de iniciar. Carga preguntas activas, sesiones existentes y progreso del alumno.
- **`iniciar()`** → Crea `SesionConjunto` si no existe, redirige a primera pregunta activa.
- **`finalizar()`** → Calcula puntuación total (respuestas correctas), marca sesión como terminada.
- **`resultados()`** → Muestra preguntas y progreso del alumno en el conjunto.

### `App\Http\Controllers\PreguntaController`

- **`show()`** → Valida que pregunta pertenece al conjunto y sesión está activa. Carga progreso existente.
- **`verificar()`** → Valida respuesta según `tipo_interaccion` usando métodos especializados:
    - `validarSeleccionSimple()`, `validarSeleccionMultiple()`, `validarOrdenar()`, etc.
    - Previene doble envío, guarda en `ProgresoUsuario`, retorna JSON con `correcta`, `explicacion`.
- Métodos de validación por tipo: Comparan respuesta del alumno con `respuesta_correcta` del modelo.

### `App\Http\Controllers\Admin\DashboardController`

- **`index()`** → Métricas: total alumnos, PDFs, conjuntos, respuestas. Lista alumnos con conteo de progresos y sesiones completadas.
- **`verProgreso($userId)`** → Progreso detallado de un alumno: sesiones por conjunto, preguntas respondidas, puntuaciones.

### `App\Http\Controllers\Admin\AdminConjuntoController`

- CRUD completo: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`.
- **`toggle()`** → Activa/desactiva conjuntos.

### `App\Http\Controllers\Admin\AdminPreguntaController`

- CRUD de preguntas dentro de un conjunto.
- Convierte JSON textual de `configuracion` y `respuesta_correcta` a arrays.
- Lista de tipos de interacción codificada localmente (debería usar `TipoInteraccion::values()`).
- **`toggle()`** → Activa/desactiva preguntas.

### `App\Http\Controllers\Admin\PdfController`

- Gestión de PDFs: subida segura (`mimes:pdf`), validación de tamaño.
- Eliminación condicionada: solo si no tiene conjuntos asociados.

## 🧩 Rutas principales

### Alumno

- `GET /conjuntos` → `conjuntos.index`
- `GET /conjuntos/{conjunto}` → `conjuntos.show`
- `POST /conjuntos/{conjunto}/iniciar` → `conjuntos.iniciar`
- `GET /conjuntos/{conjunto}/preguntas/{pregunta}` → `preguntas.show`
- `POST /conjuntos/{conjunto}/preguntas/{pregunta}/verificar` → `preguntas.verificar`
- `PATCH /conjuntos/{conjunto}/finalizar` → `conjuntos.finalizar`
- `GET /conjuntos/{conjunto}/resultados` → `conjuntos.resultados`

### Admin

- `GET /admin/dashboard` → `admin.dashboard`
- `GET /admin/alumnos/{usuario}/progreso` → `admin.alumnos.progreso`
- CRUD de PDFs: `admin/pdfs`
- CRUD de conjuntos: `admin/conjuntos`
- CRUD de preguntas dentro de un conjunto: `admin/conjuntos/{conjunto}/preguntas`

## 🛣️ Rutas completas

### Públicas

GET / → home (welcome)

### Autenticadas (`auth`)

GET /dashboard → Redirige según rol
GET /conjuntos → conjuntos.index (dashboard alumno)
GET /conjuntos/{conjunto} → conjuntos.show
POST /conjuntos/{conjunto}/iniciar → conjuntos.iniciar
GET /conjuntos/{conjunto}/preguntas/{pregunta} → preguntas.show
POST /conjuntos/{conjunto}/preguntas/{pregunta}/verificar → preguntas.verificar
PATCH /conjuntos/{conjunto}/finalizar → conjuntos.finalizar
GET /conjuntos/{conjunto}/resultados → conjuntos.resultados
GET /settings/profile → profile.edit (Volt)
GET /settings/password → user-password.edit (Volt)
GET /settings/appearance → appearance.edit (Volt)
GET /settings/two-factor → two-factor.show (Volt/Fortify)

### Admin (`auth` + `admin`)

GET /admin/dashboard → admin.dashboard
GET /admin/alumnos/{usuario}/progreso → admin.alumnos.progreso
GET /admin/pdfs → admin.pdfs.index
GET /admin/pdfs/create → admin.pdfs.create
POST /admin/pdfs → admin.pdfs.store
GET /admin/pdfs/{archivoPdf} → admin.pdfs.show
DELETE /admin/pdfs/{archivoPdf} → admin.pdfs.destroy
GET /admin/conjuntos → admin.conjuntos.index
GET /admin/conjuntos/create → admin.conjuntos.create
POST /admin/conjuntos → admin.conjuntos.store
GET /admin/conjuntos/{conjunto} → admin.conjuntos.show
GET /admin/conjuntos/{conjunto}/edit → admin.conjuntos.edit
PATCH /admin/conjuntos/{conjunto} → admin.conjuntos.update
DELETE /admin/conjuntos/{conjunto} → admin.conjuntos.destroy
PATCH /admin/conjuntos/{conjunto}/toggle → admin.conjuntos.toggle
GET /admin/conjuntos/{conjunto}/preguntas/create → admin.preguntas.create
POST /admin/conjuntos/{conjunto}/preguntas → admin.preguntas.store
GET /admin/conjuntos/{conjunto}/preguntas/{pregunta}/edit → admin.preguntas.edit
PATCH /admin/conjuntos/{conjunto}/preguntas/{pregunta} → admin.preguntas.update
DELETE /admin/conjuntos/{conjunto}/preguntas/{pregunta} → admin.preguntas.destroy
PATCH /admin/conjuntos/{conjunto}/preguntas/{pregunta}/toggle → admin.preguntas.toggle

## 🎨 Vistas relevantes

### Alumno

- **`resources/views/conjuntos/index.blade.php`**
    - Lista de conjuntos activos con estado de sesión (botones "Iniciar", "Continuar", "Ver resultados").
    - Muestra PDF asociado, número de preguntas, sesiones del alumno.
- **`resources/views/conjuntos/show.blade.php`**
    - Detalle del conjunto: descripción, PDF, lista de preguntas con estado (no respondida, correcta, incorrecta).
    - Botón para iniciar sesión.
- **`resources/views/conjuntos/resultados.blade.php`**
    - Resultados finales: puntuación, preguntas con respuestas del alumno y correctas, explicaciones.
- **`resources/views/preguntas/show.blade.php`**
    - Plantilla principal: enunciado, imagen, área de interacción dinámica (`preguntas.tipos.{tipo_interaccion}`).
    - Navegación entre preguntas, botón "Verificar" (deshabilitado si ya respondida).
    - Incluye scripts JS (`preguntas.scripts.{tipo_interaccion}`).
    - Validación: Si `tipo_interaccion` vacío, muestra mensaje de advertencia.
- **`resources/views/preguntas/tipos/*.blade.php`**
    - Vistas específicas por tipo: `seleccion_simple`, `ordenar`, etc.
    - Renderizan la interfaz de interacción basada en `configuracion`.
- **`resources/views/preguntas/scripts/*.blade.php`**
    - Scripts JS para interactividad: drag&drop (SortableJS), validaciones, AJAX para verificar.

### Admin

- **`resources/views/admin/dashboard.blade.php`**
    - Métricas generales, lista de alumnos con links a progreso.
- **`resources/views/admin/pdfs/*`**
    - `index`: Lista PDFs con acciones CRUD.
    - `create/edit`: Formularios con subida de archivos.
- **`resources/views/admin/conjuntos/*`**
    - `index`: Lista conjuntos con toggle activo, conteo preguntas.
    - `create/edit`: Formularios con select PDF, campos básicos.
    - `show`: Detalle conjunto + lista preguntas con acciones.
- **`resources/views/admin/preguntas/*`**
    - `create/edit`: Formularios complejos para preguntas: JSON para config y respuesta.
- **`resources/views/admin/alumnos/progreso.blade.php`**
    - Progreso por conjunto: sesiones, puntuaciones, detalle preguntas.

### Observaciones

- `resources/views/preguntas/index.blade.php` existe pero no está enlazada en rutas (ruta `preguntas.index` no definida).
- `resources/views/layouts/app.blade.php`: Layout principal con navegación, dark mode, responsive.
- Diseño consistente: Cards con `bg-white/95 dark:bg-neutral-900/90`, gradientes, flash messages.

## ✅ Tipos de interacción soportados

Todos los tipos tienen un archivo en `resources/views/preguntas/tipos/` (HTML) y otro en `resources/views/preguntas/scripts/` (JavaScript).

| Tipo                     | Descripción                                 | `configuracion` clave                                                  | `respuesta_correcta` formato |
| ------------------------ | ------------------------------------------- | ---------------------------------------------------------------------- | ---------------------------- |
| `seleccion_simple`       | Elige 1 opción (texto o imagen)             | `opciones: [{id, tipo, valor}]`                                        | `["B"]`                      |
| `seleccion_multiple`     | Elige N opciones con checkboxes             | `opciones: [{id, valor}]`                                              | `["A","C"]`                  |
| `ordenar`                | Drag & drop para ordenar (SortableJS)       | `elementos: [{id, nombre}]`                                            | `[["1","2","3"]]`            |
| `grid_seleccion`         | Marcar celdas en cuadrícula                 | `labels_filas, labels_columnas, estado_inicial, numeros_celdas`        | `[{fila, columna}]`          |
| `emparejar`              | Relacionar con selects                      | `objetos: [{id, nombre}], destinos: [{id, nombre}]`                    | `[{objeto, destino}]`        |
| `rellenar`               | Colorear áreas con paleta                   | `colores_disponibles, areas: [{id, nombre}]`                           | `[{area, color}]`            |
| `texto_libre`            | Input de texto o número                     | `tipo_respuesta: "numero"\|"texto", min, max`                          | `["4"]`                      |
| `completar`              | Flexible: checkboxes, slots, blanks, string | Varía según subtipo                                                    | Varía                        |
| `colocar_piezas`         | Drag & drop de abejas en panal hexagonal    | `celdas_hexagonales: 7\|19, abejas: [{id, imagen}]`                    | `[{abeja, celda}]`           |
| `colorear_hexagonos`     | Pintar pirámide de hexágonos                | `colores_disponibles, filas, hexagonos_iniciales: [{posicion, color}]` | `[{posicion, color}]`        |
| `tejer_alfombra`         | Grid NxM con símbolos/colores               | `filas, columnas, simbolos_disponibles`                                | Array 2D de letras           |
| `rompecabezas_hexagonos` | Colocar piezas en grid hexagonal            | `piezas_disponibles: [{id, color, imagen}], estructura`                | `[{pieza, fila, columna}]`   |

### SortableJS

Los tipos `ordenar` y `completar` (subtipo imagen) cargan SortableJS desde CDN. `preguntas/show.blade.php` lo inyecta automáticamente cuando el tipo lo requiere:

```blade
@if(in_array($pregunta->tipo_interaccion, ['ordenar', 'completar']))
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
@endif
```

### ✅ Implementados (7 tipos - 89% de preguntas)

- **`seleccion_simple`**: Elegir 1 opción (imagen/texto). Preguntas: 13. Archivos: tipos/ + scripts/. Validación: ✓
- **`seleccion_multiple`**: Elegir varias opciones. Preguntas: 2. Validación: ✓
- **`ordenar`**: Drag & drop para ordenar. Preguntas: 4. Dependencias: SortableJS. Validación: ✓
- **`grid_seleccion`**: Marcar celdas en grid. Preguntas: 2. Validación: ✓
- **`emparejar`**: Unir elementos con líneas. Preguntas: 1. Validación: ✓
- **`rellenar`**: Colorear/rellenar espacios. Preguntas: 1. Validación: ✓
- **`texto_libre`**: Respuesta escrita. Preguntas: 1. Validación: ✓

### ❌ No implementados (3 tipos - 11% de preguntas)

- **`colocar_piezas`**: Pregunta 15 - Panal de Abejas. Grid hexagonal con 19 celdas, 7 abejas con reglas de posicionamiento.
- **`colorear_hexagonos`**: Pregunta 21 - Hexágonos de Colores. Estructura piramidal de 5 filas, 3 colores.
- **`tejer_alfombra`**: Pregunta 26 - Tejiendo Alfombras. Grid 6x6 con símbolos/colores.

## 📌 Observaciones y puntos pendientes

### ✅ Corregidos

- **Vista `preguntas.show`**: Agregada validación `@if(!empty($pregunta->tipo_interaccion))` para evitar errores con tipos vacíos.
- **Scripts JS**: Mismo fix aplicado.
- **DashboardController::verProgreso()**: Variable `$usuario` correctamente definida (bug reportado era incorrecto).

### ⚠️ Pendientes

- **`App\Enums\TipoInteraccion.php`**: Incluye tipos no implementados (`colocar_piezas`, `colorear_hexagonos`, `tejer_alfombra`, `completar`). Debería reflejar solo implementados o agregar implementaciones.
- **`resources/views/preguntas/index.blade.php`**: Vista huérfana sin ruta (`preguntas.index` no definida).
- **AdminPreguntaController**: Lista de tipos codificada localmente; usar `TipoInteraccion::values()`.
- **Implementar tipos faltantes**: 3 preguntas sin funcionalidad completa.
- **Validación en PreguntaController**: Manejo específico para tipos vacíos (actualmente retorna `false`).

### 🔄 Mejoras futuras

- Unificar tipos en Enum con implementaciones.
- Agregar tests para tipos de interacción.
- Optimizar consultas N+1 en vistas (eager loading).
- Implementar cache para configuraciones estáticas.

## 🧪 Datos iniciales

El proyecto incluye seeders para:

- `DatabaseSeeder`
- `UserSeeder`
- `PdfSeeder`
- `ConjuntoSeeder`
- `PreguntasSeeder`

Estos seeders crean usuarios de ejemplo, conjuntos, PDFs y preguntas Bebras.

## 📌 Estado actual del sistema

El proyecto está implementado como un sistema funcional de gestión y resolución de cuestionarios Bebras, con soporte para:

- ✅ Sesión de alumno por conjunto (única por par user-conjunto)
- ✅ Registro de respuesta por pregunta (única por par user-pregunta)
- ✅ Puntuación de conjunto (conteo de correctas)
- ✅ Administración de recursos PDF (subida, asociación a conjuntos)
- ✅ Administración de conjuntos y preguntas (CRUD completo)
- ✅ 7 tipos de interacción implementados (89% de preguntas)
- ✅ Autenticación con roles (admin/alumno) y 2FA
- ✅ UI responsive con dark mode y diseño consistente
- ✅ Validaciones de seguridad (middleware admin, abort 404/403)
- ✅ Prevención de doble envío en respuestas

### 🔄 Áreas de mejora identificadas

- **Completitud**: 3 preguntas sin tipo de interacción (11%)
- **Consistencia**: Enum vs implementaciones, rutas huérfanas
- **Rendimiento**: Consultas N+1 en algunas vistas
- **Mantenibilidad**: Código duplicado en validaciones, tipos codificados

### 🚀 Próximos pasos recomendados

1. **Implementar tipos faltantes**: `colocar_piezas`, `colorear_hexagonos`, `tejer_alfombra`
2. **Limpiar Enum**: Sincronizar con implementaciones reales
3. **Agregar tests**: Cobertura para controladores y tipos de interacción
4. **Optimizar DB**: Eager loading en consultas complejas
5. **Documentar API**: Endpoints para futuras integraciones

## 📁 Resumen de carpetas clave

- **`app/`**: Lógica de negocio. Modelos con relaciones Eloquent, controladores CRUD, enums para tipos, middleware de auth.
- **`database/`**: Esquema y datos. Migraciones con foreign keys y JSON, seeders para datos Bebras completos.
- **`public/`**: Punto de entrada y estáticos. `index.php` para Laravel, `storage/` link para archivos subidos, `build/` assets Vite.
- **`resources/`**: Vistas y assets. Blade templates con diseño consistente, JS para interactividad, CSS Tailwind.
- **`routes/`**: Definición de endpoints. Rutas autenticadas para alumnos, admin protegidas, sin rutas huérfanas críticas.
- **`storage/`**: Archivos privados. PDFs/imágenes subidas, logs de aplicación, cache/framework.

## 🔐 Autenticación

- **Laravel Fortify** con soporte 2FA activado
- **Middleware `admin`**: `app/Http/Middleware/AdminMiddleware.php` — verifica `user->isAdmin()`
- **Registro en** `bootstrap/app.php` como alias `'admin'`
- Post-login: `GET /dashboard` redirige a `admin.dashboard` o `conjuntos.index` según rol

## 📦 Storage

Los archivos subidos van a `storage/app/public/`:

- `pdfs/` → PDFs subidos por el admin

El symlink `public/storage → storage/app/public` se crea con:

```bash
sail artisan storage:link
```

Las rutas de imágenes en `configuracion` JSON de preguntas son **rutas relativas** desde storage, ejemplo: `"valor": "imagenes/tabla-libros.png"`. En las vistas se accede con `asset('storage/' . $ruta)` o `Storage::url($ruta)`.

## 🌱 Seeders

DatabaseSeeder
├── UserSeeder → admin@bebras.mx (admin123) + alumno@bebras.mx (alumno123)
├── PdfSeeder → Registro placeholder del PDF de Primavera 2025
└── ConjuntoSeeder → Conjunto "Reto Bebras MX Primavera 2025" con 5 preguntas de ejemplo

**Nota**: `PreguntasSeeder.php` es un archivo legacy incompatible con el esquema actual. No está incluido en `DatabaseSeeder`. Eliminar o ignorar.

## ⚠️ Consideraciones importantes

### Al crear preguntas

1. **`configuracion`** y **`respuesta_correcta`** se ingresan como JSON en el formulario admin. El controlador convierte el string a array antes de guardar.
2. Las **imágenes** referenciadas en `configuracion` deben subirse primero desde **Admin → Imágenes** y copiar la ruta exacta. _(Pendiente: ImagenController no implementado)_
3. El campo `imagen_enunciado` e `imagen_explicacion` de la pregunta también son rutas de storage (no URLs completas).
4. El formulario de creación incluye **plantillas JSON por tipo** que se cargan con el botón "↺ Cargar plantilla".

### Al resolver preguntas

- Un alumno **no puede responder dos veces** la misma pregunta (índice único en `progreso_usuarios`).
- Un alumno **no puede volver a abrir** un conjunto terminado para responder más preguntas.
- La verificación ocurre en el servidor (`PreguntaController::verificar()`), nunca en el cliente.

### Storage en Docker

Si el visor de PDFs o imágenes muestra 404:

1. Ejecutar `sail artisan storage:link`
2. Verificar que `APP_URL=http://localhost` en `.env`
3. Verificar que el archivo existe en `storage/app/public/pdfs/` o `imagenes/`

## 📋 Comandos útiles

```bash
# Reset completo con datos iniciales
sail artisan migrate:fresh --seed && sail artisan storage:link

# Limpiar cachés
sail artisan optimize:clear

# Shell del contenedor
sail shell

# Tinker (consola interactiva)
sail artisan tinker

# Ver rutas
sail artisan route:list --path=admin

# Compilar assets (desarrollo)
sail npm run dev

# Compilar assets (producción)
sail npm run build
```

## 📊 Estado del sistema

| Característica                        | Estado                               |
| ------------------------------------- | ------------------------------------ |
| Autenticación + 2FA                   | ✅                                   |
| Roles admin/alumno                    | ✅                                   |
| CRUD de PDFs                          | ✅                                   |
| CRUD de Conjuntos                     | ✅                                   |
| CRUD de Preguntas                     | ✅                                   |
| Formulario guiado con plantillas JSON | ✅                                   |
| 7 tipos de interacción                | ✅                                   |
| Sesiones de conjunto por alumno       | ✅                                   |
| Registro de progreso por pregunta     | ✅                                   |
| Dashboard con métricas                | ✅ (admin y alumno)                  |
| Visor PDF embebido                    | ✅                                   |
| Dark mode responsive                  | ✅                                   |
| Protección doble envío                | ✅                                   |
| Navegación entre preguntas            | ✅ (miniaturas + anterior/siguiente) |
| Gestión de Imágenes                   | ❌ (Pendiente)                       |
| Biblioteca de PDFs para alumnos       | ❌ (Pendiente)                       |
