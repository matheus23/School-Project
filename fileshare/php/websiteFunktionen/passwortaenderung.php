<?php
	include "../utilities.php";

	function verarbeitePasswortaenderung($nrt, $emailUnencoded, $pw, $neuesPw, $neuesPwWiederholung) {
		$db = oeffneBenutzerDB($nrt);
	
		$email = $db->real_escape_string(strtolower($emailUnencoded));
		
		if (userExestiertBereits($db, $email)) {
			if ($neuesPw != $neuesPwWiederholung) {
				$nrt->fehler("Das neue Passwort stimmt nicht mit der Wiederholung überein");
			}
			else {
				$passwortTest = benutzerPwTest($db, $email, $pw);
				if ($passwortTest == PASSWORD_PASS)  {
					$neuesPwHash = passwordHash($neuesPw);
					$db->query("UPDATE `Benutzer` SET Passwort='$neuesPwHash' WHERE Email='$email'")->fold(
						function($ergebnis) use (&$nrt) {
							$nrt->okay("Passwort erfolgreich geändert");
							//Bestätigungs Email, dass das Pw geändert wurde
						}, function($fehlerNachricht) use (&$nrt) {
							$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
						}
					);
				} elseif ($passwortTest == WRONG_EMAIL) {
					$nrt->fehler("Diese Email ist nicht registriert.");
				} else {
					$nrt->fehler("Email-Passwort Kombination passt nicht.");
				}
			}
		}
	}
?>