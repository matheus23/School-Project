<meta charset="UTF-8" />
<?php

// Findet heraus, ob es bereits einen User mit der 
// gegebenen email gibt, der bereits registriert ist:
function userExestiertBereits($db, $userEmail) {
	$rows = $sql->query("SELECT * FROM Benutzer WHERE Email = '$userEmail'");
	if ($sql->connect_errno) die($sql->error);
	return $rows->num_rows > 0;
}

// Öffnet die Datenbank für die Benutzer.
function öffneBenutzerDB() {
	$sql = new MySQLi("localhost", "schoolproject", "hallo123", "Fileshare");
	$sql->set_charset("utf-8");
	return $sql;
}

// Benutzung:
// $testArray = array("Hallo" => "Ja?", "Test" => "Blah");
// alleSchlüsselGesetzt($testArray, "Hallo"); // Gibt False zurück
// alleSchlüsselGesetzt($testArray, "Hallo", "Test"); // Gibt True zurück
// oder:
// alleSchlüsselGesetzt($_POST, "user", "password");
// 
// Gibt True zurück, wenn alle gegebenen Keys im array $array 
// exestieren.
function alleSchlüsselGesetzt($array) {
	$allargs = func_get_args();
	for ($i = 1; $i < count($allargs); $i++) {
		if (!isset($array[$allargs[$i]])) return false;
	}
	return true;
}

?>