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
				<div id="fehlerListe">
				<noscript><div class='warnung'><img src='img/warning.png' />Javascript muss aktiviert sein!</div></noscript>
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
$fehlerliste = array();

if (alleSchluesselGesetzt($data, "Bn", "Pw", "Pwb", "email")) {

	$user = $data["Bn"];
	$pw = $data["Pw"];
	$pwb = $data["Pwb"];
	$email = $data["email"];
	
	$db = oeffneBenutzerDB();
	if ($pw != $pwb) {
		array_push($fehlerliste, "Das Passwort stimmt nicht mit der Wiederholung überein");
	}
	elseif (userExestiertBereits($db, $email)) {
		array_push($fehlerliste, "Diese E-Mail ist bereits vergeben.");
	}
	else {
		$db->query("INSERT INTO `Benutzer`(`Nutzername`, `Passwort`, `Email`) VALUES ('$user', '$pw', '$email')");
	}
}

$fehlerjs = jsFuerFehlerListe("document.getElementById('fehlerListe')", $fehlerliste);
?>
<script src="pruefeRegistrierung.js"></script>
<script type="text/javascript"><?=$fehlerjs?></script>
</body>
</html>