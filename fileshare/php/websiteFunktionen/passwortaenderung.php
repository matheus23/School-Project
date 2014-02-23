<?php
	include_once "../utilities.php";
	debugModus();

	function verarbeitePasswortaenderung($nrt, $emailUnencoded, $pw, $neuesPw, $neuesPwWiederholung) {
		$db = oeffneBenutzerDB($nrt);
	
		$email = $db->real_escape_string(strtolower($emailUnencoded));
		
		if (userExestiertBereits($db, $email)) {
			if ($neuesPw != $neuesPwWiederholung) {
				return WRONG_REPETITION;
			}
			else {
				$passwortTest = benutzerPwTest($db, $email, $pw);
				if ($passwortTest == PASSWORD_PASS) {
					$neuesPwHash = passwordHash($neuesPw);
					return $db->query("UPDATE `Benutzer` SET Passwort='$neuesPwHash' WHERE Email='$email'")->fold(
						function($ergebnis) use (&$nrt) {
							$nrt->okay("Passwort erfolgreich geändert");
							//Bestätigungs Email, dass das Pw geändert wurde
							return PASS_THROUGH;
						}, function($fehlerNachricht) use (&$nrt) {
							return DB_FAIl;
						}
					);
				} elseif ($passwortTest == WRONG_EMAIL) {
					return WRONG_EMAIL;
				} elseif ($passwortTest == WRONG_COMBINATION) {
					return WRONG_COMBINATION;
				}
			}
		}
		else { return WRONG_EMAIL; }
	}
	
?>