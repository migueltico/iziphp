 # <p style="text-align: center;"> GUIA RAPIDA </p>
 ---
# Rutas
Las rutas las podemos encontrar en las carpeta del proyecto `./rutas/rutas.php`, en este archivo se escriben las rutas que necesitara el proyecto. Podemos encontrar alli tanto las  **`ruta::post`** y las **`ruta::get`**, estas funciones reciben dos parametros:
 - La ruta
 - El controlador y su función.
 
#### La ruta
las **`ruta::get`**   y  **`ruta::post`** pueden recibir **URL´s** absolutas o con parametros.
Estos parametros se distinguira de las demás por `:` (dos puntos)
ejemplo:
   ```
    :id , :category
```
``` php
 ruta::get('/producto/:id/categoria/:category','productosController@product_by_category');
 ruta::post('/get/all/products','productosController@all_products');
```

Los parametros que se pasen por `url` seran recibidas por la variable que selecciones para tu `funcion` según el controlador correspondiente a la `url` que se registro en `ruta::get` esta sera un `array` asociativo con el nombre que se le asigno a cada una en la `url`

``` php
 ruta::get('/producto/:id/categoria/:category','productosController@product_by_category');
```


``` php
public function product_by_category($parameters=null)
{
	$datos = array("id" => $parameters['id'],"categoria" => $parameters['category']);
	view::view("producto_by_categoria",$datos);
}

```
 
#### El controlador y su función
como segundo parametro nos encontramos con el controlador al que se hara el llamado y a su función correspondiente; Esta misma esta dividida por un `@`, siendo el primero el nombre del controlador `nombreController` y el segundo el nombre de la función a ejecutar `mifuncion`, se veria asi  `nombreController@mifuncion`, el archivo controlador siempre debe terminar en `Controller` y respetando la mayúscula, ejemplo:
 - mainController.php
 - defaultController.php
 - firstController.php

> En las ruta no hay necesidad de escribir la extension del archivo php
---

# Error de ruta



Para los errores de URL's que no estan registradas en `./rutas/rutas.php` en este archivo de `rutas.php` al final del codigo encontraremos una condicional que verifica si hubo alguna coincidencia, si no manda a ejecutar la funcion `ruta::error` al cual se le pasa tres parametros `ruta::error($msj ,$controlador, $funcion)`.

El primer parametro recibe el mensaje personalizado que se pasara como datos a la vista del error, el segundo parametro es el nombre del controlador que sera llamado, en el cual estara lo que sera el tercer parametro, la funcion a ejecutar de el controlador. el parametro `$msj` es opcional, su uso es mas que todo para pruebas.

---
# Controladores

Los controladores se encuentran en la carpeta del proyecto `./controllers/` dentro del proyecto vacio encontraras una base del controlador por default para ayudarte a generar a partir de el, los tuyos. Estos controladores en sus clases, deben llamarse igual que el archivo sin la extension, y su namespace debe llamarse igual que la carpeta que los contiene.

Para poder usar otros controladores o librerias propias use el metodo de `php` `use` , por ejemplo:

``` php
<?php namespace controllers;
use Config\view;
use models\conexion;
class indexController extends view
{
	public function index($var=null)
	{
		view::viewT("index",'main');
	}
}
 ?>

```

---

# Vistas

Las vistas se cargan desde los controladores usando `use Config\view;` antes de la clase y junto con el nombre de la clase se coloca `extend view` como podemos ver en el ejemplo de arriba.

Una vez configurado, tenemos dos opciones para cargar vistas:
- Cargar vista simple
- Cargar vista desde un template

#### Vista simple `view::view("$view",$var = NULL);`

Esta función se encarga de cargar las vista sin nungun template de por medio, en la carpeta proyecto `./config/config.php` podremos modificar las rutas por default en donde buscara los archivos de las vista usando las constantes globales `VIEW` y para template `TEMPLATE`.

``` php
<?php 
define('DS',DIRECTORY_SEPARATOR);
define('ROOT',realpath(dirname(__FILE__)) . DS);
define('VIEWS','views/');
define('TEMPLATE','views/template/');
define('DB_NAME', 'prueba');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');

  ?>
```



En el primer parametro colocaremos la url de la vista que queremos usar (no se debe poner la extension ya que esta definida la de `php` por default). En el segundo parametro se coloca un `array` asociativo con los datos que quiere que la vista pueda utilizar.
``` php
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
En la funcion `view::view` recibira ese `array` y convertira todos los identificadores asociativos a variables `php`, que despúes podran ser utilizadas en las vistas en archivos `php` usando estas etiquetas `<?= ?>` o estas `<?php ?>` siempre que su configuracion de servidor lo permita

``` php
    <ul>
        <li> <?=$nombre ?> </li>
        <li> <?=$edad ?> </li>
        <li> <?=$pais ?> </li>
        <li> <?=$email ?> </li>
    </ul>
    <?php ?>
```
En el caso de el ejemplo anterior el item `idioma` es un array, en estos caso con un `foreach` o simplemente navegando por sus niveles llegaremos donde necesitamos.
``` php
    <h1>Mis idiomas</h1>
    <hr>
    <h2>Idioma principal</h2>
    <p><?=$idiomas['0']?></p>
    <h2>Otros Idiomas</h2>
    <ul>
    <?php foreach ($idiomas as $idioma):?>
        <li><?=$idioma?></li>
    <?php endforeach; ?>
    </ul>
```
También se podria usar la variable por default que obtiene la funcion `view::view` que se llama `$var`, en la variable encontraras cualquier dato que se haya enviado por medio de la función.


#### Vista con template `view::viewT("$view","$template",$var = NULL);`

Al igual que la vista simple, la ruta por defecto de template se encuentra en el archivo de configuración en `./config/config.php`, no hay grandes cambios al respecto de la vista simple, solo que en este caso el segundo parametro sera la ruta y nombre del template y de tercero los datos que se pasaran desde el controlador

`¡IMPORTANTE!` Los archivos template por default deben terminar en `nombre.tpl.php` esto para distinguirlo de archivos de vista normales en caso que no haya separacion por carpeta; Por default la carpeta para los template esta en `./views/template`, pero se puede cambiar desde la configuracion, pero no es necesario especificar el abreviado de `tpl` a la hora de escribir la ruta, porque en la funcion ya esta especificado el `tpl.php`.

Los datos pasados a la vista funcionan de la misma manera que la vista simple.

---
# Configuración

En la configuración podemos modificar los datos para las carpetas raiz de nuestras vistas y templates, y la configuracion de la base de datos
>La configuracion de la base de datos es opcional como su controlador en la carpeta models

``` php
<?php 
define('DS',DIRECTORY_SEPARATOR);
define('ROOT',realpath(dirname(__FILE__)) . DS);
define('VIEWS','views/');
define('TEMPLATE','views/template/');
define('DB_NAME', 'prueba');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');

 ?>
```

---
# Sesiones

Para poder usar las `session` de `php`, solo debemos colocarlas en el controlador que se requiera autenticar que la sesion este iniciada

``` php
<?php namespace controllers;
session_start();
use Config\view;
class indexController extends view
{
	public function go_dashboard($var=null)
	{
		
		if (!empty($_SESSION['id'])) {
			$id =$_SESSION['id'];
			view::viewT("dashboard",'main',array("id"=>$id));
		}else{
			view::viewT("login",'main',array("msg"=>"No has iniciado sesion"));
		}
	}
 ?>
```
---
# Archivos estaticos

La carpeta por default donde se deben colocar los archivos como `*.ccs`, `*.js`, `*.png` o subcarpetas con dichos contenidos es la carpeta `public`, esta carpeta puede ser reemplazada por la que gusten, no hay restricción.

---
# Como ejecutar

-Desde su gestor de base de datos, seleccione importar sql y seleccione `base.sql`, esto para las pruebas del proyecto IZIPHP, asi mismo para usar su configuracion default
debes configurar en tu `php.ini` el `PDO` quitandole el `;` quedaria asi:
```
extension=pdo_mysql
```


-IZIZPHP solo funciona desde un servidor `localhost` o un `VirtualHost` de apache en entorno de desarrollo
	*-No funcionara si tratas de ejecutarlo desde `"htdocs/micarpetaweb"` del xammp, pero si funcionara si el proyecto se ejecuta desde la raiz de `htdocs`
	*-para ejecutar un server php ingresa a la carpeta del proyecto y ejecuta desde un `cmd` o `git bash` este comando:
```
php -S localhost:80
```
o simplemente configura tu `Virtualhost` con apache, busca un tutorial  XD
[Virtual Host Guia](https://httpd.apache.org/docs/2.4/vhosts/examples.html)

