<?php
	include_once "../utilities.php";
	debugModus();

	function verarbeiteNamensaenderung($nrt, $emailUnencoded, $pw, $neuerNameUnencoded) {
		$db = oeffneBenutzerDB($nrt);
	
		$email = $db->real_escape_string(strtolower($emailUnencoded));
		$neuerNutzername = $db->real_escape_string($neuerNameUnencoded);
		
		if (userExestiertBereits($db, $email)) {
			$passwortTest = benutzerPwTest($db, $email, $pw);
			if ($passwortTest == PASSWORD_PASS)  {
				return $db->query("UPDATE `Benutzer` SET Nutzername='$neuerNutzername' WHERE Email='$email'")->fold(
					function($ergebnis) use (&$nrt) {
						$nrt->okay("Nutzernamen erfolgreich geändert");
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
		} else {
			$nrt->fehler("Die angegebene Email exestiert nicht");
			return false;
		}
	}
	
?>