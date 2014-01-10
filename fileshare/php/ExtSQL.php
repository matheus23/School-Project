<meta charset="UTF-8" />
<?php
class ExtSQL extends MySQLi {
	public $nrt;
	public function __construct($settings, & $errormsgs) {
		parent::__construct(
			$settings["host"], 
			$settings["user"], 
			$settings["password"], 
			$settings["database"]);
		$this->nrt =& $errormsgs;
		parent::set_charset("utf8");
		if ($this->connect_errno) {
			$nrt->fehler("Konnte nicht zu Datenbanken zugreifen: " . $this->connect_error);
		}
	}
	
	public function query($sqlQuery) {
		$res = parent::query($sqlQuery);
		if (!$res) {
			$this->nrt->fehler("Fehler bei SQL-Query: " . $this->error);
		}
		return $res;
	}
}
?>