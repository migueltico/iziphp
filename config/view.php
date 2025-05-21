<?php namespace Config;
/**
* 
*/
class view
{
    private static $sections = [];
    private static $currentSectionName = null;

    public static function clearSections()
    {
        self::$sections = [];
        self::$currentSectionName = null;
    }

    public static function startSection(string $name)
    {
        if (self::$currentSectionName !== null) {
            // This means a section was started without ending the previous one.
            // It's good practice to handle this, e.g., by throwing an error or auto-ending.
            // For now, we'll log a warning and implicitly end the previous section.
            trigger_error("startSection('{$name}') called while section '" . self::$currentSectionName . "' was already active. Auto-ending previous section.", E_USER_WARNING);
            self::endSection(); // Attempt to clean up the previous section
        }
        self::$currentSectionName = $name;
        ob_start();
    }

    public static function endSection()
    {
        if (self::$currentSectionName === null) {
            if (ob_get_level() > 0) { 
                 ob_end_clean(); 
            }
            trigger_error("endSection() called without startSection() or section already ended.", E_USER_WARNING);
            return;
        }
        self::$sections[self::$currentSectionName] = ob_get_clean();
        self::$currentSectionName = null;
    }

    public static function renderSection(string $name, string $defaultContent = '')
    {
        echo self::$sections[$name] ?? $defaultContent;
    }

	/**
	 * Carga la vista seleccionada (sin template).
	 * @param  [String] $view_file_name_only [nombre del archivo que se cargara sin la palabra View ni extencion]
	 * @param  [array] $var  [Contiene las variables que se envien]
	 */
	public static function view($view_file_name_only, $var = NULL)
	{
		if ($var !== NULL) {
			foreach ($var as $key => $value) {
				$$key = $value;
			}
		}

		$view_file_path = VIEWS . $view_file_name_only .".php"; 
        if (is_readable($view_file_path)) {
    		include $view_file_path;
        } else {
            echo "<p>Error: View file '{$view_file_path}' not found.</p>";
        }
	}

	/**
	 * Carga una vista dentro de un template usando el sistema de secciones.
	 * @param  [String] $view_name_only [Nombre del archivo de la vista (sin .php) a cargar en la sección 'content']
	 * @param  [String] $template       [Nombre del archivo del template (sin .tpl.php)]
	 * @param  [array]  $var            [Variables para pasar a la vista]
	 */
	public static function viewT($view_name_only, $template, $var = NULL)
	{
        self::clearSections(); 

		if ($var !== NULL) {
			foreach ($var as $key => $value) {
				$$key = $value; 
			}
		}

        $view_file_path = VIEWS . $view_name_only . ".php"; 
        if (is_readable($view_file_path)) {
            ob_start();
            include $view_file_path; 
            self::$sections['content'] = ob_get_clean(); 
        } else {
            self::$sections['content'] = "<p>Error: View file '{$view_file_path}' not found.</p>";
        }

        $template_file_path = TEMPLATE . $template . ".tpl.php"; 
        if (is_readable($template_file_path)) {
            include $template_file_path;
        } else {
            echo "<p>Error: Template file '{$template_file_path}' not found.</p>";
            echo self::$sections['content'] ?? ''; 
        }
	}
}
?>
