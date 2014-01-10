<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="style.css" />
	<script src="jsUtilities.js" type="text/javascript"></script>
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
				<table align="center" valign="middle">
					<form method="post" id="formular">
						<tr><td><input type="text" name ="Bn" id="Bn" required> Benutzername</td></tr>
						<tr><td><input type="password" name ="Pw" id="Pw" required> Passwort</td></tr>
						<tr><td><input type="password" name ="Pwb" id="Pwb" required> Passwort bestätigen</td></tr>
						<tr><td><input type="email" name ="email" id="email" required> Email adresse</td></tr>
						<tr><td><input type="submit" value="Registrieren" id="button"><input type="reset" name="Löschen"></td></tr>
					</form>
				</table>
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
							<img src='img/warning.png' width='16' height='16' />
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

	$db = oeffneBenutzerDB();
	
	$user = $db->real_escape_string($data["Bn"]);
	$email = $db->real_escape_string($data["email"]);
	// wird sowieso gehashed:
	$pw = $data["Pw"];
	$pwb = $data["Pwb"];
	
	$db = oeffneBenutzerDB();
	if ($pw != $pwb) {
		$nrt->fehler("Das Passwort stimmt nicht mit der Wiederholung überein");
	}
	elseif (userExestiertBereits($db, $email)) {
		$nrt->fehler("Diese E-Mail ist bereits vergeben.");
	}
	else {
		$pwHash = password_hash($pw);
		$db->query("INSERT INTO `Benutzer`(`Nutzername`, `Passwort`, `Email`) VALUES ('$user', '$pwHash', '$email')");
		$nrt->okay("Erfolgreich registriert!");
	}
}

$fehlerjs = $nrt->toJsCode();
?>
<script src="pruefeRegistrierung.js"></script>
<script type="text/javascript"><?=$fehlerjs?></script>
</body>
</html>