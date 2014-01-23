<!DOCTYPE html>
<?php
	include "generate.php";
?>
<html>
<head>
	<meta charset="UTF-8" />
	<link href="../css/style.css" rel="stylesheet" type="text/css" />
	<script src="../js/jsUtilities.js" type="text/javascript"></script>
	<title>Account löschen</title>
</head>
<body>
<?=generateHeaderBanner()?>
<table width="100%" height="100%">
<tr>
	<td colspan="2">
		<table align="center">
		<tr><td><h1>Bitte Anmeldedaten eingeben,<br />um den Account zu löschen</h1></td></tr>
		<tr>
			<td>
				<form method="post" id="formular">
					<table align="center" valign="middle">
						<tr><td class="rightAlign">E-Mail:</td><td><input type="email" name ="mail"></td></tr>
						<tr><td class="rightAlign">Passwort:</td><td><input type="password" name ="Pw"></td></tr>
						<tr><td></td><td><input type="submit" value="Account löschen"><input type="reset"></td></tr>
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
<tr>
	<td class='bottom_fix_right'>
		Zurück zur <a href="Anmeldung.php">Startseite</a>
	</td>
</tr>
</table>
<?php
include "../php/utilities.php";
include_once("benutzerEmail.php");

debugModus();

$data = $_POST;
$nrt = new Nachrichten("fehlerListe");

if (alleSchluesselGesetzt($data, "mail", "Pw")){
	$db = oeffneBenutzerDB($nrt);
	$email = $db->real_escape_string(strtolower($data["mail"]));
	$pw = $data["Pw"];

	if (userExestiertBereits($db, $email)) {
		$passwortTest = benutzerPwTest($db, $email, $pw);
		if ($passwortTest == PASSWORD_PASS)  {
			$db->query("SELECT Nutzername FROM Benutzer WHERE Email='$email'")->fold(
				function ($ergebnis) use (&$nrt, $email, $db) {
					$user = $ergebnis->fetch_assoc()["Nutzername"];
					$mail = schickeGeloeschtEmail($user,$email,$nrt);
					if ($mail){
						$nrt->okay("Account erfolgreich gelöscht! Eine E-Mail ist auf dem Weg...");
						$db->query("DELETE FROM `Benutzer` WHERE email='$email'");
					} else {
						$nrt->fehler("Fehler beim Mailversandt...");
					}
				},
				function ($fehlerNachricht) use (&$nrt) {
					$nrt->fehler("Fehler beim Zugriff auf die Datenbank: $fehlerNachricht");
				}
			);
		} elseif ($passwortTest == WRONG_EMAIL) {
			$nrt->fehler("Diese Email ist nicht registriert.");
		} else {
			$nrt->fehler("Email-Passwort Kombination passt nicht.");
		}
	} 
	else {
		$nrt->fehler("Diese Email ist nicht registriert");
	}	
}

$fehlerjs = $nrt->toJsCode();
?>
<script type="text/javascript"><?=$fehlerjs?></script>
</body>
</html>