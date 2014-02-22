<!DOCTYPE html>
<?php
include "generate.php";
?>
<html>
<head>
	<meta charset="UTF-8" />
	<link href="../css/style.css"  rel="stylesheet" type="text/css"/>
	<script src="../js/jquery-1.10.2.min.js"></script>
	<script src="../js/jsUtilities.js" type="text/javascript"></script>
	<title>Passwort-reset</title>
</head>
<body>
<?=generateHeaderBanner()?>
<table width="100%" height="100%">
<tr>
	<td colspan="2">
		<table align="center">
		<tr><td align="center"><h1>Passwort vergessen?</h1></td></tr>
		<tr><td align="center"><h2>E-mail eingeben</h2></td></tr>
		<tr>
			<td>
				<form method="post" id="formular" action="">
					<table align="center" valign="middle">
						<tr><td class="rightAlign">Email Adresse:</td><td><input type="email" name="email" id="email" required></td></tr>
						<tr><td></td><td><input type="submit" value="Passwort zurücksetzen"></td></tr>
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
	<tr>
		<td align="center" width="20px">
			Sie werden nach Absenden dieses Formulars eine Email mit dem neuen Passwort erhalten
		</td>
	</tr>
	<tr>
	<td class='bottom_fix_right'>
		Zurück zur <a href="Anmeldung.php">Startseite
	</td>
</tr>
</table>
<?php
include "utilities.php";

debugModus();

$data = $_POST;
$nrt = new Nachrichten("#fehlerListe");

if (alleSchluesselGesetzt($data, "email")) {
	$db = oeffneBenutzerDB($nrt);
	
	$email = $db->real_escape_string(strtolower($data["email"]));

	if (userExestiertBereits($db, $email)) {
		include_once "benutzerEmail.php";
		
		$resetID=uniqid("reset_",true);
		$verfallsdatum = date ("Y-m-d H:i:s", strtotime("+1 day"));//1 Tag im MYSQL-Date Format
		
		$db->query("INSERT INTO `Passwortreset`(`Email`, `ID`, `Verfalldatum`) VALUES ('$email','$resetID','$verfallsdatum')")->fold(
			function($ergebnis) use (&$nrt,$email,$resetID,$verfallsdatum){
				if(schickePasswortResetEmail($nrt,$email,$resetID,$verfallsdatum)){
					$nrt->okay("Eine Passwort-Reset-Mail wurde an '$email' geschickt.");
				}
			},
			function($fehlerNachricht) use (&$nrt) {
				$nrt->fehler("Es gab einen Fehler beim Datenbankzugriff: $fehlerNachricht");
			}
		);
	}
	else {
		$nrt->fehler("Kein Benutzer mit dieser Email vorhanden.");
	}
}
?>
<script src="../js/pruefeRegistrierung.js"></script>
<?php $nrt->genJsCode(); ?>
</body>
</html>