<meta charset="UTF-8" />
<?php
class ExtSQL extends MySQLi {
	function ExtSQL($settings) {
		parent::__construct(
			$settings["host"], 
			$settings["user"], 
			$settings["password"], 
			$settings["database"]);
	}
}
?>