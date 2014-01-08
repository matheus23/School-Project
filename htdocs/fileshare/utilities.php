<?php

// Findet heraus, ob es bereits einen User mit der 
// gegebenen email gibt, der bereits registriert ist:
function userAlreadyExists($sql, $userEmail) {
	$rows = $sql->query("SELECT * FROM Benutzer WHERE Email = '$userEmail'");
	if ($sql->connect_errno) die($sql->error);
	return $rows->num_rows > 0;
}

// Öffnet die Datenbank für die Benutzer.
function openBenutzerDB() {
	$sql = new MySQLi("localhost", "schoolproject", "hallo123", "Fileshare");
	$sql->set_charset("utf-8");
	return $sql;
}

// Benutzung:
// $testArray = array("Hallo" => "Ja?", "Test" => "Blah");
// allKeysSet($testArray, "Hallo"); // Gibt False zurück
// allKeysSet($testArray, "Hallo", "Test"); // Gibt True zurück
// oder:
// allKeysSet($_POST, "user", "password");
// 
// Gibt True zurück, wenn alle gegebenen Keys im array $array 
// exestieren.
function allKeysSet($array) {
	$allargs = func_get_args();
	for ($i = 1; $i < count($allargs); $i++) {
		if (!isset($array[$allargs[$i]])) return false;
	}
	return true;
}

?>