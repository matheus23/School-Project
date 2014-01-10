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

function jsFuerFehlerListe($element, $nachrichtenArray) {
	$jsAufrufe = "";
	foreach ($nachrichtenArray as $nachricht) {
		$jsAufrufe .= "fehlerNachricht($element, '$nachricht');\n";
	}
	return $jsAufrufe;
}

class Nachricht {
	public $nachricht = "";
	public $art = "fehler";
	
	public function __construct($nachricht, $art) {
		$this->nachricht = $nachricht;
		$this->art = $art;
	}
	
	public function toJsCall($elementVar) {
		$n = $this->nachricht;
		$a = $this->art;
		return "fehlerNachricht($elementVar, '$n', '$a');";
	}
}

class Nachrichten {
	public $jsElemVarDef = "";
	public $jsElemVarName = "";
	public $nachrichtenListe = array();
	
	public function __construct($jselem) {
		$this->jsElemVarName = "__$jselem";
		$this->jsElemVarDef = "var " . $this->jsElemVarName . " = document.getElementById('$jselem');";
	}
	
	public function nachricht($art, $nachricht) {
		array_push($this->nachrichtenListe, new Nachricht($nachricht, $art));
	}
	
	public function okay($nachricht) {
		$this->nachricht("okay", $nachricht);
	}
	
	public function warnung($nachricht) {
		$this->nachricht("warnung", $nachricht);
	}
	
	public function fehler($nachricht) {
		$this->nachricht("fehler", $nachricht);
	}
	
	public function toJsCode() {
		$code = $this->jsElemVarDef . "\n";
		$nachrichten = $this->nachrichtenListe;
		foreach ($nachrichten as $nachricht) {
			$code .= $nachricht->toJsCall($this->jsElemVarName) . "\n";
		}
		return $code;
	}
}

?>