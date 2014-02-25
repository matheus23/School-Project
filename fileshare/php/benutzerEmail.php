<?php
require_once dirname(__FILE__) . '/../PHPMailer/PHPMailerAutoload.php';
require_once dirname(__FILE__) . '/../../../EmailPasswort.php';
require_once dirname(__FILE__) . '/frontend/frontendUtilities.php';

#####################################################################
#							WICHTIG:								#
#		Der Mailversand ist nur mit dem Passwort möglich, das		#
#		in Skype steht.												#
#																	#
#		BITTE VERWENDET DIESES PASSWORT NICHT IN EINER DATEI IN 	#
#		EUREM GIT-REPOSITORY!										#
#																	#
#		Das Passwort wird von außerhalb über das Skript				#
#		'../../../EmailPasswort.php' eingebunden, das nicht im		#
#		Repository liegt. Es definiert die Kostante					#
#		EMAIL_PASSWORT												#
#####################################################################

class ExtPHPMailer extends PHPMailer {
	public function __construct() {
		parent::__construct();
		parent::isSMTP();
		$this->setLanguage('de');
		$this->Host='smtp-mail.outlook.com';
		$this->Hostname='limond.de';//Wichtig für Backtrace (Spam-Erkennung)
		$this->XMailer=' ';//Wichtig für Spam-Erkennung (Der Default-Wert "PHPMailer..." wird fast überall als Spam erkannt
		$this->SMTPAuth=true;
		$this->Username = 'mangoshare@limond.de';
		$this->Password = EMAIL_PASSWORT;
		$this->SMTPSecure = 'tls';
		$this->From = 'mangoshare@limond.de';
		$this->FromName = 'Mangoshare';
		$this->CharSet = 'utf-8';
		parent::isHTML(true);
		
	}
}

function schickeRegistrierungsEmail($user,$email,$regID,$nrt){
	$mail= new ExtPHPMailer();
	$mail->Subject = 'Registrierung abschließen';
	$pfad = dirname($_SERVER["REQUEST_URI"]);
	$mail->addAddress($email);
	$mail->Body =//Email-Text für HTML-Mails (Link anklickbar)
		"Hallo $user,<br>".
		"um deine Registrierung abzuschließen öffne folgenden Link:<br>".
		"<a href='https://".host."/".githubdir."/fileshare/php/emailBestaetigen.php?regID=$regID'>".
		"https://".host."/".githubdir."/fileshare/php/emailBestaetigen.php?regID=$regID".
		"</a>";
	$mail->AltBody = //Email-Text, wenn HTML nicht aktiviert ist
		"Hallo $user,\n".
		"um deine Registrierung abzuschließen öffne folgenden Link:\n".
		"https://".host."/".githubdir."/fileshare/php/emailBestaetigen.php?regID=$regID";
	if(!$mail->send()) {
		$nrt->fehler("Fehler beim Mailversand:".$mail->ErrorInfo);
		return false;
	}
	return true;
}

function schickeGeloeschtEmail($user,$email,$nrt){
	$mail= new ExtPHPMailer();
	$mail->Subject = 'Account erfolgreich gelöscht';
	//$pfad = dirname($_SERVER["REQUEST_URI"]);
	$mail->addAddress($email);
	$mail->Body =//Email-Text für HTML-Mails
		"Hallo $user,<br>".
		"dein Account wurde erfolgreich gelöscht.<br>";
	$mail->AltBody = //Email-Text, wenn HTML nicht aktiviert ist
		"Hallo $user,\n".
		"dein Account wurde erfolgreich gelöscht.\n";
	if(!$mail->send()) {
		$nrt->fehler("Fehler beim Mailversand:".$mail->ErrorInfo);
		return false;
	}
	return true;
}



//Prüft die NutzerID und setzt den jeweilgin Nutzer auf bestätigt
function pruefeRegistrierungsEmail($regID,$db,$nrt){
	$db->query("SELECT * from Benutzer where RegistrierungsID='$regID'")->fold(
		function($ergebnis) use (&$nrt,$regID,$db){
			$nutzer=$ergebnis->fetch_array(MYSQLI_ASSOC);
			if(count($nutzer)==0){
				$nrt->fehler("Kein passender Nutzer gefunden");
				return;
			}
			if($nutzer["Bestaetigt"]==1){
				$nrt->warnung("Nutzer schon bestätigt");
				return;
			}
			$db->query("UPDATE `Benutzer` SET Bestaetigt=1 where RegistrierungsID='$regID'")->fold(
				function($ergebnis)use (&$nrt,$nutzer){
					$email = $nutzer["Email"];
					$nrt->okay("Das Konto mit der E-Mail '$email' wurde erfolgreich bestätigt.");
				},
				function($fehlerNachricht)use (&$nrt){
					$nrt->fehler("Fehler in der Datenbank bei Bestätigung des Kontos");
				}
			);
		},
		function($fehlerNachricht)use (&$nrt){
			$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
		}
	);
}

function schickePasswortResetEmail($nrt,$email,$resetID,$verfallsdatum){
	$mail= new ExtPHPMailer();
	$mail->Subject = 'Passwort zurücksetzen';
	$mail->addAddress($email);
	$mail->Body =//Email-Text für HTML-Mails (Link anklickbar)
		"Hallo,<br>".
		"Um dein Passwort zu ändern, benutze den folgenden Link:<br>".
		"<a href='https://".host."/".githubdir."/fileshare/php/passwortreset.php?resetID=$resetID'>https://".host."/".githubdir."/fileshare/php/passwortreset.php?resetID=$resetID</a><br><br>".
		"Der Link ist 24 Stunden gültig.";
	$mail->AltBody = //Email-Text, wenn HTML nicht aktiviert ist
		"Hallo,\n".
		"Um dein Passwort zu ändern, benutze den folgenden Link:\n".
		"https://".host."/".githubdir."/fileshare/php/passwortreset.php?resetID=$resetID\n\n".
		"Der Link ist 24 Stunden gültig.";
	if(!$mail->send()) {
		$nrt->fehler("Fehler beim Mailversand:".$mail->ErrorInfo."  $email,$resetID,$verfallsdatum");
		return false;
	}
	return true;
}

function resetPasswortEmail($resetID,$passwordHash,$db,$nrt){
	$db->query("SELECT * from Passwortreset where ID='$resetID'")->fold(
		function($ergebnis) use (&$nrt,$passwordHash,$db,$resetID){
			$nutzer=$ergebnis->fetch_array(MYSQLI_ASSOC);
			if(count($nutzer)==0){
				$nrt->fehler("Kein passender Nutzer gefunden oder falsche ID");
				return;
			}
			if(strtotime($nutzer["Verfalldatum"])<time()){
				$nrt->fehler("Link verfallen");
				return;
			}
			$email = $nutzer["Email"];
			$db->query("UPDATE `Benutzer` SET Passwort='$passwordHash' where Email='$email'")->fold(
				function($ergebnis)use (&$nrt,$resetID,$db){
					$nrt->okay("Passwort erfolgreich geändert.");
					$db->query("DELETE FROM `Passwortreset` WHERE ID='$resetID' OR Verfalldatum<NOW()");
				},
				function($fehlerNachricht)use (&$nrt){
					$nrt->fehler("Fehler in der Datenbank bei der Änderung: $fehlerNachricht");
				}
			);
		},
		function($fehlerNachricht)use (&$nrt){
			$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
		}
	);
}

function istNutzerBestaetigt($nutzerID,$db){
	return $db->query("SELECT Bestaetigt from Benutzer where ID='$nutzerID'")->fold(
		function($ergebnis) use (&$nrt,$nutzerID,$db){
			$nutzer=$ergebnis->fetch_array(MYSQLI_ASSOC);
			if($nutzer["Bestaetigt"]==1){
				return true;
			}
			if($nutzer["Bestaetigt"]==0){
				#$nrt->fehler("Nutzer nicht bestätigt");
				return false;
			}
		},
		function($fehlerNachricht)use (&$nrt){
			$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
			return false;
		}
	);
}

function setzteNutzerBestaetigt($db, $nutzerID, $bestaetigt) {
	return $db->query("UPDATE Benutzer SET Bestaetigt='$bestaetigt' WHERE ID='$nutzerID'")->fold(
		function($ergebnis) use (&$nrt,$nutzerID,$db){
			return true;
		},
		function($fehlerNachricht)use (&$nrt){
			$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
			return false;
		}
	);
}

function schickeBestaetigungsEmail($user,$email,$nutzerID,$nrt){
	$mail= new ExtPHPMailer();
	$mail->Subject = 'Emailänderung abschließen';
	$mail->addAddress($email);
	$mail->Body =//Email-Text für HTML-Mails (Link anklickbar)
		"Hallo $user,<br>".
		"um deine Emailänderung abzuschließen öffne folgenden Link:<br>".
		"<a href='https://".host."/".githubdir."/fileshare/php/emailBestaetigen.php?regID=$nutzerID'>https://".host."/".githubdir."/fileshare/php/emailBestaetigen.php?nutzerID=$nutzerID</a>";
	$mail->AltBody = //Email-Text, wenn HTML nicht aktiviert ist
		"Hallo $user,\n".
		"um deine Emailänderung abzuschließen öffne folgenden Link:\n".
		"https://".host."/".githubdir."/fileshare/php/emailBestaetigen.php?regID=$nutzerID";
	if(!$mail->send()) {
		$nrt->fehler("Fehler beim Mailversand:".$mail->ErrorInfo);
		return false;
	}
	return true;
}

?>