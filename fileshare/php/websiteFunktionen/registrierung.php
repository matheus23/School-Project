<?php

	function verarbeiteRegistrierung($nrt, $nutzername, $passwort, $passwortBestaetigen, $email) {
		$db = oeffneBenutzerDB($nrt);
		$user = strip_tags($db->real_escape_string($nutzername));
		$email = $db->real_escape_string(strtolower($email));
		
		if ($passwort != $passwortBestaetigen) {
			$nrt->fehler("Das Passwort stimmt nicht mit der Wiederholung überein.");
			return false;
		}
		elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			$nrt->fehler("Die E-Mail-Adresse hat ein ungültiges Format.");
			return false;
		}
		elseif (userExestiertBereits($db, $email)) {
			$nrt->fehler("Diese E-Mail ist bereits vergeben.");
			return false;
		}
		else {
			$pwHash = passwordHash($passwort);
			$regID=sichereID("reg_",30);
			
			// So geht das überprüfen von passwörtern dann:
			//if (passwordVerify($passwort, $pwHash))  {
			//	$nrt->okay("Passwort hashing funzt!");
			//}
			$sql = 
				"INSERT INTO ".
				"`Benutzer`(`Nutzername`, `Passwort`, `Email`, `RegistrierungsID`, `Bestaetigt`) ".
				"VALUES ('$user', '$pwHash', '$email','$regID','0')"; 
			$db->query($sql)->fold(
				function($ergebnis) use (&$nrt, $user, $email, $regID) {
					$mail = schickeRegistrierungsEmail($user,$email,$regID,$nrt);
					if ($mail) {//Bei einem Fehler wurde dieser bereits in $nrt geschrieben
						$nrt->okay("Erfolgreich registriert! Eine E-Mail ist auf dem Weg...");
						return true;
					}
				}, function($fehlerNachricht) use (&$nrt) {
					$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
					return false;
				}
			);
		}
	}
?>