<?php
//Funktion, zum Senden der E-mail
//Diese Funktion ist ohne Wirkung, solange in der php.ini kein SMTP-Server angegeben ist.
//Um E-Mail-Diesnste (gmail,yahoo,...) zu nutzen, sind scheinbar immer zusätzliche programme oder Module für php notwendig,
//die nicht ins repository passen.
function schickeRegistrierungsEmail($user,$email,$nutzerID){
	$header = 'From: "secureshare" <secureshare@limond.de>';
	$betreff = "Registrierung abschließen";
	$pfad = dirname($_SERVER["REQUEST_URI"]);
	$message =
		"Hallo $user,\n".
		"um deine Registrierung abzuschließen öffne folgenden Link:\n".
		host."$pfad/emailBestaetigen.php?nutzerID=$nutzerID";
	return mail($email,$betreff,$message,$header);
}
//Prüft die NutzerID und setzt den jeweilgin Nutzer auf bestätigt
function pruefeRegistrierungsEmail($nutzerID,$db,$nrt){
	$db->query("SELECT * from Benutzer where RegistrierungsID='$nutzerID'")->fold(
		function($ergebnis) use (&$nrt,$nutzerID,$db){
			$nutzer=$ergebnis->fetch_array(MYSQLI_ASSOC);
			if(count($nutzer)==0){
				$nrt->fehler("Kein passender Nutzer gefunden");
				return;
			}
			if($nutzer["Bestaetigt"]==1){
				$nrt->warnung("Nutzer schon bestätigt");
				return;
			}
			$db->query("UPDATE `Benutzer` SET Bestaetigt=1 where RegistrierungsID='$nutzerID'")->fold(
				function($ergebnis)use (&$nrt,$nutzer){
					$email = $nutzer["Email"];
					$nrt->okay("Das Konto mit der E-Mail '$email' wurde erfolgreich bestätigt.");
				},
				function($fehlerNachricht)use (&$nrt){
					$nrt->fehler("Fehler in der Datenbank bei Bestätigung des Kontos");
				}
			);
		},
		function($fehlerNachricht)use (&$nrt){
			$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
		}
	);
}

function schickePasswortEmail($nrt,$email,$neuesPasswort){
	$header = 'From: "secureshare" <secureshare@limond.de>';
	$betreff = "Neues Passwort";
	$pfad = dirname($_SERVER["REQUEST_URI"]);
	$message =
		"Hallo,\n".
		"dein Passwort für Secureshare wurde geändert in:\n".
		"$neuesPasswort\n\n".
		"Bitte logge dich ein, um dein Passwort zu ändern, sodass du diese E-Mail löschen kannst:\n".
		host."$pfad/Anmeldung.php";
	return mail($email,$betreff,$message,$header);
}
?>