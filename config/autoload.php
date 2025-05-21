<?php namespace Config;
	class Autoload	
	{
		public static function on()
		{
			spl_autoload_register(function($class_name)
			{
				// Convert namespace separators to directory separators
				$path_parts = explode('\\', $class_name);
				
				// The last part is the class file name; it should retain its case.
				$file_name = array_pop($path_parts) . '.php';
				
				// The rest are namespace parts, which can form the directory path (often lowercased).
				$directory_path = "";
				if (!empty($path_parts)) {
					$directory_path = strtolower(implode('/', $path_parts)) . '/';
				}
				
				$full_path = $directory_path . $file_name;

				// print "<br>--------------<br>". $full_path . "<br>---------------------<br>";
				if (is_readable($full_path)) {
					require_once $full_path;
				} else {
					// It's helpful to see what path was attempted, especially during debugging.
					// throw new \Exception("Error al cargar la clase: " . $class_name . " (intentado en: " . $full_path . ")");
					// For now, keeping the original error message style if preferred:
					throw new \Exception("Error al cargar la clase: " . $class_name);
				}
			});
		}

	}
	
?>