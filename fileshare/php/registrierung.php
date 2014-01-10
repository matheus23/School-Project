<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<link href="../css/style.css" rel="stylesheet" type="text/css" />
	<script src="../js/jsUtilities.js" type="text/javascript"></script>
	<title>Anmeldung LGÖ - Datenbank</title>
</head>
<body>
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
						<tr><td></td><td><input type="submit" value="Registrieren" id="button"><input type="reset" name="Löschen"></td></tr>
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
include "utilities.php";

debugModus();

$data = $_POST;
$nrt = new Nachrichten("fehlerListe");

if (alleSchluesselGesetzt($data, "Bn", "Pw", "Pwb", "email")) {

	$db = oeffneBenutzerDB($nrt);
	tabelleNeueSpalte($db,"Benutzer","RegistrierungsID","TEXT");//MUSS SPÄTER ENTFERNT WERDEN ### NUR ZUR TABELLEN-MIGRATION
	$user = $db->real_escape_string($data["Bn"]);
	$email = $db->real_escape_string($data["email"]);
	// wird sowieso gehashed:
	$pw = $data["Pw"];
	$pwb = $data["Pwb"];
	
	if ($pw != $pwb) {
		$nrt->fehler("Das Passwort stimmt nicht mit der Wiederholung überein");
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
		$erfolgreich = $db->query("INSERT INTO `Benutzer`(`Nutzername`, `Passwort`, `Email`,`RegistrierungsID`) VALUES ('$user', '$pwHash', '$email','$nutzerID')");
		if ($erfolgreich) {
			$nrt->okay("Erfolgreich registriert!");
		} // Ansonsten wird bereits ein fehler ausgegeben.
	}
	
}
$fehlerjs = $nrt->toJsCode();
?>
<script src="../js/pruefeRegistrierung.js"></script>
<script type="text/javascript"><?=$fehlerjs?></script>
</body>
</html>