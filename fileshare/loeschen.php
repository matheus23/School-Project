<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="style.css" />
	<script src="jsUtilities.js" type="text/javascript"></script>
	<title>Account löschen</title>
</head>
<body>
<table width="100%" height="100%">
<tr>
	<td colspan="2">
		<table align="center">
		<tr><td><h1>Bitte Anmeldedaten eingeben, um den Account zu löschen</h1></td></tr>
		<tr>
			<td>
				<table align="center" valign="middle">
					<form method="post">
						<tr><td><input type="email" name ="mail"> E- Mail</td></tr>
						<tr><td><input type="password" name ="Pw"> Passwort</td></tr>
						<tr><td><input type="submit" value="Account löschen"><input type="reset"></td></tr>
					</form>
				</table>
			</td>
		</tr>
		</table>
		<br>
	</td>
</tr>
<tr>
	<td class='bottom_fix'>
		Zurück zur <a href="Anmeldung.php">Startseite
	</td>
</tr>
</table>
<?php
include "utilities.php";

$data = $_POST;

if (alleSchluesselGesetzt($data, "mail", "Pw")){
	$email = $data["mail"];
	$pw = $data["Pw"];
	
	$db = oeffneBenutzerDB();
    
	if (userExestiertBereits($db, $email)) {
		$db->query("DELETE FROM `Benutzer` where email='$email'");
		pruefeSQLFehler($db, "Fehler:", True);
		echo ("LÖSCHEN DES ACCOUNTS ERFOLGREICH");
	} else {
		echo ("Fehler: USER NICHT VORHANDEN");
	}
}
?>
</body>
</html>