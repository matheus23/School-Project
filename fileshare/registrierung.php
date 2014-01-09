<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
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
		</table>
		<br>
	</td>
</tr>
</table>
<?php
include "utilities.php";

debugModus();

$data = $_POST;
$fehlerliste="";

if (alleSchluesselGesetzt($data, "Bn", "Pw", "Pwb", "email")) {

	$user = $data["Bn"];
	$pw = $data["Pw"];
	$pwb = $data["Pwb"];
	$email = $data["email"];
	
	if ($pw != $pwb){
		$fehlerliste.="<li>Das Passwort stimmt nicht mit der Wiederholung überein</li>";
	}
	
	$db = oeffneBenutzerDB();
	
	if (userExestiertBereits($db, $email)) {
		$fehlerliste .= "<li>Diese E-Mail ist bereits vergeben.</li>";
	}
	$db->query("INSERT INTO `Benutzer`(`Nutzername`, `Passwort`, `Email`) VALUES ('$user', '$pw', '$email')");
}
?>
<ul id="fehlerListe">
<?=$fehlerliste?>
</ul>
<script src="pruefeRegistrierung.js"></script>
</body>
</html>