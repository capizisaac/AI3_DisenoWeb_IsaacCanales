# ORDENALO

Aplicación web de **presupuesto personal**: registra tus ingresos y gastos, consulta el resumen del mes y vigila cuánto llevas gastado frente al límite de cada categoría.

Desarrollada en PHP orientado a objetos, con acceso a datos por PDO y MariaDB/MySQL, y organizada por capas (validación, servicios, repositorio y vistas).

## Funcionalidades

- **Registro de movimientos**: tipo (ingreso o gasto), categoría, monto, descripción y fecha, con validación en el cliente y en el servidor.
- **Consulta**: tabla responsiva con todos los movimientos (en pantallas angostas cada fila se convierte en una tarjeta).
- **Resumen del mes actual**: ingresos, gastos y saldo.
- **Límites de gasto por categoría**, con tres niveles: *dentro del límite*, *cerca* (80 % o más del límite) y *excedido*.
- **Formulario inteligente**: al elegir el tipo, solo se ofrecen las categorías que le corresponden.
- **Tema claro y oscuro** automático según el sistema del usuario.

## Tecnologías

| Capa | Herramientas |
|---|---|
| Lenguaje | PHP 8.2 (clases, enums, propiedades `readonly`) |
| Base de datos | MariaDB 10.4 / MySQL, acceso por PDO |
| Dependencias | Composer: autoload PSR-4 y `vlucas/phpdotenv` |
| Interfaz | HTML5, CSS3 y JavaScript, sin librerías externas |
| Entorno | XAMPP (Apache + MariaDB) |
| Control de versiones | Git y GitHub |

## Requisitos

- XAMPP con Apache y MariaDB/MySQL, y **PHP 8.2 o superior**.
- **Composer 2**.
- **Git**.
- Extensión `zip` de PHP activada, que Composer usa para descargar paquetes: en `C:\xampp\php\php.ini`, la línea `extension=zip` sin punto y coma al inicio.
- `mod_rewrite` activo y `AllowOverride All` para `htdocs`, que es la configuración por defecto de XAMPP; el archivo `.htaccess` depende de ello.

## Instalación y despliegue local

1. Clona el repositorio dentro de `htdocs`. El nombre de la carpeta forma parte de la URL:

   ```powershell
   cd C:\xampp\htdocs
   git clone https://github.com/capizisaac/AI3_DisenoWeb_IsaacCanales.git
   cd AI3_DisenoWeb_IsaacCanales
   ```

2. Instala las dependencias:

   ```powershell
   composer install
   ```

3. Crea el archivo de entorno a partir de la plantilla:

   ```powershell
   Copy-Item .env.example .env
   ```

   Abre `.env` y escribe en `DB_PASS` la clave que usarás en el paso 5.

4. Inicia **Apache** y **MySQL** desde el panel de XAMPP e importa la base de datos (crea `db_sistema_web` y la tabla `movimientos`):

   ```powershell
   C:\xampp\mysql\bin\mysql.exe -u root -e "source database/schema.sql"
   ```

   También puedes pegar el contenido de `database/schema.sql` en la pestaña **SQL** de phpMyAdmin. El script no usa `IF NOT EXISTS` para la tabla: si ya existe, bórrala antes de importar de nuevo.

5. Crea el usuario de la aplicación con los permisos mínimos (usa la misma clave que pusiste en `.env`):

   ```sql
   CREATE USER 'ordenalo_app'@'localhost' IDENTIFIED BY 'TU_CLAVE';
   GRANT SELECT, INSERT ON db_sistema_web.* TO 'ordenalo_app'@'localhost';
   ```

6. Abre la aplicación:

   <http://localhost/AI3_DisenoWeb_IsaacCanales/public/>

### Variables de entorno (`.env`)

| Variable | Valor por defecto | Descripción |
|---|---|---|
| `DB_HOST` | `localhost` | Servidor de base de datos |
| `DB_NAME` | `db_sistema_web` | Nombre de la base de datos |
| `DB_USER` | `ordenalo_app` | Usuario de la aplicación |
| `DB_PASS` | *(vacío)* | Clave del usuario; debes definirla |
| `DB_CHARSET` | `utf8mb4` | Juego de caracteres de la conexión |

El archivo `.env` no se sube a Git: solo se versiona `.env.example`.

## Configuración

- **Límites mensuales por categoría**: se editan en `config/limites.php` (montos en USD, con punto decimal). Una categoría que no aparezca allí no tiene límite.
- **Umbral de "cerca del límite"**: constante `UMBRAL_CERCA` (80) en `src/Services/ResumenService.php`.
- **Zona horaria**: `America/Guayaquil`, definida en `config/contenedor.php`.

## Reglas de negocio

| Campo | Regla |
|---|---|
| Tipo | `ingreso` o `gasto` |
| Categoría | Ingreso: sueldo, extras u otros. Gasto: alimentación, transporte, vivienda, servicios, salud, educación, ocio u otros |
| Monto | Mayor que cero, con punto decimal, hasta 8 enteros y 2 decimales (`125.50`) |
| Descripción | Entre 3 y 255 caracteres |
| Fecha | Entre `2000-01-01` y `2100-12-31`; se permiten fechas futuras |

El resumen mensual cuenta **todo el mes en curso**, incluidos los movimientos fechados después de hoy. Un gasto que supera exactamente el límite queda en *cerca*; solo se marca *excedido* cuando lo sobrepasa.

## Arquitectura

Cada página de `public/` solo coordina: pide los servicios al contenedor, procesa la petición y elige la vista.

```
Navegador
   │  petición HTTP
   ▼
public/ (crear.php, listar.php)
   │
   ├─► MovimientoService ──► Reglas de validación ──► Movimiento (entidad)
   │          │
   │          └─► MovimientoRepositorio (interfaz) ──► MovimientoRepositorioPdo ──► MariaDB
   │
   ├─► ResumenService ──► MovimientoRepositorio ──► totales y límites
   │
   └─► Vista ──► plantillas (HTML con Html::e() en cada dato)
```

| Pieza | Responsabilidad |
|---|---|
| `config/contenedor.php` | Único lugar que ensambla los objetos concretos (inyección de dependencias) |
| `src/Database/ConexionFactory.php` | Construye el `PDO` con las opciones seguras |
| `src/Models/` | Entidad `Movimiento`, objeto de valor `Dinero` (centavos enteros y moneda), enums `Tipo`, `Categoria` y `NivelPresupuesto`, y los objetos del resumen |
| `src/Validation/` | Una regla por campo, todas con la misma interfaz (**Strategy**) |
| `src/Services/` | `MovimientoService` (**Facade** de registro y consulta) y `ResumenService` (totales y límites) |
| `src/Repositories/` | Interfaz del repositorio e implementación con PDO |
| `src/Views/` | Clase `Vista`, escape de salida centralizado y plantillas |

Principios aplicados: responsabilidad única, dependencia de interfaces (los servicios no conocen PDO), reglas nuevas sin modificar las existentes y objetos inmutables. El dinero se maneja en **centavos enteros** para que las sumas y comparaciones sean exactas.

## Seguridad

| Riesgo | Medida |
|---|---|
| Inyección SQL | Sentencias preparadas con PDO y preparación nativa (`ATTR_EMULATE_PREPARES => false`); el charset va en el DSN |
| XSS | Toda salida pasa por
if (Test-Path README.md) { Copy-Item README.md (Join-Path $env:TEMP 'README_anterior.md') -Force; 'Copia del README anterior en: ' + (Join-Path $env:TEMP 'README_anterior.md') }

$readme = @'
# ORDENALO

Aplicación web de **presupuesto personal**: registra tus ingresos y gastos, consulta el resumen del mes y vigila cuánto llevas gastado frente al límite de cada categoría.

Desarrollada en PHP orientado a objetos, con acceso a datos por PDO y MariaDB/MySQL, y organizada por capas (validación, servicios, repositorio y vistas).

## Funcionalidades

- **Registro de movimientos**: tipo (ingreso o gasto), categoría, monto, descripción y fecha, con validación en el cliente y en el servidor.
- **Consulta**: tabla responsiva con todos los movimientos (en pantallas angostas cada fila se convierte en una tarjeta).
- **Resumen del mes actual**: ingresos, gastos y saldo.
- **Límites de gasto por categoría**, con tres niveles: *dentro del límite*, *cerca* (80 % o más del límite) y *excedido*.
- **Formulario inteligente**: al elegir el tipo, solo se ofrecen las categorías que le corresponden.
- **Tema claro y oscuro** automático según el sistema del usuario.

## Tecnologías

| Capa | Herramientas |
|---|---|
| Lenguaje | PHP 8.2 (clases, enums, propiedades `readonly`) |
| Base de datos | MariaDB 10.4 / MySQL, acceso por PDO |
| Dependencias | Composer: autoload PSR-4 y `vlucas/phpdotenv` |
| Interfaz | HTML5, CSS3 y JavaScript, sin librerías externas |
| Entorno | XAMPP (Apache + MariaDB) |
| Control de versiones | Git y GitHub |

## Requisitos

- XAMPP con Apache y MariaDB/MySQL, y **PHP 8.2 o superior**.
- **Composer 2**.
- **Git**.
- Extensión `zip` de PHP activada, que Composer usa para descargar paquetes: en `C:\xampp\php\php.ini`, la línea `extension=zip` sin punto y coma al inicio.
- `mod_rewrite` activo y `AllowOverride All` para `htdocs`, que es la configuración por defecto de XAMPP; el archivo `.htaccess` depende de ello.

## Instalación y despliegue local

1. Clona el repositorio dentro de `htdocs`. El nombre de la carpeta forma parte de la URL:

   ```powershell
   cd C:\xampp\htdocs
   git clone https://github.com/capizisaac/AI3_DisenoWeb_IsaacCanales.git
   cd AI3_DisenoWeb_IsaacCanales
   ```

2. Instala las dependencias:

   ```powershell
   composer install
   ```

3. Crea el archivo de entorno a partir de la plantilla:

   ```powershell
   Copy-Item .env.example .env
   ```

   Abre `.env` y escribe en `DB_PASS` la clave que usarás en el paso 5.

4. Inicia **Apache** y **MySQL** desde el panel de XAMPP e importa la base de datos (crea `db_sistema_web` y la tabla `movimientos`):

   ```powershell
   C:\xampp\mysql\bin\mysql.exe -u root -e "source database/schema.sql"
   ```

   También puedes pegar el contenido de `database/schema.sql` en la pestaña **SQL** de phpMyAdmin. El script no usa `IF NOT EXISTS` para la tabla: si ya existe, bórrala antes de importar de nuevo.

5. Crea el usuario de la aplicación con los permisos mínimos (usa la misma clave que pusiste en `.env`):

   ```sql
   CREATE USER 'ordenalo_app'@'localhost' IDENTIFIED BY 'TU_CLAVE';
   GRANT SELECT, INSERT ON db_sistema_web.* TO 'ordenalo_app'@'localhost';
   ```

6. Abre la aplicación:

   <http://localhost/AI3_DisenoWeb_IsaacCanales/public/>

### Variables de entorno (`.env`)

| Variable | Valor por defecto | Descripción |
|---|---|---|
| `DB_HOST` | `localhost` | Servidor de base de datos |
| `DB_NAME` | `db_sistema_web` | Nombre de la base de datos |
| `DB_USER` | `ordenalo_app` | Usuario de la aplicación |
| `DB_PASS` | *(vacío)* | Clave del usuario; debes definirla |
| `DB_CHARSET` | `utf8mb4` | Juego de caracteres de la conexión |

El archivo `.env` no se sube a Git: solo se versiona `.env.example`.

## Configuración

- **Límites mensuales por categoría**: se editan en `config/limites.php` (montos en USD, con punto decimal). Una categoría que no aparezca allí no tiene límite.
- **Umbral de "cerca del límite"**: constante `UMBRAL_CERCA` (80) en `src/Services/ResumenService.php`.
- **Zona horaria**: `America/Guayaquil`, definida en `config/contenedor.php`.

## Reglas de negocio

| Campo | Regla |
|---|---|
| Tipo | `ingreso` o `gasto` |
| Categoría | Ingreso: sueldo, extras u otros. Gasto: alimentación, transporte, vivienda, servicios, salud, educación, ocio u otros |
| Monto | Mayor que cero, con punto decimal, hasta 8 enteros y 2 decimales (`125.50`) |
| Descripción | Entre 3 y 255 caracteres |
| Fecha | Entre `2000-01-01` y `2100-12-31`; se permiten fechas futuras |

El resumen mensual cuenta **todo el mes en curso**, incluidos los movimientos fechados después de hoy. Un gasto que supera exactamente el límite queda en *cerca*; solo se marca *excedido* cuando lo sobrepasa.

## Arquitectura

Cada página de `public/` solo coordina: pide los servicios al contenedor, procesa la petición y elige la vista.

```
Navegador
   │  petición HTTP
   ▼
public/ (crear.php, listar.php)
   │
   ├─► MovimientoService ──► Reglas de validación ──► Movimiento (entidad)
   │          │
   │          └─► MovimientoRepositorio (interfaz) ──► MovimientoRepositorioPdo ──► MariaDB
   │
   ├─► ResumenService ──► MovimientoRepositorio ──► totales y límites
   │
   └─► Vista ──► plantillas (HTML con Html::e() en cada dato)
```

| Pieza | Responsabilidad |
|---|---|
| `config/contenedor.php` | Único lugar que ensambla los objetos concretos (inyección de dependencias) |
| `src/Database/ConexionFactory.php` | Construye el `PDO` con las opciones seguras |
| `src/Models/` | Entidad `Movimiento`, objeto de valor `Dinero` (centavos enteros y moneda), enums `Tipo`, `Categoria` y `NivelPresupuesto`, y los objetos del resumen |
| `src/Validation/` | Una regla por campo, todas con la misma interfaz (**Strategy**) |
| `src/Services/` | `MovimientoService` (**Facade** de registro y consulta) y `ResumenService` (totales y límites) |
| `src/Repositories/` | Interfaz del repositorio e implementación con PDO |
| `src/Views/` | Clase `Vista`, escape de salida centralizado y plantillas |

Principios aplicados: responsabilidad única, dependencia de interfaces (los servicios no conocen PDO), reglas nuevas sin modificar las existentes y objetos inmutables. El dinero se maneja en **centavos enteros** para que las sumas y comparaciones sean exactas.

## Seguridad

| Riesgo | Medida |
|---|---|
| Inyección SQL | Sentencias preparadas con PDO y preparación nativa (`ATTR_EMULATE_PREPARES => false`); el charset va en el DSN |
| XSS | Toda salida pasa por `Html::e()` (`htmlspecialchars` con `ENT_QUOTES` y UTF-8) |
| Datos manipulados | Validación en el servidor para todos los campos, aunque el navegador ya valide |
| Credenciales | En `.env`, fuera de Git; los errores se registran en el log y el usuario solo ve un mensaje genérico |
| Acceso directo por URL | `.htaccess` devuelve 403 para `config/`, `src/`, `vendor/`, `database/`, `.git/`, `.env` y los archivos de Composer |
| Privilegios | El usuario de la base de datos solo tiene `SELECT` e `INSERT` |
| Importes | Restricción `CHECK (monto > 0)` en la tabla, además de la validación en PHP |

Comprobación: abre `http://localhost/AI3_DisenoWeb_IsaacCanales/composer.json` o `.../config/conexion.php`; ambas deben responder **403 Forbidden**.

## Estructura del proyecto

```
.
├── .env.example
├── .htaccess
├── composer.json
├── config/
│   ├── conexion.php
│   ├── contenedor.php
│   └── limites.php
├── database/
│   └── schema.sql
├── public/
│   ├── index.php
│   ├── crear.php
│   ├── listar.php
│   └── assets/
│       ├── css/estilos.css
│       └── js/validacion.js
└── src/
    ├── Database/
    ├── Models/
    ├── Repositories/
    ├── Services/
    ├── Validation/
    └── Views/
        └── plantillas/
```

## Solución de problemas

| Síntoma | Causa y solución |
|---|---|
| `No se pudo conectar con el servicio de datos` | MySQL está apagado, o `.env` tiene datos incorrectos. Inicia MySQL en XAMPP y revisa `DB_PASS`. El detalle técnico queda en el log de errores de Apache |
| 404 al abrir la aplicación | La carpeta del proyecto debe llamarse `AI3_DisenoWeb_IsaacCanales` y estar en `htdocs`; la URL incluye `/public/` |
| `The zip extension and unzip/7z commands are both missing` | Activa `extension=zip` en `php.ini` y repite `composer install` |
| `Class "Ordenalo\..." not found` | Ejecuta `composer install`; cada clase debe estar en la ruta que indica su `namespace` |
| MySQL se apaga solo al arrancar | Suele ocurrir tras un apagado brusco de la PC. Antes de apagar, pulsa **Stop** en MySQL desde el panel de XAMPP |
| `composer.json` se puede abrir por URL | El `.htaccess` no se aplica: verifica `mod_rewrite` y `AllowOverride All` |

## Convención de commits

Commits atómicos y en español, con los prefijos `feat:`, `fix:`, `docs:`, `refactor:` y `chore:`.

## Mejoras futuras

- Protección CSRF en el formulario de registro.
- Entrada de montos tolerante, con formato (punto o coma decimal) elegido por el usuario.
- Persistir la moneda en la base de datos (hoy el objeto `Dinero` ya la maneja).
- Editar y eliminar movimientos, y elegir el mes a consultar.

## Autor y licencia

Isaac Canales ([@capizisaac](https://github.com/capizisaac)).
Todos los derechos reservados (licencia *proprietary*).