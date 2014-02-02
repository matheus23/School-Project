<?php
//Diese Datei braucht kein HTML, wird nur für AJAX-Anfrage benutzt
session_start();
include "../utilities.php";
include_once "frontendUtilities.php";
debugModus();
$data = $_POST;
$nrt = new Nachrichten("fehlerListe","../../");
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
		case "aktuellerDateischluessel":
			aktuellerDateischluessel();
			break;
		case "neuerSignaturschluessel":
			neuerSignaturschluessel();
			break;
		case "aktuellerSignaturschluessel":
			aktuellerSignaturschluessel();
			break;
	}
}

function neuerDateischluessel(){
	global $data, $nrt;
	$semail = $_SESSION["semail"];
	if (alleSchluesselGesetzt($data, "versionID","schluessel")){
		$nutzerID = EmailZuNutzerID($semail,$nrt);
		$db = oeffneBenutzerDB($nrt);
		$versionID = $db->real_escape_string($data["versionID"]);
		$schluessel = $db->real_escape_string($data["schluessel"]);
		$sql=//Fügt Schlüssel ein, bzw. updated ihn
			"INSERT INTO ".
			"`Dateischluessel`(`NutzerID`, `Schluessel`, `VersionID`) ".
			"VALUES ($nutzerID, '$schluessel', '$versionID')".
			"ON DUPLICATE KEY UPDATE ".
			"Schluessel='$schluessel'," .
			"VersionID='$versionID'";
		$db->query($sql)->fold(
			function ($ergebnis) use(&$nrt,$db){
				$nrt->okay("Schlüssel erfolgreich geändert");
				echo json_encode(array("nrt"=>$nrt->toJsCode()));
				die();
			},
			function($fehlerNachricht) use (&$nrt) {
				$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
				echo json_encode(array("nrt"=>$nrt->toJsCode()));
				die();
			}
		);
	}
}
function aktuellerDateischluessel(){
	global $data, $nrt;
	$semail = $_SESSION["semail"];
	$db = oeffneBenutzerDB($nrt);
	$nutzerID = EmailZuNutzerID($semail,$nrt);
	//$html="";
	$versionID;
	$sql="SELECT VersionID FROM Dateischluessel WHERE NutzerID='$nutzerID'";
	$db->query($sql)->fold(
		function ($ergebnis) use (&$versionID){
				$schluessel = $ergebnis->fetch_array(MYSQLI_ASSOC);
				$versionID = $schluessel["VersionID"];
				//$html .= "<div class='listenelement' data-versionID='$versionID'><span class='listenlabel'>$versionID</span></div>";
		},
		function($fehlerNachricht){		
		}
	);
	echo json_encode(array("nrt"=>$nrt->toJsCode(),"versionID"=>$versionID));
	die();
}

function neuerSignaturschluessel(){
	global $data, $nrt;
	$semail = $_SESSION["semail"];
	if (alleSchluesselGesetzt($data, "versionID","schluessel")){
		$nutzerID = EmailZuNutzerID($semail,$nrt);
		$db = oeffneBenutzerDB($nrt);
		$versionID = $db->real_escape_string($data["versionID"]);
		$schluessel = $db->real_escape_string($data["schluessel"]);
		$sql=//Fügt Schlüssel ein, bzw. updated ihn
			"INSERT INTO ".
			"`Signaturschluessel`(`NutzerID`, `Schluessel`, `VersionID`) ".
			"VALUES ($nutzerID, '$schluessel', '$versionID')".
			"ON DUPLICATE KEY UPDATE ".
			"Schluessel='$schluessel'," .
			"VersionID='$versionID'";
		$db->query($sql)->fold(
			function ($ergebnis) use(&$nrt,$db){
				$nrt->okay("Schlüssel erfolgreich geändert");
				echo json_encode(array("nrt"=>$nrt->toJsCode()));
				die();
			},
			function($fehlerNachricht) use (&$nrt) {
				$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
				echo json_encode(array("nrt"=>$nrt->toJsCode()));
				die();
			}
		);
	}
}
function aktuellerSignaturschluessel(){
	global $data, $nrt;
	$semail = $_SESSION["semail"];
	$db = oeffneBenutzerDB($nrt);
	$nutzerID = EmailZuNutzerID($semail,$nrt);
	//$html="";
	$versionID;
	$sql="SELECT VersionID FROM Signaturschluessel WHERE NutzerID='$nutzerID'";
	$db->query($sql)->fold(
		function ($ergebnis) use (&$versionID){
				$schluessel = $ergebnis->fetch_array(MYSQLI_ASSOC);
				$versionID = $schluessel["VersionID"];
				//$html .= "<div class='listenelement' data-versionID='$versionID'><span class='listenlabel'>$versionID</span></div>";
		},
		function($fehlerNachricht){		
		}
	);
	echo json_encode(array("nrt"=>$nrt->toJsCode(),"versionID"=>$versionID));
	die();
}
?>