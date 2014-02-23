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