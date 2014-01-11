<?php
//Funktion, zum Senden der E-mail
//Diese Funktion ist ohne Wirkung, solange in der php.ini kein SMTP-Server angegeben ist.
//Um E-Mail-Diesnste (gmail,yahoo,...) zu nutzen, sind scheinbar immer zusätzliche programme oder Module für php notwendig,
//die nicht ins repository passen.
function schickeRegistrierungsEmail($user,$email,$nutzerID){
	$server = $_SERVER["HTTP_HOST"];
	$betreff = "Registrierung abschließen";
	$pfad = dirname($_SERVER["REQUEST_URI"]);
	$message =
		"Hallo $user,\n".
		"um deine Registrierung abzuschließen öffne folgenden Link:\n".
		"$server$pfad/emailBestaetigen.php?nutzerID=$nutzerID";
	mail($email,$betreff,$message);	
}
//Prüft die NutzerID und setzt den jeweilgin Nutzer auf bestätigt
function pruefeRegistrierungsEmail($nutzerID,$db,$nrt){
	$nutzer = $db->query("SELECT * from Benutzer where RegistrierungsID='$nutzerID'")->fetch_array(MYSQLI_ASSOC);
	if(count($nutzer)==0){
		$nrt->fehler("Kein passender Nutzer gefunden");
		return;
	}
	if($nutzer["Bestaetigt"]==1){
		$nrt->warnung("Nutzer schon bestätigt");
		return;
	}
	$success = $db->query("UPDATE `Benutzer` SET Bestaetigt=1 where RegistrierungsID='$nutzerID'");
	if(!$success){
		$nrt->fehler("Fehler in der Datenbank bei Bestätigung des Kontos");
		return;
	}
	$email = $nutzer["Email"];
	$nrt->okay("Das Konto mit der E-Mail '$email' wurde erfolgreich bestätigt.");
}
?>