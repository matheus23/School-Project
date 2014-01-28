<?php
	include "../../php/utilities.php";

	function verarbeiteAnmeldung($nrt, $emailUnescaped, $merken, $passwort) {
		$db = oeffneBenutzerDB($nrt);
		
		$email = $db->real_escape_string(strtolower($emailUnescaped));
		$pwTest = benutzerPwTest($db, $email, $passwort);
		
		if ($pwTest == WRONG_EMAIL) {
			$nrt->fehler("Falsche Email");
		} elseif ($pwTest == WRONG_COMBINATION) {
			$nrt->fehler("Falsches Passwort");
		} elseif ($pwTest == PASSWORD_PASS) {
			$nrt->okay("Anmeldung erfolgreich");
			$_SESSION["semail"] = $email;
			if ($merken) {
				setcookie("email",$email,time()+1*60*60*24*7,"/");//email cookie wird gesetzt (1 Woche)
			}
			else{
				setcookie("email",null,-1,"/");
			}
			session_regenerate_id(true);//Session wird neu gestartet
		}
	}
?>