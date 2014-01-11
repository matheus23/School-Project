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
function oeffneBenutzerDB($nrt) {
	global $benutzerDB;
	$db = new ExtSQL($benutzerDB, $nrt);
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

function passwordHash($password) {
	$salt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
	$salt = base64_encode($salt);
	$salt = str_replace('+', '.', $salt);
	return crypt($password, '$2y$10$' . $salt . '$');
}

function passwordVerify($password, $actualPasswordHash) {
	return crypt($password, $actualPasswordHash) == $actualPasswordHash;
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
		$n = addslashes($this->nachricht);
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
	
	public function genJsCode() {
		$jsCode = $this->toJsCode();
		echo "<script type='text/javascript'>$jsCode</script>";
	}
}

//tabelleNeueSpalte erfüllt nur den Zweck fehler durch ungleiche Datenbanken zu verhindern
//$db = Rückgabewert von oeffneBenutzerDB
//$tabelle = Tabellenname z.B $tabelle="Benutzer"
//$spaltenname = einzufügende Spalte z.B $spaltenname="RegistrierungsID"
//$typ = SQL-Spalten-Typ z.B $typ="VARCHAR(40)" oder $typ="TEXT"
function tabelleNeueSpalte($db,$tabelle,$spaltenname,$typ){
	$spalteninfo = $db->query("SHOW COLUMNS FROM $tabelle");
	while($tabellenspalte = $spalteninfo->fetch_array()[0]){
		if ($spaltenname == $tabellenspalte) return;//Spalte existiert schon
	}
	$db->query("ALTER TABLE $tabelle ADD $spaltenname $typ");
}
?>