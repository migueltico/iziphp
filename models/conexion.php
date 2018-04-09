<?php namespace models; 
use PDO;
use Config\Config;
class Conexion
{
	
	private $con;
	private $statement;
	public function __construct()
	{
		try{
			//$pdo = new PDO("mysql:host=". HOST . ";dbname=". DB . ";",USER,PASS);
			$this->con = new \PDO("mysql:host=". DB_HOST. ";dbname=" . DB_NAME . ";" , DB_USER, DB_PASS, array(PDO::ATTR_PERSISTENT => false, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

		}catch(\PDOException $e){
			echo 'Error al conectarse con la base de datos: ' . $e->getMessage();
			exit;
		}
	}
	function disconnect() {
		// $this->statement->closeCursor();
		$this->statement = null;
		$this->con = null;
	}


	//SIMPLE QUERY, NO RETORNA DATOS, SE PASAN LOS DATOS POR VARIABLE EN EL EXECUTE
	public function SQ($sql,$datos)
	{
		try {
			$statement = $this->con->prepare($sql);
			$statement->execute($datos);
			if (!$statement) {
				$stm =false;
			}else{
				$stm =true;
			}
			$estado = array('estado' => $stm,'error' => $statement->errorCode(),'errorMsg' => $statement->errorInfo());
			$return = $estado;
			return $return;
			
		} catch (PDOException $e) {
			return $e;
		}
		
	}
	//SIMPLE RETURN QUERY, SI RETORNA DATOS, LA PRIMERA FILA ENCONTRADA, SE PASAN LOS DATOS POR VARIABLE EN EL EXECUTE
	public function SRQ($sql,$datos)
	{
		
		$statement = $this->con->prepare($sql);
		$statement->execute($datos);
		if (!$statement) {
			$estado = array('estado' => false,'error' => $statement->errorCode(),'errorMsg' => $statement->errorInfo());
			$return = $estado;
			return $estado; 
		}
		$row = $statement->fetch(PDO::FETCH_ASSOC);
		$cuenta = $statement->rowCount();
		return $row ;

	}
	//MULTIPLE RETURN QUERY, SI RETORNA DATOS, TODAS FILA ENCONTRADA
	public function MRQ($sql)
	{
		try{
		$statement = $this->con->prepare($sql);
		$statement->execute();
		$row = $statement->fetchAll(PDO::FETCH_ASSOC);
		$cuenta = $statement->rowCount();
		if ($statement) {
			return $row;
		}

				
		} catch (Exception $e) {
			return $e;
		}
	}
		//MULTIPLE RETURN QUERY CON DATOS, SI RETORNA DATOS, TODAS FILA ENCONTRADA
	public function MRQD($sql, $data)
	{
		try{
		$statement = $this->con->prepare($sql);
		$statement->execute($data);
		$row = $statement->fetchAll(PDO::FETCH_ASSOC);
		$cuenta = $statement->rowCount();
			if ($statement) {
				return $row;
			}
				
		} catch (Exception $e) {
			return $e;
		}
	}
}


?>