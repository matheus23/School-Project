<?php
//Diese Datei braucht kein HTML, wird nur für AJAX-Anfrage benutzt
session_start();
include "../utilities.php";
include_once "frontendUtilities.php";
debugModus();
$data = $_POST;
$nrt = new Nachrichten("fehlerListe","../../");
$nrtGruppe = new Nachrichten("fehlerListeGruppe","../../");
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
		case "schickeNutzerID":
			schickeNutzerID();
			break;
		case "fertigGruppe":
			fertigGruppe();
			break;
		case "neueGruppe":
			neueGruppe();
			break;
		case "schickeMitglieder":
			echo schickeMitglieder();
			break;
		case "schickeGruppen":
			echo schickeGruppen();
			break;
		case "loescheGruppe":
			loescheGruppe();
			break;
	}
}

function schickeNutzerID(){
	global $data, $nrt;
	if (alleSchluesselGesetzt($data, "nameemail")) {
		$db = oeffneBenutzerDB($nrt);
		$nameemail=$db->real_escape_string($data["nameemail"]);
		$ergebnisArray = array();
		$db->query("SELECT Email, Nutzername, ID FROM Benutzer WHERE Email='$nameemail' OR Nutzername='$nameemail'")->fold(
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
	$seid = $_SESSION["seid"];
	$erfolg=false;
	if (alleSchluesselGesetzt($data, "nutzerIDs","gruppenname","GruppenID")){
		$db = oeffneBenutzerDB($nrt);
		$nutzerIDs=json_decode($data["nutzerIDs"]);
		$gruppenname=$db->real_escape_string($data["gruppenname"]);
		$gruppenID=$db->real_escape_string($data["GruppenID"]);
		
		if(strlen($gruppenname)==0){
			$nrt->fehler("Kein Gruppenname angegeben");
			echo json_encode(array("nrt"=>$nrt->toJsCode()));
			die();
		}
		$sql="SELECT ID FROM Gruppe WHERE Name='$gruppenname' AND ModeratorID='$seid' AND ID!='$gruppenID'";
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
		foreach($nutzerIDs as &$nutzerID){
			$nutzerID = $db->real_escape_string($nutzerID);
		}
		$sql="SELECT NutzerID FROM Gruppenmitglieder WHERE GruppenID='$gruppenID'";	
		$bisherigeMitglieder=array();
		$db->query($sql)->fold(
			function ($ergebnis) use (&$bisherigeMitglieder){
				while($nutzer = $ergebnis->fetch_array(MYSQLI_ASSOC)){
					array_push($bisherigeMitglieder,$nutzer["NutzerID"]);
				}
			},
			function($fehlerNachricht) use (&$nrt,&$erfolg){
				$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
				$erfolg=false;
			}
		);
		$loeschen = array_diff($bisherigeMitglieder,$nutzerIDs);
		$hinzufuegen = array_diff($nutzerIDs,$bisherigeMitglieder);
		
		foreach($loeschen as $loeschMitglied){
			$sql="DELETE FROM Gruppenmitglieder WHERE GruppenID='$gruppenID' AND NutzerID='$loeschMitglied'";	
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
				"`Gruppenmitglieder`(`GruppenID`, `NutzerID`) ".
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
	global $data, $nrtGruppe;
	$semail = $_SESSION["semail"];
	if (alleSchluesselGesetzt($data,"gruppenname")){	
		$db = oeffneBenutzerDB($nrtGruppe);
		$gruppenname=$db->real_escape_string($data["gruppenname"]);
		if(strlen($gruppenname)==0){
			$nrtGruppe->fehler("Kein Gruppenname angegeben");
			echo json_encode(array("nrt"=>$nrtGruppe->toJsCode()));
			die();
		}
		$moderatorID=EmailZuNutzerID($semail,$nrtGruppe);
		$sql="SELECT ID FROM Gruppe WHERE Name='$gruppenname' AND ModeratorID='$moderatorID'";
		$db->query($sql)->fold(
			function ($ergebnis) use (&$nrtGruppe){
				if($ergebnis->num_rows>0){
					$nrtGruppe->fehler("Es gibt bereits eine Gruppe mit diesem Namen.");
					echo json_encode(array("nrt"=>$nrtGruppe->toJsCode()));
					die();
				}
			},
			function($fehlerNachricht) use (&$nrtGruppe) {
				$nrtGruppe->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
				echo json_encode(array("nrt"=>$nrtGruppe->toJsCode()));
				die();
			}
		);
		$sql=
			"INSERT INTO ".
			"`Gruppe`(`ID`, `Name`, `ModeratorID`) ".
			"VALUES (0, '$gruppenname', '$moderatorID')"; 
		$db->query($sql)->fold(
			function ($ergebnis) use(&$nrtGruppe,$db){
				$nrtGruppe->okay("Gruppe erfolgreich hinzugefügt.");
				echo json_encode(array("nrt"=>$nrtGruppe->toJsCode(),"gruppenHTML"=>generateHTMLGruppen($db)));
				die();
			},
			function($fehlerNachricht) use (&$nrtGruppe) {
				$nrtGruppe->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
				echo json_encode(array("nrt"=>$nrtGruppe->toJsCode()));
				die();
			}
		);
	}
}

function schickeMitglieder(){
	global $data,$nrt;
	if (alleSchluesselGesetzt($data, "GruppenID")) {
		$db = oeffneBenutzerDB($nrt);
		$gruppenID = $db->real_escape_string($data["GruppenID"]);
		return generateHTMLMitglieder($db,$gruppenID);
	}		
}

function schickeGruppen(){
	global $data,$nrt;
	$db = oeffneBenutzerDB($nrt);
	return generateHTMLGruppen($db);
}

function loescheGruppe(){
	global $data,$nrtGruppe;
	
	if (alleSchluesselGesetzt($data, "GruppenID")) {
		
		$db = oeffneBenutzerDB($nrtGruppe);
		$gruppenID = $db->real_escape_string($data["GruppenID"]);
		$sql="DELETE FROM Gruppe  WHERE ID='$gruppenID'";
		$db->query($sql)->fold(
			function ($ergebnis){
			},
			function($fehlerNachricht) use (&$nrtGruppe) {
				$nrtGruppe->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
				echo json_encode(array("nrt"=>$nrtGruppe->toJsCode()));
				die();
			}
		);
		$sql="DELETE FROM Gruppenmitglieder  WHERE GruppenID='$gruppenID'";
		$db->query($sql)->fold(
			function ($ergebnis) use (&$nrtGruppe){
				$nrtGruppe->okay("Erfolgreich gelöscht");
			},
			function($fehlerNachricht) use (&$nrtGruppe) {
				$nrtGruppe->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
				echo json_encode(array("nrt"=>$nrtGruppe->toJsCode()));
				die();
			}
		);
		echo json_encode(array("nrt"=>$nrtGruppe->toJsCode()));
	}		
}
?>