<?php namespace controllers;
// manda a llamar al controlador de vistas
use Config\view;
// manda a llamar al controlador de conexiones a bases de datos
use models\conexion;

// la calse debe llamarse igual que el controlador respetando mayusculas
class errorController extends view
{
	public function error($var=null)
	{
        view::viewT("error/error","main");
	}

}

 ?>