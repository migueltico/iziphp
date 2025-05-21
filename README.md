# <p style="text-align: center;"> GUIA RAPIDA </p>
---
# Instalacion

##### Requerimientos minimos

- PHP >= 7.0 (Recomendado 7.4+)
- PDO PHP Extension (si se usa la base de datos)
- Servidor web (Apache, Nginx) o el servidor de desarrollo de PHP.

## Servidor de Desarrollo PHP
Para un entorno de desarrollo rápido, puedes usar el servidor incorporado de PHP. Navega a la raíz de tu proyecto en la terminal y ejecuta:
```bash
php -S localhost:8080
```
Esto iniciará un servidor en `http://localhost:8080`. La raíz del proyecto (donde se encuentra `index.php`) será el directorio actual desde donde ejecutas el comando.

## Configuración para Servidores Web

### Friendly URLs / Pretty URLs
El framework está diseñado para usar URLs amigables (e.g., `/users/profile` en lugar de `/index.php?url=users/profile`), lo que mejora la estética de las URLs y la SEO. Para que esto funcione, tu servidor web necesita redirigir las solicitudes a `index.php`.

#### Apache
Si usas Apache, asegúrate de que `mod_rewrite` esté habilitado. El archivo `.htaccess` incluido en la raíz del proyecto ya contiene la configuración necesaria:
```apache
# Activa el motor de reescritura de URLs
RewriteEngine On

# Si el archivo o directorio solicitado no existe físicamente...
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
# ...redirige la solicitud a index.php
# La [L] significa que es la última regla a aplicar si esta coincide.
RewriteRule ^(.*)$ index.php [L]
```
Este bloque asegura que todas las solicitudes que no correspondan a un archivo o directorio físico sean manejadas por `index.php`, permitiendo que el router PHP (`Config\ruta`) procese la URL completa.

#### Nginx
Si usas Nginx, la configuración de tu servidor (generalmente en un archivo dentro de `/etc/nginx/sites-available/yourdomain.conf` o similar) debería incluir una sección `location /` para manejar el enrutamiento. Este archivo no es `.htaccess`.
```nginx
server {
    listen 80;
    server_name yourdomain.com; # Reemplaza con tu dominio
    root /path/to/your/project; # Reemplaza con la ruta raíz de tu proyecto

    index index.php;

    location / {
        # Intenta servir el archivo/directorio directamente.
        # Si no existe, pasa la solicitud a /index.php.
        # El router Config\ruta lee $_SERVER['REQUEST_URI'] directamente, por lo que no se necesitan $args.
        try_files $uri $uri/ /index.php;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        # Ejemplo: Ajusta a tu versión/socket de PHP-FPM
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock; # O tu versión, e.g., php7.4-fpm.sock
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; # A menudo necesario
    }

    # Deniega el acceso a archivos .htaccess si existen (buena práctica)
    location ~ /\.ht {
        deny all;
    }
}
```
Después de guardar los cambios en la configuración de Nginx, recuerda reiniciar o recargar el servicio (e.g., `sudo systemctl restart nginx` o `sudo systemctl reload nginx`).

---
# Enhanced Routing
Las rutas se definen en el archivo `./rutas/rutas.php`. El sistema de rutas (`Config\ruta`) ha sido mejorado para usar expresiones regulares (regex) internamente, lo que permite un emparejamiento de patrones más robusto y una extracción de parámetros flexible.

## Definiendo Rutas Básicas
Se utilizan los métodos estáticos `ruta::get()` para solicitudes GET y `ruta::post()` para solicitudes POST:
```php
use Config\ruta; // Asegúrate de incluir la clase ruta

// Ruta para una solicitud GET a la URL /path
// Se mapea al método 'methodName' del controlador 'ControllerName'
ruta::get('/path', 'ControllerName@methodName');

// Ruta para una solicitud POST a la URL /submit
// Se mapea al método 'handleSubmit' del controlador 'FormController'
ruta::post('/submit', 'FormController@handleSubmit');
```
- El primer parámetro es el patrón de la URL.
- El segundo parámetro es una cadena `NombreDelControlador@nombreDelMetodo`.

## Rutas con Parámetros
Puedes definir rutas que capturen segmentos de la URL como parámetros. Estos se indican con un prefijo de dos puntos (`:`).
```php
use Config\ruta;

// Captura un 'id' de usuario desde la URL
// Ejemplos: /users/123, /users/john-doe
ruta::get('/users/:id', 'UserController@show');

// Captura múltiples parámetros: 'year', 'month', y 'slug' para un post de blog
// Ejemplo: /posts/2023/11/mi-primer-post
ruta::get('/posts/:year/:month/:slug', 'PostController@view');
```
Los parámetros capturados se pasan como un array asociativo al primer argumento del método del controlador:
```php
<?php namespace controllers;

class UserController {
    // $params será ['id' => 'valor_capturado']
    public function show($params) {
        $userId = $params['id'];
        // ... lógica para mostrar el perfil ...
    }
}

class PostController {
    // $params será ['year' => '2023', 'month' => '11', 'slug' => 'mi-primer-post']
    public function view($params) {
        $year = $params['year'];
        $month = $params['month'];
        $slug = $params['slug'];
        // ... lógica para mostrar el post ...
    }
}
?>
```

---
# Middleware System
Middleware proporciona un mecanismo conveniente para filtrar las solicitudes HTTP que ingresan a tu aplicación. Puede ejecutar código antes de que una solicitud alcance el controlador (e.g., para autenticación o validación) o después de que el controlador genere una respuesta (e.g., para modificar la respuesta o logging).

## Creando Middleware
Un middleware es una clase PHP que implementa la interfaz `Middlewares\MiddlewareInterface`. Esta interfaz define un único método: `handle($request, callable $next)`.
Coloca tus clases de middleware en el directorio `middlewares/`. El autoloader está configurado para encontrarlas allí si usas el namespace `Middlewares`.
```php
<?php
namespace Middlewares;

// Asumiendo que MiddlewareInterface está correctamente definida en el namespace Middlewares
// use Middlewares\MiddlewareInterface; // No es necesario si está en el mismo namespace

class ExampleMiddleware implements MiddlewareInterface {
    public function handle($request, callable $next) {
        // Lógica ANTES de llamar al siguiente middleware o controlador
        // Ejemplo: Verificar autenticación, registrar la solicitud.
        // if (!isAuthenticated()) {
        //     // Detener la cadena y devolver una respuesta de error
        //     http_response_code(403);
        //     echo "Acceso denegado.";
        //     return; 
        // }

        $response = $next($request); // Procede al siguiente en la cadena

        // Lógica DESPUÉS de que el siguiente middleware/controlador haya corrido
        // Ejemplo: Modificar la respuesta, registrar la respuesta.
        // $response .= "<!-- Modificado por ExampleMiddleware -->";

        return $response;
    }
}
?>
```
- `$request`: Contiene los datos de la solicitud (actualmente, los parámetros de la URL).
- `$next`: El siguiente eslabón en la cadena de middleware/controlador.

## Registrando Middleware
Asigna middleware a las rutas directamente en `rutas/rutas.php`. Puedes usar el nombre de clase (si está en el namespace `Middlewares`) o el Fully Qualified Class Name (FQCN).
```php
use Config\ruta;

// Middleware único para una ruta
// El router buscará la clase Middlewares\ExampleMiddleware
ruta::get('/profile', 'UserController@profile', 'Middlewares\ExampleMiddleware');

// Múltiples middleware para una ruta (se ejecutan en orden)
// El router buscará Middlewares\LoggingMiddleware y Middlewares\AuthMiddleware
ruta::get('/settings', 'UserController@settings', ['Middlewares\LoggingMiddleware', 'Middlewares\AuthMiddleware']);
```

---
# Route Groups
Los grupos de rutas (`ruta::group()`) permiten aplicar atributos comunes como prefijos de URL y middleware a múltiples rutas de forma concisa.

## Definiendo Grupos
Utiliza el método estático `ruta::group(array $attributes, callable $callback)`:
```php
use Config\ruta;

// Grupo con prefijo de URL
ruta::group(['prefix' => '/admin'], function() {
    // Accesible en /admin/dashboard
    ruta::get('/dashboard', 'Admin\DashboardController@index');
    // Accesible en /admin/users
    ruta::get('/users', 'Admin\UserController@list');       
});

// Grupo con middleware común
// Todas las rutas aquí estarán protegidas por Middlewares\AdminAuthMiddleware
ruta::group(['middleware' => 'Middlewares\AdminAuthMiddleware'], function() {
    ruta::get('/reports', 'ReportController@view'); 
    ruta::post('/reports/generate', 'ReportController@generate');
});

// Grupo combinando prefijo y middleware
ruta::group(['prefix' => '/api/v1', 'middleware' => 'Middlewares\ApiAuthMiddleware'], function() {
    // Ruta: /api/v1/data
    // Middleware: Middlewares\ApiAuthMiddleware (del grupo)
    ruta::get('/data', 'ApiController@getData');

    // Ruta: /api/v1/data (POST)
    // Middleware: Middlewares\ApiAuthMiddleware (del grupo) Y Middlewares\SpecificValidationMiddleware (de la ruta)
    ruta::post('/data', 'ApiController@postData', 'Middlewares\SpecificValidationMiddleware'); 
});
```
- `$attributes`: Un array asociativo que puede contener:
    - `'prefix' (string)`: Un prefijo que se añadirá al inicio de todas las rutas definidas dentro del grupo.
    - `'middleware' (string|array)`: Uno o más middleware (nombres de clase o FQCN) que se aplicarán a todas las rutas del grupo. Estos se fusionan con cualquier middleware definido directamente en la ruta.
- `$callback`: Una función anónima (closure) donde defines las rutas que pertenecen al grupo.

---
# Controladores (Existente, Revisado)
Los controladores manejan la lógica de la aplicación. Se encuentran en `./controllers/`.
- El nombre del archivo debe coincidir con el nombre de la clase (e.g., `indexController.php` para `indexController`).
- Deben estar en el namespace `controllers` (o un sub-namespace como `controllers\admin`).
```php
<?php namespace controllers; // O controllers\admin

use Config\view; // Si usas el sistema de vistas

class indexController { // 'extends view' es opcional si no heredas de él
    public function index($params = []) { // $params es opcional, contiene parámetros de ruta
        // ... tu lógica ...
        view::viewT("index",'main'); // Ejemplo de carga de vista
    }
}
?>
```

---
# Vistas (Sección Existente)

Las vistas se cargan desde los controladores usando `use Config\view;` antes de la clase y junto con el nombre de la clase se coloca `extend view` como podemos ver en el ejemplo de arriba.
*(El resto de esta sección se mantiene como estaba en el README original, ya que no fue objeto de esta refactorización específica, pero la información sobre `view::view` y `view::viewT` sigue siendo relevante).*

#### Vista simple `view::view("$view",$var = NULL);`
En la carpeta proyecto `./config/config.php` podremos modificar las rutas por default en donde buscara los archivos de las vista usando las constantes globales `VIEW` y para template `TEMPLATE`.
En el primer parametro colocaremos la url de la vista que queremos usar (no se debe poner la extension ya que esta definida la de `php` por default). En el segundo parametro se coloca un `array` asociativo con los datos que quiere que la vista pueda utilizar.
```php
	public function index($var=null)
	{
    	$usuario = array(
    	    "nombre" =>"Pepito",
    	    "edad" =>"20",
    	    "pais" =>"Costa Rica",
    	    "email" =>"email@email.com",
    	    "idiomas" => array("español","ingles","frances","portugues")
    	);
		view::view("index",$usuario);
	}
```
En la funcion `view::view` recibira ese `array` y convertira todos los identificadores asociativos a variables `php`, que despúes podran ser utilizadas en las vistas en archivos `php` usando estas etiquetas `<?= ?>` o estas `<?php ?>` siempre que su configuracion de servidor lo permita.

#### Vista con template `view::viewT("$view","$template",$var = NULL);`
Al igual que la vista simple, la ruta por defecto de template se encuentra en el archivo de configuración en `./config/config.php`. El segundo parametro sera la ruta y nombre del template y de tercero los datos que se pasaran desde el controlador.
`¡IMPORTANTE!` Los archivos template por default deben terminar en `nombre.tpl.php`.

---
# Configuración (Sección Existente)
En la configuración (`./config/config.php`) podemos modificar los datos para las carpetas raiz de nuestras vistas y templates, y la configuracion de la base de datos.
```php
<?php 
define('DS',DIRECTORY_SEPARATOR);
define('ROOT',realpath(dirname(__FILE__)) . DS);
define('VIEWS','views/');
define('TEMPLATE','views/template/');
// ... otras constantes como DB_NAME, DB_HOST, etc. ...
?>
```

---
# Sesiones (Sección Existente)
Para poder usar las `session` de `php`, solo debemos colocarlas en el controlador que se requiera autenticar que la sesion este iniciada.
```php
<?php namespace controllers;
session_start(); // Importante: Iniciar sesión al principio si se usa
use Config\view;
// ...
class indexController extends view {
    // ...
}
?>
```
---
# Archivos estaticos (Sección Existente)
La carpeta por default donde se deben colocar los archivos como `*.css`, `*.js`, `*.png` o subcarpetas con dichos contenidos es la carpeta `public`. Esta carpeta puede ser reemplazada por la que gusten, pero es una convención común. Asegúrate de que tu servidor web esté configurado para servir archivos estáticos directamente desde esta carpeta.
