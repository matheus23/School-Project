<?php
//Diese Datei braucht kein HTML, wird nur für AJAX-Anfrage benutzt
session_start();
include "../utilities.php";
debugModus();
include_once "frontendUtilities.php";

$data = $_POST;
$nrt = new Nachrichten("fehlerListe","../../");
$nrtGruppe = new Nachrichten("fehlerListeGruppe","../../");

if(!istAngemeldet()){
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
		case "benutzerInformation":
			benutzerInformation();
			break;
		case "ladeHoch":
			ladeHoch();
			break;
		case "holeDateien":
			holeDateien();
			break;
		case "frageNachURL":
			frageNachURL();
			break;
	}
}

function benutzerInformation(){
	global $data, $nrt;
	if (alleSchluesselGesetzt($data, "email")) {
		$db = oeffneBenutzerDB($nrt);
		$email = $db->real_escape_string($data["email"]);
		$info;
		$sql =
			"SELECT Benutzer.ID,Dateischluessel.Schluessel,Dateischluessel.VersionID ".
			"FROM Benutzer,Dateischluessel,aktuellerDateischluessel ".
			"WHERE Benutzer.Email='$email' AND Benutzer.ID=Dateischluessel.NutzerID AND Dateischluessel.NutzerID=aktuellerDateischluessel.NutzerID AND Dateischluessel.VersionID=aktuellerDateischluessel.VersionID";
		$db->query($sql)->fold(
			function ($ergebnis) use (&$info){
				$info = $ergebnis->fetch_array(MYSQLI_ASSOC);
			},
			function($fehlerNachricht) use (&$nrt) {
				$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
			}
		);
		echo json_encode(array("info"=>$info,"nrt"=>$nrt->toJsonUnencoded()));
	}
}
function ladeHoch(){
	global $data, $nrt;
	$semail = $_SESSION["semail"];
	if (alleSchluesselGesetzt($data, "datei","dateiname","nutzerID","versionID","zugriff")) {
		$db = oeffneBenutzerDB($nrt);
		$ersteller = EmailZuNutzerID($semail,$nrt);
		$dateiname = $db->real_escape_string($data["dateiname"]);
		$nutzerID = $db->real_escape_string($data["nutzerID"]);
		$versionID = $db->real_escape_string($data["versionID"]);
		$zugriff = $db->real_escape_string($data["zugriff"]);
		$sql=
				"INSERT INTO ".
				"`Datei`(`ID`, `Name`,`SchluesselID`,`Ersteller`,`Zugriff`) ".
				"VALUES (0, '$dateiname','$versionID',$ersteller,$zugriff)";
		$db->query($sql)->fold(
			function ($ergebnis) use (&$nrt,$db,$data){
				$schreiben = file_put_contents("/home/www-data/dateien/".$db->insert_id,$data["datei"]);
				if(!$schreiben){
					$nrt->fehler("Es gab einen Fehler beim Schreiben am Server");
				}
				else{
					$nrt->okay("Datei hochgeldaden");
				}
			},
			function($fehlerNachricht) use (&$nrt) {
				$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
			}
		);
		echo json_encode(array("nrt"=>$nrt->toJsonUnencoded()));
	}
}

function holeDateien(){
	global $data, $nrt;
	$semail = $_SESSION["semail"];
	$nutzerID = EmailZuNutzerID($semail,$nrt);
	$db = oeffneBenutzerDB($nrt);
	$dateiArray=array();
	$sql =
		"SELECT Datei.Name, Datei.ID, Datei.SchluesselID, Benutzer.Nutzername,Benutzer.ID as 'nutzerID' ".
		"FROM Benutzer,Datei ".
		"WHERE Datei.Zugriff='$nutzerID' AND Datei.Ersteller=Benutzer.ID";
	$db->query($sql)->fold(
		function ($ergebnis) use (&$dateiArray){
			while($datei = $ergebnis->fetch_array(MYSQLI_ASSOC)){
				array_push($dateiArray,$datei);
			}
		},
		function($fehlerNachricht) use (&$nrt) {
			$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
		}
	);
	echo json_encode(array("dateien"=>$dateiArray,"nrt"=>$nrt->toJsonUnencoded()));
}

function frageNachURL(){
	global $data, $nrt;
	$semail = $_SESSION["semail"];
	if (alleSchluesselGesetzt($data, "ausgewaehltID")) {
		$nutzerID = EmailZuNutzerID($semail,$nrt);
		$db = oeffneBenutzerDB($nrt);
		$ausgewaehltID = $db->real_escape_string($data["ausgewaehltID"]);
		$url;
		$sql =
			"SELECT Datei.ID ".
			"FROM Benutzer,Datei ".
			"WHERE Datei.Zugriff='$nutzerID' AND Datei.ID='$ausgewaehltID'";
		$db->query($sql)->fold(
			function ($ergebnis) use (&$url){
				$datei = $ergebnis->fetch_array(MYSQLI_ASSOC);
				$dateiname = str_replace(array("/","+"),"",sichereID("datei_",10));
				$pfad = "/var/www/download_tmp/$dateiname";
				$kopieren = copy("/home/www-data/dateien/".$datei["ID"],$pfad);
				if(!$kopieren){
					$nrt->fehler("Es gab einen Fehler beim Zugänglichmachen der Datei");
				}
				else{
					$url = "http://".host."/download_tmp/$dateiname";//Muss später in https geändert werden
				}
			},
			function($fehlerNachricht) use (&$nrt) {
				$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
			}
		);
		echo json_encode(array("url"=>$url,"nrt"=>$nrt->toJsonUnencoded()));
	}
}
?>