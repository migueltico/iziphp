<?php namespace Config;

/**
* 
*/
class ruta 
{
	public static $get_it =  false;
		/**
		* Obtiene el metodo usado POST o GET
		* @return [type] [description]
		*/
		public function get_State()
		{
			return self::$get_it;
		}
		public static function get_method()
		{
			$method = $_SERVER['REQUEST_METHOD'];
			if ($method == 'GET') {
				return true;
			}else if ($method == 'POST') {
				return false;			}
		}
		public static function post($route, $function)
		{
			$url = $_SERVER['REQUEST_URI'];
				//Verificar si la ruta actual es con o sin variables
			$url_type = ruta::url_type($route);
				//Verifica que la url sea igual a la url que se envio
			$check = ruta::Check_url($route, $url, $url_type);
				/// en caso que ambos esten comprobados
			if ($url_type == 'var' && $check) {
				$variables = ruta::get_params($route,$url);
				self::$get_it = true;
				$function = ruta::get_function($function);
				$mostrar = "controllers\\" . $function['controller'];
				$controller = new $mostrar;
				call_user_func_array(array($controller, $function['function']) , array($variables));

			}else if ($url_type == 'static' && $check) {

				$function = ruta::get_function($function);
				$mostrar = "controllers\\" . $function['controller'];
				$controller = new $mostrar;
				call_user_func(array($controller, $function['function']));
				self::$get_it = true;
			}
			
		}

		public static function get($route, $function){

			if (self::$get_it == false) {
				$url = $_SERVER['REQUEST_URI'];
				if ($url == "") {
					$url="/";
				}else{
					$url = rtrim($url,'/');
				}
				$url_type = ruta::url_type($route);
				$check = ruta::Check_url($route, $url, $url_type);
				if ($url_type == 'var' && $check) {
					$variables = ruta::get_params($route,$url);
					self::$get_it = true;
					$function = ruta::get_function($function);
					$mostrar = "controllers\\" . $function['controller'];
					$controller = new $mostrar;
					call_user_func_array(array($controller, $function['function']) , array($variables));
				
				}else if ($url_type == 'static' && $check) {
					
					$function = ruta::get_function($function);
					$mostrar = "controllers\\" . $function['controller'];
					$controller = new $mostrar;
					call_user_func(array($controller, $function['function']));
					self::$get_it = true;
				}
			}
		}

		private static function get_function($function){
			$function = explode("@", $function);
			$result = array(
				'controller' => $function[0],
				'function' => $function[1],
			);
			return $result;
		}

		private static function url_type($route){

			preg_match('/:/', $route, $matches);
			return (count($matches) !== 0 ? 'var':'static');
		}

		private static function Check_url($route, $url, $url_type){
			
			$routeFstatic = $route;
			$urlFstatic = $url;

			if ($url_type == 'var') {

				$url = rtrim($url,'/');
				$url = ltrim($url,'/');
				$route = ltrim($route,'/');
				$route = explode("/",$route);
		
				$url = rtrim($url,'/');
				$url = explode("/",$url);
				$res_valid_url = array_diff($route, $url);
				$res_valid_url2 = array_diff($url, $route);
				$staticSame = array_intersect($url, $route);
				$cu =count($route);

				for ($i = 0; $i < ($cu); $i++) { 

					$claveOnUrl = array_search($staticSame[$i], $url);
					$claveOnROUTE = array_search($staticSame[$i], $route);

					if ($claveOnUrl !== "" && $claveOnROUTE!=="") {
					
						if ($claveOnUrl !== $claveOnROUTE) {
							return false;
						}
					}
				}

				// if (in_array($staticSame, $res_valid_url2)) {
				$count = 0;
				foreach ($res_valid_url as $r) {
						// echo $r."<br>";
					$caracter = substr($r, 0, 1);
					if ($caracter !== ":") {
						$count++;
					}
					// }else{

					// 	$count++;
					// }
				}

				$r = count($route);
				$u = count($url);
				$res1 = ($count > 0 ?true : false);
				$res2 = ($u > $r ? true : false);
				$result = ($res1 and $res2 ? true : false);

				if ($u == $r) {

					$result = true;
				}else if ($u > $r){

					$result = false;
				}else{
					$result = false;

				}
				return ($result and $count == 0 ? true : false);
	
			}else if ($url_type == 'static') {

				$newURL = $urlFstatic;
				// $newURL = $_SERVER['REQUEST_URI'];
				$newROUTE = $routeFstatic;
				$newURL=($newURL == ""? $newURL = "/" : $newURL);
				// echo"<br>*************<STRONG style='color:orange;'> '$newURL'  -Vs-  '$newROUTE' </STRONG>*******************<br><pre>";
				$newURL = rtrim($newURL,'/');
				$newROUTE = rtrim($newROUTE,'/');
				$newURL = ltrim($newURL,'/');
				$newROUTE = ltrim($newROUTE,'/');
				// echo "<br><br>***************************FIN STATIC*************************************<br><br>";
				return($newURL == $newROUTE ? true : false);

			}
		}
		/**
		 * Obtiene las variables pasadas por url, verificando que coincidan las
		 * posiciones, las posiciones que no coincidad del route con la url
		 * estas deberian ser unicamente aquellas que son declaradas
		 * como variables en el route
		 * @param  String $route ruta que se paso en el metodo ruta::get
		 * @param  String $url   url actual
		 * @return Array  Retorna un arreglo con las variables de la url
		 */
		private static function get_params($route, $url){

			$url2 = $url;
			$route2 = $route;

			$route2 = explode("/", $route);
			$url2 = explode("/", $url);
			$route = explode("/", $route);
			$url = explode("/", $url);
			$res_get_route = array_diff($route,$url);
			$res_get_var = array_diff($url,$route);

			$var_items = array();
			$items = array();
			$items2 = array();
			$count = 0;
			foreach ($res_get_var as $key => $val) {
				$items2[] = $val;
			}

			foreach ($res_get_route as $array => $val){
				$val = ltrim($val,":");

				$items[$val] = $items2[$count];

				$count++;

			}
			// echo $items."<br>";
			return $items;

		}
		public static function error($mensaje = NULL, $controlador,$funcion)
		{
			// $function = ruta::get_function('error');
			$function=ruta::get_function('error');
			$mostrar = "controllers\\".$controlador;
			$controller = new $mostrar;
			$variables= array('error' => $mensaje);
			
			call_user_func(array($controller, $funcion) ,$variables);


			// echo "<style>
			// 	*{
			// 		margin:0;
			// 		padding:0;
			// 	}
			
			// </style>";
			// echo "<div style='display:flex;flex-direction:column;justify-content:center;align-items:center;height:100vh;background:#ada;color:white;font-size:2.5rem;padding:0 20px;'>
			// <h1>Url no existe</h1>
			// <p>Puedes configurar de manera directa este mensaje en: './config/ruta.php' en la linea 218 'funcion error' y escribir un echo con la informacion a mostrar(al igual que este mensaje); o tambien ahi mismo, habilitar el codigo comentado y generar un controlador para tus errores y manejos de views</p>
	
			// </div>";

		}
	}
	?>