<?php
	include_once "../benutzerEmail.php";
	function verarbeiteLoeschung($nrt, $email, $passwort) {
		$db = oeffneBenutzerDB($nrt);
		$emailEscaped = $db->real_escape_string(strtolower($email));
	
		if (userExestiertBereits($db, $emailEscaped)) {
			$passwortTest = benutzerPwTest($db, $emailEscaped, $passwort);
			if ($passwortTest == PASSWORD_PASS)  {
				return $db->query("SELECT Nutzername, ID FROM Benutzer WHERE Email='$emailEscaped'")->fold(
					function ($ergebnis) use (&$nrt, $emailEscaped, $db) {
						$ergebnisAssoc = $ergebnis->fetch_assoc();
						$user = $ergebnisAssoc["Nutzername"];
						$userID = $ergebnisAssoc["ID"];
						$mail = schickeGeloeschtEmail($user,$emailEscaped,$nrt);
						if ($mail){
							$nrt->okay("Account erfolgreich gelöscht! Eine E-Mail ist auf dem Weg...");
							$db->query("DELETE FROM Benutzer WHERE ID='$userID'");
							$db->query("DELETE FROM aktuellerDateischluessel WHERE ID='$userID'");
							loescheAlleDateien($db, $userID);
							$db->query("DELETE FROM Datei WHERE Ersteller='$userID'");
							$db->query("DELETE FROM Dateischluessel WHERE NutzerID='$userID'");
							loescheAlleModeriertenGruppen($db, $userID);
							$db->query("DELETE FROM Gruppenmitglieder WHERE NutzerID='$userID'");
							$db->query("DELETE FROM Passwortreset WHERE Email='$emailEscaped'");
							$db->query("DELETE FROM Signaturschluessel WHERE NutzerID='$userID'");
							return true;
						} else {
							$nrt->fehler("Fehler beim Mailversandt...");
							return false;
						}
					},
					function ($fehlerNachricht) use (&$nrt) {
						$nrt->fehler("Fehler beim Datenbankzugriff: $fehlerNachricht");
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
		else {
			$nrt->fehler("Diese Email ist nicht registriert");
			return false;
		}	
	}
	
	function loescheAlleDateien($db, $nutzerID) {
		$db->query("SELECT * FROM Datei WHERE Ersteller='$nutzerID'")->fold(
			function($ergebnis) use ($nutzerID) {
				while ($zeile = $ergebnis->fetch_assoc()) {
					loescheDatei($zeile);
				}
			}, 
			function() {}
		);
	}
	
	function loescheDatei($tabellenZeile) {
		unlink("/home/www-data/dateien/".$tabellenZeile["ID"]);
	}
	
	function loescheAlleModeriertenGruppen($db, $userID) {
		$db->query("SELECT ID WHERE ModeratorID='$userID'")->fold(
			function($ergebnis) use (&$db) {
				while ($zeile = $ergebnis->fetch_assoc()) {
					$db->query("DELETE FROM Gruppenmitglieder WHERE GruppenID='".$zeile["ID"]."'");
				}
			},
			function () {}
		);
		$db->query("DELETE FROM Gruppen WHERE ModeratorID='$userID'");
	}
?>