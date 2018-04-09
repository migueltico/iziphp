<?php namespace controllers;
session_start();
use Config\view;
use models\conexion;

class indexController extends view
{
	public function index($var=null)
	{
		$_SESSION['id']="aaaa";
		if (!empty($_SESSION['id'])) {
			$id ="miguel";
			$_SESSION['id']=$id;
			view::viewT("index",'main',array("nombre"=>$id));
		}else{
			view::viewT("index",'main',array("nombre"=>"No has iniciado sesion"));
		}
	}


	public function index2($var=null)
	{
	
		
		view::viewT("index",'main');
	}

}

 ?>