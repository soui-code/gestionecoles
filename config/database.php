<?php 
class Database{
	private $con;
	public function __construct(){
		try {
			$this->con = new PDO(
				"mysql:host=localhost;dbname=gestionecole;charset=utf8mb4", 
				"root", 
				"",
				[
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::ATTR_EMULATE_PREPARES => false
				]
			);
		} catch (PDOException $e) {
			die("Erreur de connexion à la base de données: " . $e->getMessage());
		}
	}
	public function getConnection(){
		return $this->con;
	}
}
 ?>