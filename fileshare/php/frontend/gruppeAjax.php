<?php
//Diese Datei braucht kein HTML, wird nur für AJAX-Anfrage benutzt
session_start();
include "../utilities.php";
include_once "frontendUtilities.php";
debugModus();
$data = $_POST;
$nrt = new Nachrichten("fehlerListe","../../");
if((!isset($_SESSION["semail"]))||($_SESSION["semail"]=="")){
	$nrt->fehler("Session abgelaufen.");
	echo json_encode(array("nrt"=>$nrt->toJsCode()));
	die();
}

if (alleSchluesselGesetzt($data, "aktion")) {
	switch ($data["aktion"]){
		case "schickeNutzerEmail":
			schickeNutzerEmail();
			break;
		case "fertigGruppe":
			fertigGruppe();
			break;
		case "neueGruppe":
			neueGruppe();
			break;
		case "schickeMitglieder":
			schickeMitglieder();
			break;
		case "schickeGruppen":
			schickeGruppen();
			break;
	}
}

function schickeNutzerEmail(){
	global $data, $nrt;
	if (alleSchluesselGesetzt($data, "nameemail")) {
		$db = oeffneBenutzerDB($nrt);
		$nameemail=$db->real_escape_string($data["nameemail"]);
		$ergebnisArray = array();
		$db->query("SELECT Email, Nutzername FROM Benutzer WHERE Email='$nameemail' OR Nutzername='$nameemail'")->fold(
			function ($ergebnis) use (&$ergebnisArray){
				while($nutzer = $ergebnis->fetch_array(MYSQLI_ASSOC)){
					array_push($ergebnisArray,$nutzer);
				}
			},
			function($fehlerNachricht) use (&$nrt) {
				$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
			}
		);
		if(count($ergebnisArray)==0){
			$nrt->fehler("Keinen Nutzer mit der Email/dem Nutzernamen '$nameemail' gefunden.");
		}
		echo json_encode(array("nutzer"=>$ergebnisArray,"nrt"=>$nrt->toJsCode()));
	}
}


function fertigGruppe(){
	global $data, $nrt;
	$semail = $_SESSION["semail"];
	$erfolg=false;
	if (alleSchluesselGesetzt($data, "emails","gruppenname","GruppenID")){
		$db = oeffneBenutzerDB($nrt);
		$emails=json_decode($data["emails"]);
		$gruppenname=$db->real_escape_string($data["gruppenname"]);
		$gruppenID=$db->real_escape_string($data["GruppenID"]);
		
		if(strlen($gruppenname)==0){
			$nrt->fehler("Kein Gruppenname angegeben");
			echo json_encode(array("nrt"=>$nrt->toJsCode()));
			die();
		}
		$sql="SELECT ID FROM Gruppe WHERE Name='$gruppenname' AND ModeratorEmail='$semail' AND ID!='$gruppenID'";
		$db->query($sql)->fold(
			function ($ergebnis) use (&$nrt){
				if($ergebnis->num_rows>0){
					$nrt->fehler("Es gibt bereits eine Gruppe mit diesem Namen.");
					echo json_encode(array("nrt"=>$nrt->toJsCode()));
					die();
				}
			},
			function($fehlerNachricht) use (&$nrt) {
				$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
				echo json_encode(array("nrt"=>$nrt->toJsCode()));
				die();
			}
		);
		$erfolg=true;
		$sql="UPDATE Gruppe SET Name='$gruppenname' WHERE ID='$gruppenID'";
		$db->query($sql)->fold(
			function ($ergebnis) use (&$nrt){
			},
			function($fehlerNachricht) use (&$nrt,&$erfolg) {
				$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
				$erfolg=false;
			}
		);
		foreach($emails as &$email){
			$email = $db->real_escape_string($email);
		}
		$sql="SELECT NutzerEmail FROM Gruppenmitglieder WHERE GruppenID='$gruppenID'";	
		$bisherigeMitglieder=array();
		$db->query($sql)->fold(
			function ($ergebnis) use (&$bisherigeMitglieder){
				while($nutzer = $ergebnis->fetch_array(MYSQLI_ASSOC)){
					array_push($bisherigeMitglieder,$nutzer["NutzerEmail"]);
				}
			},
			function($fehlerNachricht) use (&$nrt,&$erfolg){
				$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
				$erfolg=false;
			}
		);
		$loeschen = array_diff($bisherigeMitglieder,$emails);
		$hinzufuegen = array_diff($emails,$bisherigeMitglieder);
		
		foreach($loeschen as $loeschMitglied){
			$sql="DELETE FROM Gruppenmitglieder WHERE GruppenID='$gruppenID' AND NutzerEmail='$loeschMitglied'";	
			$db->query($sql)->fold(
				function ($ergebnis){
				},
				function($fehlerNachricht) use (&$nrt,&$erfolg){
					$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
					$erfolg=false;
				}
			);
		}
		foreach($hinzufuegen as $hinzufuegenMitglied){
			$sql=
				"INSERT INTO ".
				"`Gruppenmitglieder`(`GruppenID`, `NutzerEmail`) ".
				"VALUES ('$gruppenID', '$hinzufuegenMitglied')";
			$db->query($sql)->fold(
				function ($ergebnis){
					
				},
				function($fehlerNachricht) use (&$nrt,&$erfolg){
					$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
					$erfolg=false;
				}
			);				
		}
	}
	if ($erfolg){
		$nrt->okay("Liste gespeichert");
	}
	echo json_encode(array("nrt"=>$nrt->toJsCode()));
}

function neueGruppe(){
	global $data;
	$nrt = new Nachrichten("fehlerListeGruppe","../../"); //Achtung: aneres Nachrichten-Feld
	$semail = $_SESSION["semail"];
	if (alleSchluesselGesetzt($data,"gruppenname")){	
		$db = oeffneBenutzerDB($nrt);
		$gruppenname=$db->real_escape_string($data["gruppenname"]);
		if(strlen($gruppenname)==0){
			$nrt->fehler("Kein Gruppenname angegeben");
			echo json_encode(array("nrt"=>$nrt->toJsCode()));
			die();
		}
		$sql="SELECT ID FROM Gruppe WHERE Name='$gruppenname' AND ModeratorEmail='$semail'";
		$db->query($sql)->fold(
			function ($ergebnis) use (&$nrt){
				if($ergebnis->num_rows>0){
					$nrt->fehler("Es gibt bereits eine Gruppe mit diesem Namen.");
					echo json_encode(array("nrt"=>$nrt->toJsCode()));
					die();
				}
			},
			function($fehlerNachricht) use (&$nrt) {
				$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
				echo json_encode(array("nrt"=>$nrt->toJsCode()));
				die();
			}
		);
		$sql=
			"INSERT INTO ".
			"`Gruppe`(`ID`, `Name`, `ModeratorEmail`) ".
			"VALUES (0, '$gruppenname', '$semail')"; 
		$db->query($sql)->fold(
			function ($ergebnis) use(&$nrt,$db){
				$nrt->okay("Gruppe erfolgreich hinzugefügt.");
				echo json_encode(array("nrt"=>$nrt->toJsCode(),"gruppenHTML"=>generateHTMLGruppen($db)));
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

function schickeMitglieder(){
	global $data,$nrt;
	if (alleSchluesselGesetzt($data, "GruppenID")) {
		$db = oeffneBenutzerDB($nrt);
		$gruppenID = $data["GruppenID"];
		echo generateHTMLMitglieder($db,$gruppenID);
	}
		
}
function schickeGruppen(){
	global $data,$nrt;
	$db = oeffneBenutzerDB($nrt);
	echo generateHTMLGruppen($db);
}
?>