<?php

function userAlreadyExists($sql, $userEmail) {
	$rows = $sql->query("SELECT * FROM Benutzer WHERE Email = '$userEmail'");
	if ($sql->connect_errno) die($sql->error);
	return $rows->num_rows > 0;
}

function openDB() {
	$sql = new MySQLi("localhost", "schoolproject", "hallo123", "Fileshare");
	$sql->set_charset("utf-8");
	return $sql;
}

function allKeysSet($array) {
	$allargs = func_get_args();
	for ($i = 1; $i < count($allargs); $i++) {
		if (!isset($array[$allargs[$i]])) return false;
	}
	return true;
}

?>