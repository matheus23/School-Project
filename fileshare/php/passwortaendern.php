<!DOCTYPE html>
<?php session_start(); ?>
<html>
<head>
	<meta charset="UTF-8" />
	<link href="../css/style.css" rel="stylesheet" type="text/css" />
	<script src="../js/jsUtilities.js" type="text/javascript"></script>
	<title>Passwort ändern</title>
</head>
<body>
<div id="header">
    <i><h1 id="banner">Fileshare</h1></i>
</div>
<table width="100%" height="95%">
<tr>
	<td colspan="2">
		<table align="center">
		<tr><td align="center"><h1>Passwort ändern</h1></td></tr>
		<tr>
			<td>
				<form method="post" id="formular">
					<table align="center" valign="middle">					
						<tr><td class="rightAlign">Email Adresse:</td><td><input type="email" name="email" id="email" required></td></tr>
						<tr><td class="rightAlign">Altes Passwort:</td><td><input type="password" name="APw" id="APw" required></td></tr>
						<tr><td class="rightAlign">Neues Passwort:</td><td><input type="password" name="NPw" id="NPw" required></td></tr>
						<tr><td class="rightAlign">Neues Passwort bestätigen:</td><td><input type="password" name="NPwb" id="NPwb" required></td></tr>
						<tr><td colspan="2" align="right"><input type="submit" value="Passwort ändern" id="button"><input type="reset" name="Löschen"></td></tr>
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

<?php
include "../php/utilities.php";

debugModus();

$data = $_POST;
$nrt = new Nachrichten("fehlerListe");

if (alleSchluesselGesetzt($data, "APw", "NPw", "NPwb", "email")) {
	$db = oeffneBenutzerDB($nrt);
	tabelleNeueSpalte($db, "Benutzer", "RegistrierungsID","TEXT");//MUSS SPÄTER ENTFERNT WERDEN ### NUR ZUR TABELLEN-MIGRATION

	$email = $db->real_escape_string(strtolower($data["email"]));
	// wird sowieso gehashed:
	$apw = $data["APw"];
	$npw = $data["NPw"];
	$npwb = $data["NPwb"];
		
	if (userExestiertBereits($db, $email)) {	
		
		if ($npw != $npwb) {
			$nrt->fehler("Das neue Passwort stimmt nicht mit der Wiederholung überein");
		}
		else {
			$passwortTest = benutzerPwTest($db, $email, $apw);
			if ($passwortTest == PASSWORD_PASS)  {
				$npwHash = passwordHash($npw);
				$erfolgreich = $db->query("UPDATE `Benutzer` SET Passwort='$npwHash' WHERE Email='$email'");
				if ($erfolgreich) {
					$nrt->okay("Passwort erfolgreich geändert");
					//Bestätigungs Email, dass das Pw geändert wurde
				} // Ansonsten wird bereits ein fehler ausgegeben.
			} elseif ($passwortTest == WRONG_EMAIL) {
				$nrt->fehler("Diese Email ist nicht registriert.");
			} else {
				$nrt->fehler("Email-Passwort Kombination passt nicht.");
			}
		}
	}
}
?>
<script src="../js/pruefeRegistrierung.js"></script>
<?php $nrt->genJsCode(); ?>
</body>
</html>