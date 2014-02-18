<?php
require_once "ExtSQL.php";

#Serverweite Variablen START
define("host", $_SERVER["HTTP_HOST"]);//Sollte später durch hardcoded ersetzt werden
$url_folder = substr(substr($_SERVER["REQUEST_URI"],1), 0,
	strpos(substr($_SERVER["REQUEST_URI"],1), "/"));
define("githubdir", $url_folder);
define("rootdir", $_SERVER["DOCUMENT_ROOT"] . "/" . githubdir . "/");
#Serverweite Variablen ENDE

// Im debugModus gibt php Alle fehler an den Client weiter.
function debugModus() {
	error_reporting(E_ALL);
	ini_set('display_errors', true);
}

// Findet heraus, ob es bereits einen User mit der 
// gegebenen email gibt, der bereits registriert ist:
function userExestiertBereits($db, $userEmail) {
	return $db->query("SELECT * FROM Benutzer WHERE Email = '$userEmail'")->fold(
		function($rows) {
			return $rows->num_rows > 0;
		}, function($fehlerNachricht) {
			return true;
		}
	);
}

$benutzerDB = array(
	"database" => "Fileshare",
	"host" => "localhost",
	"user" => "schoolproject",
	"password" => "hallo123"
);

// Öffnet die Datenbank für die Benutzer.
function oeffneBenutzerDB($nrt) {
	global $benutzerDB;
	$db = new ExtSQL($benutzerDB, $nrt);
	return $db;
}

define("PASSWORD_PASS", 0);
define("WRONG_EMAIL", 1);
define("WRONG_COMBINATION", 2);
// Testet das Passwort für einen Benutzer:
// PASSWORD_PASS, (also 0) wenn das Passwort stimmt.
// WRONG_EMAIL, (also 1) wenn es die Email nicht gibt.
// WRONG_COMBINATION, (also 2) wenn es die Email gibt, aber das Passwort dazu nicht stimmt.
function benutzerPwTest($db, $email, $passwort) {
	return $db->query("SELECT Passwort FROM Benutzer WHERE Email = '$email'")->fold(
		function ($ergebnis) use ($email, $passwort) {
			if ($ergebnis) {
				if (!$ergebnis->data_seek(0)) {
					return WRONG_EMAIL;
				} else {
					$ergebnisZeile = $ergebnis->fetch_array();
					$pwHash = $ergebnisZeile[0];
					$pwPasst = passwordVerify($passwort, $pwHash);
					if ($pwPasst) return PASSWORD_PASS;
					else return WRONG_COMBINATION;
				}
			} // Bei datenbank-fehler:
		}, function ($fehlerNachricht) {
			// Hmmmm naja...
			// Sollte _eigentlich_ einen fehler Ausgeben...
			return WRONG_COMBINATION;
		});
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

function orDefault($array, $index, $default) {
	if (isset($array[$index])) {
		return $array[$index];
	} else {
		return $default;
	}
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
	public $path = "../";
	
	public function __construct($nachricht, $art,$path) {
		$this->nachricht = $nachricht;
		$this->art = $art;
		$this->path = $path;
	}
	
	public function toJsonUnencoded() {
		$n = addslashes($this->nachricht);
		$a = $this->art;
		$path = $this->path;
		return array("typ" => $a, "nachricht" => $n);
	}
	
	public function toJson() {
		return json_encode($this->toJsonUnencoded());
	}
}

class Nachrichten {
	public $jsElemQuery = "";
	public $nachrichtenListe = array();
	
	public function __construct($jselem,$path="../") {
		$this->jsElemQuery = $jselem;
		$this->path = $path;
	}
	
	public function nachricht($art, $nachricht) {
		array_push($this->nachrichtenListe, new Nachricht($nachricht, $art, $this->path));
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
	
	public function toJsonUnencoded() {
		$nachrichten = $this->nachrichtenListe;
		
		$jsonNrtArray = array();
		foreach ($nachrichten as $nachricht) {
			array_push($jsonNrtArray, $nachricht->toJsonUnencoded());
		}
		return array("liste" => $jsonNrtArray, "path" => $this->path);
	}
	
	public function toJson() {
		return json_encode($this->toJsonUnencoded());
	}
	
	public function toJsCode() {
		$elem = $this->jsElemQuery;
		$json = $this->toJson();
		return "fehlerNachrichten(\$('$elem'), $json);";
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
	$db->query("SHOW COLUMNS FROM $tabelle")->fold(
		function($ergebnis) use ($tabelle,$spaltenname,$typ){
			$spalteninfo = $ergebnis;
			while($tabellenspalte = $spalteninfo->fetch_array()[0]){
				if ($spaltenname == $tabellenspalte) return;//Spalte existiert schon
			}
			$db->query("ALTER TABLE $tabelle ADD $spaltenname $typ");			
		},
		function($fehlerNachricht){
		}
	);
}

function zufallPasswort(){
	$passwort = substr(str_shuffle('abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'),0,10);
	$passwort .= substr(str_shuffle('!%&=+*#_'),0,2);
	return str_shuffle($passwort);
}

//Gibt eine ID zurück, die sowohl einzigartig ist, als auch nicht vorhersehbar
//$laengeZufall ist nicht die Länge der ID, sondern die Länge der angehängten Zufallsdaten
function sichereID($praefix="",$laengeZufall=20){
	$bytes = ceil($laengeZufall/4) * 3;//Base64 3 Bytes => 4 Zeichen
	$zufallString = base64_encode(openssl_random_pseudo_bytes($bytes));
	$zufallString = substr($zufallString,0,$laengeZufall);
	$zufallString = md5(microtime()).$zufallString;
	return $praefix.$zufallString;
}
?>