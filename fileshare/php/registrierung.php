<!DOCTYPE html>
<?php session_start(); ?>
<html>
<head>
	<meta charset="UTF-8" />
	<link href="../css/style.css" rel="stylesheet" type="text/css" />
	<script src="../js/jsUtilities.js" type="text/javascript"></script>
	<title>Anmeldung LGÖ - Datenbank</title>
</head>
<body>
<?=generateHeaderBanner()?>
<table width="100%" height="95%">
<tr>
	<td colspan="2">
		<table align="center">
		<tr><td><h1>Neu registrieren</h1></td></tr>
		<tr>
			<td>
				<form method="post" id="formular">
					<table align="center" valign="middle">					
						<tr><td class="rightAlign">Benutzername:</td><td><input type="text" name="Bn" id="Bn" required></td></tr>
						<tr><td class="rightAlign">Passwort:</td><td><input type="password" name="Pw" id="Pw" required></td></tr>
						<tr><td class="rightAlign">Passwort bestätigen:</td><td><input type="password" name="Pwb" id="Pwb" required></td></tr>
						<tr><td class="rightAlign">Email Adresse:</td><td><input type="email" name="email" id="email" required></td></tr>
						<tr><td colspan="2"><h2>Captcha:</h2></td></tr>
						<tr><td colspan="2">
							<img id="captcha" src="../securimage/securimage_show.php" alt="CAPTCHA Image" />
						</td></tr>
						<tr><td colspan="2">
							<input type="text" name="captcha_code" size="10" maxlength="6" />
							<a href="#" onclick="document.getElementById('captcha').src = '../securimage/securimage_show.php?' + Math.random(); return false">[ Different Image ]</a>
						</td></tr>
						<tr><td colspan="2"><input type="submit" value="Registrieren" id="button"><input type="reset" name="Löschen"></td></tr>
					</table>
				</form>
			</td>
		</tr>
		<tr>
			<td>
				<!-- Falls fehler auftreten, werden hier <div>'s erzeugt, die fehlernachrichten beinhalten: -->
				<div id="fehlerListe">
					<!-- Dieses <div> wird nur angezeigt, sofern das <script> danach es nicht sofort wieder löscht,
						das bedeutet soviel wie: Nur wenn javascript nicht aktiviert ist. -->
					<div id="jsenable" class='infobox warnung'>
						<table border="0"><tr><td class="verticalMid">
							<img src='../img/warning.png' width='16' height='16' />
						</td><td class="verticalMid">
							Javascript muss aktiviert sein!
						</td></tr></table>
					</div>
					<script type="text/javascript">
						document.getElementById("jsenable").style.display = 'none';
					</script>
				</div>
			</td>
		</tr>
		</table>
		<br>
	</td>
</tr>
</table>
<div class="bottom_fix_right">Bereits registriert? <a href="Anmeldung.php">Hier</a> klicken</div>

<?php
include "../php/utilities.php";
include_once "../securimage/securimage.php";
include_once("benutzerEmail.php");

debugModus();

$data = $_POST;
$nrt = new Nachrichten("fehlerListe");
$securimage = new Securimage();

if (alleSchluesselGesetzt($data, "Bn", "Pw", "Pwb", "email")) {
	if ($securimage->check($_POST["captcha_code"]) == false) {
		$nrt->fehler("Das eingegebene Captcha ist falsch.");
	} else {
		$db = oeffneBenutzerDB($nrt);
		$user = $db->real_escape_string($data["Bn"]);
		$email = $db->real_escape_string(strtolower($data["email"]));
		// wird sowieso gehashed:
		$pw = $data["Pw"];
		$pwb = $data["Pwb"];
		
		if ($pw != $pwb) {
			$nrt->fehler("Das Passwort stimmt nicht mit der Wiederholung überein.");
		}
		elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			$nrt->fehler("Die E-Mail-Adresse hat ein ungültiges Format.");
		}
		elseif (userExestiertBereits($db, $email)) {
			$nrt->fehler("Diese E-Mail ist bereits vergeben.");
		}
		else {
			$pwHash = passwordHash($pw);
			$nutzerID=uniqid("reg_",true);
			
			// So geht das überprüfen von passwörtern dann:
			//if (passwordVerify($pw, $pwHash))  {
			//	$nrt->okay("Passwort hashing funzt!");
			//}
			$sql = 
				"INSERT INTO ".
				"`Benutzer`(`Nutzername`, `Passwort`, `Email`, `RegistrierungsID`, `Bestaetigt`) ".
				"VALUES ('$user', '$pwHash', '$email','$nutzerID','0')"; 
			$db->query($sql)->fold(
				function($ergebnis) use (&$nrt, $user, $email, $nutzerID) {
					$mail = schickeRegistrierungsEmail($user,$email,$nutzerID);
					if (!$mail) {
						$nrt->fehler("Fehler bei dem Mailversand");
					} else {
						$nrt->okay("Erfolgreich registriert! Eine E-Mail ist auf dem Weg...");
					}
				}, function($fehlerNachricht) use (&$nrt) {
					$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
				}
			);
		}
	}
}
?>
<script src="../js/pruefeRegistrierung.js"></script>
<?php $nrt->genJsCode(); ?>
</body>
</html>