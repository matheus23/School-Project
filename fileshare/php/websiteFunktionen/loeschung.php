<?php
	include_once "../benutzerEmail.php";
	include_once "../utilities.php";
	
	function verarbeiteLoeschung($nrt, $email, $passwort) {
		$db = oeffneBenutzerDB($nrt);
		
		$emailEscaped = $db->real_escape_string(strtolower($email));
	
		if (userExestiertBereits($db, $emailEscaped)) {
			$passwortTest = benutzerPwTest($db, $emailEscaped, $passwort);
			if ($passwortTest == PASSWORD_PASS)  {
				return $db->query("SELECT Nutzername FROM Benutzer WHERE Email='$emailEscaped'")->fold(
					function ($ergebnis) use (&$nrt, $emailEscaped, $db) {
						$user = $ergebnis->fetch_assoc()["Nutzername"];
						$mail = schickeGeloeschtEmail($user,$emailEscaped,$nrt);
						if ($mail){
							$nrt->okay("Account erfolgreich gelöscht! Eine E-Mail ist auf dem Weg...");
							$db->query("DELETE FROM `Benutzer` WHERE email='$emailEscaped'");
							return true;
						} else {
							$nrt->fehler("Fehler beim Mailversandt...");
							return false;
						}
					},
					function ($fehlerNachricht) use (&$nrt) {
						$nrt->fehler("Fehler beim Zugriff auf die Datenbank: $fehlerNachricht");
						return false;
					}
				);
			} elseif ($passwortTest == WRONG_EMAIL) {
				$nrt->fehler("Diese Email ist nicht registriert.");
				return false;
			} else {
				$nrt->fehler("Email-Passwort Kombination passt nicht.");
				return false;
			}
			
		}
		$nrt->fehler("Es wurden keine Angaben gemacht");
		return false;
	}
?>