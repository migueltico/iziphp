<?php  namespace rutas;

use Config\ruta;
// se verifica previamente que tipo de SOLICITUD se trata para no pasar por todas las demas rutas a verificar innecesariamente
if (ruta::get_method()) {
//RUTAS GET
	ruta::get('/', 'indexController@index');


}else if (!ruta::get_method()) {
//RUTAS POST
	
}

if (!ruta::get_State()) {
	ruta::error('Lo sentimos esta ruta no existe',"errorController","error");
}
?>