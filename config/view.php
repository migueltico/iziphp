<?php namespace Config;
/**
* 
*/
class view
{
	/**
	 * Carga la vista seleccionada | puede llevar variables con
	 * el identificador puesto en la ruta ejemplo:
	 * ruta preestablecida: tudominio/producto/:idproducto/:cantidad
	 * ruta Get:tudominio/producto/5/10
	 * variable creada: $idproducto = 5 / $cantidad = 10
	 * -----------------------------------------------------------------
	 * @param  [String] $vista [nombre del archivo que se cargara sin la palabra View ni extencion]
	 * -----------------------------------------------------------------
	 * @param  [String] $var  [Contiene las variables que se envien]
	 *
	 */
	public static function view($view,$var = NULL)
	{
		if ($var !== NULL) {
			
			// print_r($var);
			foreach ($var as $key => $value) {
				$$key = $value;
			}
		}

		$view_name= VIEWS .$view .".php";

		include $view_name;
	}
	public function viewT($view,$template,$var = NULL)
	{
			
	
		if ($var !== NULL) {

			foreach ($var as $key => $value) {
				$$key = $value;
			}
		}

		$view_name=TEMPLATE . $template . ".tpl.php";
		$view = $view . ".php";
		include($view_name);
	}

	public static function cleanUrl($url){
	$url=str_replace(array("¿","?",",",".","/",":","@","!","¡","=","'",'"',"#","$","%","&","(",")","'","°","|","[","]","{","}"),"",$url);
	$originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
    $modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
    $url = utf8_decode($url);
    $url = strtr($url, utf8_decode($originales), $modificadas);
   	$clear=str_replace(array("¿","",",",".","/",":","@","!","¡","=","'",'"',"#","$","%","&","(",")","'","°","|","[","]","{","}"),"",str_replace(" ","-",$url));
	$clear=strtolower($clear);
		return $clear;
	}
	public static function StringExplode($string,$delimiter,$indexId){
		$delimiter=trim($delimiter);
		$dato=explode($delimiter,$string);
		// print_r($dato);
		 return $dato[$indexId];

	}
	public static function cleanHtml($cadena){
		$end=false;
		$a = 0;
		$cadena = utf8_decode($cadena);
		$cadena = html_entity_decode($cadena); 
		$cadena=strip_tags($cadena);
		return  $cadena;
	
	}

}


?>

