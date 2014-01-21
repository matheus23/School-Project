<?php
//Diese Datei braucht kein HTML, wird nur für AJAX-Anfrage benutzt
session_start();
include "../utilities.php";
debugModus();
$data = $_POST;
$nrt = new Nachrichten("fehlerListe","../../");
if((!isset($_SESSION["semail"]))||($_SESSION["semail"]=="")){
	$nrt->fehler("Session abgelaufen.");
	echo json_encode(array("nrt"=>$nrt->toJsCode()));
}

if (alleSchluesselGesetzt($data, "aktion")) {
	switch ($data["aktion"]){
		case "schickeNutzerEmail":
			schickeNutzerEmail();
			break;
		case "fertigGruppe":
			fertigGruppe();
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
	$erfolg = false;
	$semail = $_SESSION["semail"];
	if (alleSchluesselGesetzt($data, "emails","neueGruppe","gruppenname")){
		$db = oeffneBenutzerDB($nrt);
		$emails=json_decode($data["emails"]);
		$gruppenname=$db->real_escape_string($data["gruppenname"]);
		foreach($emails as &$email){
			$email = $db->real_escape_string($email);
		}
		if($data["neueGruppe"]){
			$sql=
				"INSERT INTO ".
				"`Gruppe`(`ID`, `Name`, `ModeratorEmail`) ".
				"VALUES (0, '$gruppenname', '$semail')"; 
			$db->query($sql)->fold(
				function ($ergebnis) use(&$nrt,$db,$emails,&$erfolg){
					$id = $db->insert_id;
					foreach($emails as $email){
						$sql=
							"INSERT INTO ".
							"`Gruppenmitglieder`(`GruppenID`, `NutzerEmail`) ".
							"VALUES ($id, '$email')";
						$db->query($sql)->fold(
							function ($ergebnis) use(&$nrt,&$erfolg){
									$nrt->okay("Gruppe erfolgreich hinzugefügt.");
									$erfolg = true;
							},
							function($fehlerNachricht) use (&$nrt) {
								$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
							}
						);
					}
				},
				function($fehlerNachricht) use (&$nrt) {
					$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
				}
			);
		}
		else{
		}
	}
	echo json_encode(array("nrt"=>$nrt->toJsCode(),"erfolg"=>$erfolg));
}
?>