<?php
	require_once "../utilities.php";
	debugModus();
	require_once(rootdir."fileshare/php/frontend/frontendUtilities.php");

	function verarbeiteEmailaenderung($nrt, $emailUnencoded, $pw, $neueEmailUnencoded) {
		$db = oeffneBenutzerDB($nrt);
	
		$email = $db->real_escape_string(strtolower($emailUnencoded));
		$neueemail = $db->real_escape_string(strtolower($neueEmailUnencoded));
		$nutzerID = EmailZuNutzerID($email);
		
		if (userExestiertBereits($db, $email)) {
			$passwortTest = benutzerPwTest($db, $email, $pw);
			if ($passwortTest == PASSWORD_PASS)  {
				return $db->query("UPDATE `Benutzer` SET Email='$neueemail' WHERE Email='$email'")->fold(
					function($ergebnis) use (&$db, &$nrt, $nutzerID, $email) {
						$nutzername = NutzerIDZuNutzername($nutzerID, $nrt);
						$regID = sichereID("reg_");
						if (setzteRegID($db, $nrt, $nutzerID, $regID)) {
							$mail = schickeBestaetigungsEmail($nutzername, $email, $regID, $nrt);
							if ($mail) {
								if (setzteNutzerBestaetigt($db, $nutzerID, "0")) {
									$nrt->okay("Email erfolgreich geändert");
									return true;
								} else {
									return false;
								}
							} else {
								return false;
							}
						} else return false;
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
	
	function setzteRegID($db, $nrt, $nutzerID, $regID) {
		return $db->query("UPDATE Benutzer SET RegistrierungsID='$regID' WHERE ID='$nutzerID'")->fold(
			function($ergebnis) {
				return true;
			},
			function($fehlerNachricht) {
				$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
				return false;
			}
		);
	}	
?>