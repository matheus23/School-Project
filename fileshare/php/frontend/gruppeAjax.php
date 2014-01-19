<?php
//Diese Datei braucht kein HTML, wird nur für AJAX-Anfrage benutzt
include "../utilities.php";
debugModus();
$data = $_POST;
if (alleSchluesselGesetzt($data, "nameemail")) {
	$nrt = new Nachrichten("fehlerListe","../../");
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
?>