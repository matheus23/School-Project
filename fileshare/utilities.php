<meta charset="UTF-8" />
<?php
include "ExtSQL.php";

// Im debugModus gibt php Alle fehler an den Client weiter.
function debugModus() {
	error_reporting(E_ALL);
	ini_set('display_errors', true);
}

// Findet heraus, ob es bereits einen User mit der 
// gegebenen email gibt, der bereits registriert ist:
function userExestiertBereits($db, $userEmail) {
	$rows = $db->query("SELECT * FROM Benutzer WHERE Email = '$userEmail'");
	if ($db->connect_errno) return true; // Zeitweise...
	return $rows->num_rows > 0;
}

$benutzerDB = array(
	"host" => "localhost",
	"user" => "schoolproject",
	"password" => "hallo123",
	"database" => "Fileshare"
);

// Öffnet die Datenbank für die Benutzer.
function oeffneBenutzerDB() {
	global $benutzerDB;
	$db = new ExtSQL($benutzerDB);
	$db->set_charset("utf-8");
	return $db;
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
function alleSchluesselGesetzt($array) {
	$allargs = func_get_args();
	for ($i = 1; $i < count($allargs); $i++) {
		if (!isset($array[$allargs[$i]])) return false;
	}
	return true;
}

// Prüft, ob es einen SQL Fehler gab, gibt gegebenerweise eine
// $nachricht an, und falls gewollt, den fehlercode dazu.
// Beispiele:
// prüfeSQLFehler($db, "Ein interner Fehler ist aufgetreten...");
// prüfeSQLFehler($db, "Fehler:", True);
function pruefeSQLFehler($db, $nachricht, $gibFehlercodeAus = False) {
	if ($db->connect_errno) {
		$fehlernachricht = $gibFehlercodeAus ? " (" . $db->error . ")" : "";
		die($nachricht . $fehlernachricht);
		return true;
	}
	return false;
}

?>