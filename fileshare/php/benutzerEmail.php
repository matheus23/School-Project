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

function schickePasswortResetEmail($nrt,$email,$resetID,$verfallsdatum){
	$header = 'From: "secureshare" <secureshare@limond.de>';
	$betreff = "Passwort zurücksetzen";
	$pfad = dirname($_SERVER["REQUEST_URI"]);
	$message =
		"Hallo,\n".
		"Um dein Passwort zu ändern, benutze den folgenden Link:\n".
		host."$pfad/passwortreset.php?resetID=$resetID\n\n".
		"Der Link ist 24 Stunden gültig.";
	return mail($email,$betreff,$message,$header);
}

function resetPasswortEmail($resetID,$passwordHash,$db,$nrt){
	$db->query("SELECT * from Passwortreset where ID='$resetID'")->fold(
		function($ergebnis) use (&$nrt,$passwordHash,$db,$resetID){
			$nutzer=$ergebnis->fetch_array(MYSQLI_ASSOC);
			if(count($nutzer)==0){
				$nrt->fehler("Kein passender Nutzer gefunden oder falsche ID");
				return;
			}
			if(strtotime($nutzer["Verfalldatum"])<time()){
				$nrt->fehler("Link verfallen");
				return;
			}
			$email = $nutzer["Email"];
			$db->query("UPDATE `Benutzer` SET Passwort='$passwordHash' where Email='$email'")->fold(
				function($ergebnis)use (&$nrt,$resetID,$db){
					$nrt->okay("Passwort erfolgreich geändert.");
					$db->query("DELETE FROM `Passwortreset` WHERE ID='$resetID' OR Verfalldatum<NOW()");
				},
				function($fehlerNachricht)use (&$nrt){
					$nrt->fehler("Fehler in der Datenbank bei der Änderung: $fehlerNachricht");
				}
			);
		},
		function($fehlerNachricht)use (&$nrt){
			$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
		}
	);
}
?>