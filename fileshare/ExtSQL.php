<meta charset="UTF-8" />
<?php
class ExtSQL extends MySQLi {
	function __construct($settings) {
		parent::__construct(
			$settings["host"], 
			$settings["user"], 
			$settings["password"], 
			$settings["database"]);
		parent::set_charset("utf8");
	}
}
?>