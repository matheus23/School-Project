<?php
//Diese Datei braucht kein HTML, wird nur für AJAX-Anfrage benutzt
session_start();
include "../utilities.php";
include_once "frontendUtilities.php";
debugModus();
$data = $_POST;
$nrt = new Nachrichten("#fehlerListe","../../");
if((!isset($_SESSION["semail"]))||($_SESSION["semail"]=="")){
	session_destroy();
	echo "interner Fehler";
	die();
}
if(!(new CSRFSchutz())->post()->pruefe()){//Übernimmt den CSRFToken aus den Post-Daten und überprüft ihn mit dem Token in der Session
	session_destroy();
	echo "interner Fehler";
	die();
}

if (alleSchluesselGesetzt($data, "aktion")) {
	switch ($data["aktion"]){
		case "neuerDateischluessel":
			neuerDateischluessel();
			break;
		case "schickeDateischluesselListe":
			schickeDateischluesselListe();
			break;
		case "neuerSignaturschluessel":
			neuerSignaturschluessel();
			break;
		case "aktuellerSignaturschluessel":
			aktuellerSignaturschluessel();
			break;
		case "loescheDateischluessel":
			loescheDateischluessel();
			break;
		case "aktuellerSignaturschluesselContainer":
			aktuellerSignaturschluesselContainer();
			break;
		case "holePrivaterDateischluessel":
			holePrivaterDateischluessel();
			break;
		case "holeSignaturschluessel":
			holeSignaturschluessel();
			break;
	}
}

function neuerDateischluessel(){
	global $data, $nrt;
	$semail = $_SESSION["semail"];
	$seid = $_SESSION["seid"];
	if (alleSchluesselGesetzt($data, "versionID","schluessel","privaterSchluessel")){
		$nutzerID = EmailZuNutzerID($semail,$nrt);
		$db = oeffneBenutzerDB($nrt);
		$versionID = $db->real_escape_string($data["versionID"]);
		$schluessel = $db->real_escape_string($data["schluessel"]);
		$privaterSchluessel = $db->real_escape_string($data["privaterSchluessel"]);
		$sql=//Fügt Schlüssel ein, bzw. updated ihn
			"INSERT INTO ".
			"`Dateischluessel`(`NutzerID`, `Schluessel`,`privaterSchluessel`, `VersionID`) ".
			"VALUES ($seid, '$schluessel','$privaterSchluessel', '$versionID')";
		$db->query($sql)->fold(
			function ($ergebnis) use(&$nrt,$db,$seid,$versionID){
				$sql=//Setzt den aktuellen Dateischlüssel (es gibt mehrere)
					"INSERT INTO ".
					"`aktuellerDateischluessel`(`NutzerID`, `VersionID`) ".
					"VALUES ($seid, '$versionID') ".
					"ON DUPLICATE KEY UPDATE ".
					"VersionID='$versionID'";
				$db->query($sql)->fold(
					function ($ergebnis){},
					function($fehlerNachricht) use (&$nrt) {
						$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
						echo json_encode(array("nrt"=>$nrt->toJsonUnencoded()));
						die();
					}
				);
				$nrt->okay("Schlüssel erfolgreich geändert");
				echo json_encode(array("nrt"=>$nrt->toJsonUnencoded()));
				die();
			},
			function($fehlerNachricht) use (&$nrt) {
				$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
				echo json_encode(array("nrt"=>$nrt->toJsonUnencoded()));
				die();
			}
		);
	}
}
function schickeDateischluesselListe(){
	global $data, $nrt;
	$semail = $_SESSION["semail"];
	$seid = $_SESSION["seid"];
	$db = oeffneBenutzerDB($nrt);
	//$html="";
	$schluesselArray=array();
	$sql=//case funktioniert nach dem "if"-Prinzip und überprüft anhand aktuellerDateischluessel.VersionID welcher Schlüssel der aktuellste ist
		"SELECT Dateischluessel.VersionID, (CASE WHEN Dateischluessel.VersionID=aktuellerDateischluessel.VersionID THEN 1 ELSE 0 END) as aktiv ".
		"FROM Dateischluessel,aktuellerDateischluessel WHERE Dateischluessel.NutzerID='$seid' AND Dateischluessel.NutzerID=aktuellerDateischluessel.NutzerID";
	$db->query($sql)->fold(
		function ($ergebnis) use (&$schluesselArray){
			while($schluessel = $ergebnis->fetch_array(MYSQLI_ASSOC)){
				array_push($schluesselArray,$schluessel);
			}
		},
		function($fehlerNachricht) use (&$nrt) {
			$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
		}
	);
	echo json_encode(array("nrt"=>$nrt->toJsonUnencoded(),"schluesselArray"=>$schluesselArray));
	die();
}

function neuerSignaturschluessel(){
	global $data, $nrt;
	$semail = $_SESSION["semail"];
	$seid = $_SESSION["seid"];
	if (alleSchluesselGesetzt($data, "versionID","schluessel","privaterSchluessel")){
		$db = oeffneBenutzerDB($nrt);
		$versionID = $db->real_escape_string($data["versionID"]);
		$schluessel = $db->real_escape_string($data["schluessel"]);
		$privaterSchluessel = $db->real_escape_string($data["privaterSchluessel"]);
		$sql=//Fügt Schlüssel ein, bzw. updated ihn
			"INSERT INTO ".
			"`Signaturschluessel`(`NutzerID`, `Schluessel`,`privaterSchluessel`, `VersionID`) ".
			"VALUES ($seid, '$schluessel','$privaterSchluessel', '$versionID') ".
			"ON DUPLICATE KEY UPDATE ".
			"Schluessel='$schluessel'," .
			"privaterSchluessel='$privaterSchluessel',".
			"VersionID='$versionID'";
		$db->query($sql)->fold(
			function ($ergebnis) use(&$nrt,$db){
				$nrt->okay("Schlüssel erfolgreich geändert");
				echo json_encode(array("nrt"=>$nrt->toJsonUnencoded()));
				die();
			},
			function($fehlerNachricht) use (&$nrt) {
				$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
				echo json_encode(array("nrt"=>$nrt->toJsonUnencoded()));
				die();
			}
		);
	}
}
function aktuellerSignaturschluessel(){
	global $data, $nrt;
	$semail = $_SESSION["semail"];
	$seid = $_SESSION["seid"];
	$db = oeffneBenutzerDB($nrt);
	//$html="";
	$versionID;
	$sql="SELECT VersionID FROM Signaturschluessel WHERE NutzerID='$seid'";
	$db->query($sql)->fold(
		function ($ergebnis) use (&$versionID){
				$schluessel = $ergebnis->fetch_array(MYSQLI_ASSOC);
				$versionID = $schluessel["VersionID"];
				//$html .= "<div class='listenelement' data-versionID='$versionID'><span class='listenlabel'>$versionID</span></div>";
		},
		function($fehlerNachricht){		
		}
	);
	echo json_encode(array("nrt"=>$nrt->toJsonUnencoded(),"versionID"=>$versionID));
	die();
}
function loescheDateischluessel(){
	global $data, $nrt;
	$semail = $_SESSION["semail"];
	$seid = $_SESSION["seid"];
	$db = oeffneBenutzerDB($nrt);
	if (alleSchluesselGesetzt($data, "versionID")){
		$versionID = $db->real_escape_string($data["versionID"]);
		$sql="DELETE FROM Dateischluessel WHERE NutzerID='$seid' AND VersionID='$versionID'";
		$db->query($sql)->fold(
			function ($ergebnis) use(&$nrt){
				echo json_encode(array("nrt"=>$nrt->toJsonUnencoded()));
				die();
			},
			function($fehlerNachricht) use (&$nrt) {
				$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
				echo json_encode(array("nrt"=>$nrt->toJsonUnencoded()));
				die();
			}
		);
	}
}
function aktuellerSignaturschluesselContainer(){
	global $data, $nrt;
	$semail = $_SESSION["semail"];
	$seid = $_SESSION["seid"];
	$db = oeffneBenutzerDB($nrt);
	$sql="SELECT VersionID,privaterSchluessel FROM Signaturschluessel WHERE NutzerID='$seid'";
	$schluesselContainer = $db->query($sql)->fold(
		function ($ergebnis){
				return $ergebnis->fetch_array(MYSQLI_ASSOC);
		},
		function($fehlerNachricht){
		}
	);
	echo json_encode(array("nrt"=>$nrt->toJsonUnencoded(),"schluesselContainer"=>$schluesselContainer));
	die();
}
function holePrivaterDateischluessel(){
	global $data, $nrt;
	$semail = $_SESSION["semail"];
	$seid = $_SESSION["seid"];
	$db = oeffneBenutzerDB($nrt);
	if (alleSchluesselGesetzt($data, "versionID")){
		$versionID = $db->real_escape_string($data["versionID"]);
		$sql="SELECT privaterSchluessel FROM Dateischluessel WHERE NutzerID='$seid' AND VersionID='$versionID'";
		$schluesselContainer = $db->query($sql)->fold(
			function ($ergebnis){
					return $ergebnis->fetch_array(MYSQLI_ASSOC);
			},
			function($fehlerNachricht){
			}
		);
		echo json_encode(array("nrt"=>$nrt->toJsonUnencoded(),"schluesselContainer"=>$schluesselContainer));
		die();
	}
}
function holeSignaturschluessel(){
	global $data, $nrt;
	if (alleSchluesselGesetzt($data, "nutzerID")) {
		$db = oeffneBenutzerDB($nrt);
		$nutzerID = $db->real_escape_string($data["nutzerID"]);
		$schluessel;
		$sql =
			"SELECT Schluessel ".
			"FROM Signaturschluessel ".
			"WHERE NutzerID='$nutzerID'";
		$db->query($sql)->fold(
			function ($ergebnis) use (&$schluessel){
				$schluessel = $ergebnis->fetch_array(MYSQLI_ASSOC);
				if(!$schluessel){
					$nrt->fehler("Keinen Signaturschlüssel gefunden");
					echo json_encode(array("nrt"=>$nrt->toJsonUnencoded()));
					die();
				}
			},
			function($fehlerNachricht) use (&$nrt) {
				$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
				echo json_encode(array("nrt"=>$nrt->toJsonUnencoded()));
				die();
			}
		);
		echo json_encode(array("schluessel"=>$schluessel,"nrt"=>$nrt->toJsonUnencoded()));
	}
}
?>