<meta charset="UTF-8" />
<?php
include_once("Either.php");

class ExtSQL extends MySQLi {

	public function __construct($settings, &$nrt) {
		parent::__construct(
			$settings["host"], 
			$settings["user"], 
			$settings["password"], 
			$settings["database"]);
		parent::set_charset("utf8");
		if ($this->connect_errno) {
			$nrt->fehler("Konnte nicht zu Datenbanken zugreifen: " . $this->connect_error);
		}
	}
	
	// Returns Either<mysql_result, string>
	// Where Right is the error string, when failing
	public function query($sqlQuery) {
		$res = parent::query($sqlQuery);
		if (!$res) // Bei Fehler:
			return new Right($this->error);
		else // Bei Erfolg:
			return new Left($res);
	}
}
?>