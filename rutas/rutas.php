<?php  namespace rutas;

use Config\ruta;

//RUTAS GET
ruta::get('/', 'indexController@index');

//RUTAS POST
// Ejemplo: ruta::post('/submit-data', 'dataController@submit');

// Despachar la solicitud
ruta::dispatch();
?>