<?php
	include_once "../utilities.php";
	debugModus();

	function verarbeitePasswortaenderung($nrt, $emailUnencoded, $pw, $neuesPw, $neuesPwWiederholung) {
		$db = oeffneBenutzerDB($nrt);
	
		$email = $db->real_escape_string(strtolower($emailUnencoded));
		
		if (userExestiertBereits($db, $email)) {
			if ($neuesPw != $neuesPwWiederholung) {
				$nrt->fehler("Das Passwort stimmt nicht mit der Wiederholung überein");
				return false;
			} else {
				$passwortTest = benutzerPwTest($db, $email, $pw);
				if ($passwortTest == PASSWORD_PASS) {
					$neuesPwHash = passwordHash($neuesPw);
					return $db->query("UPDATE `Benutzer` SET Passwort='$neuesPwHash' WHERE Email='$email'")->fold(
						function($ergebnis) use (&$nrt) {
							$nrt->okay("Passwort erfolgreich geändert");
							//Bestätigungs Email, dass das Pw geändert wurde
							return true;
						}, function($fehlerNachricht) use (&$nrt) {
							$nrt->fehler("Es gabe einen fehler beim Datenbankzugriff: $fehlerNachricht");
							return false;
						}
					);
				} elseif ($passwortTest == WRONG_EMAIL) {
					$nrt->fehler("Die angegebene Email exestiert nicht");
					return false;
				} elseif ($passwortTest == WRONG_COMBINATION) {
					$nrt->fehler("Die Email-Passwort Kombination passt nicht");
					return false;
				}
			}
		} else {
			$nrt->fehler("Die angegebene Email exestiert nicht");
			return false;
		}
	}
	
?>