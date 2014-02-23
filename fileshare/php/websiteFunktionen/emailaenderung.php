<?php
	include_once "../utilities.php";
	debugModus();

	function verarbeiteEmailaenderung($nrt, $emailUnencoded, $pw, $neueEmailUnencoded) {
		$db = oeffneBenutzerDB($nrt);
	
		$email = $db->real_escape_string(strtolower($emailUnencoded));
		$neueemail = $db->real_escape_string(strtolower($neueEmailUnencoded));
		
		if (userExestiertBereits($db, $email)) {
			$passwortTest = benutzerPwTest($db, $email, $pw);
			if ($passwortTest == PASSWORD_PASS)  {
				return $db->query("UPDATE `Benutzer` SET Email='$neueemail' WHERE Email='$email'")->fold(
					function($ergebnis) use (&$nrt) {
						$nrt->okay("Email erfolgreich geändert");
						//Bestätigungs Email, dass das Pw geändert wurde
						return PASS_THROUGH;
					}, function($fehlerNachricht) use (&$nrt) {
						return DB_FAIL;
					}
				);
				} elseif ($passwortTest == WRONG_EMAIL) {
					return WRONG_EMAIL;
				} elseif ($passwortTest == WRONG_COMBINATION) {
					return WRONG_COMBINATION;
				}
		}
		else { return WRONG_EMAIL; }
	}
	
?>