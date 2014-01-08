<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Account löschen</title>
</head>
<body>
<table width="100%" height="100%">
<tr>
	<td colspan="2">
		<table align="center">
		<tr><td><h1>Bitte Anmeldedaten eingeben</h1></td></tr>
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
	<td align="right">Zurück zur <a href="blablablablablablablabla">Startseite</a></td>
</tr>
</table>
<?php
include "utilities.php";

$data = $_POST;

if (alleSchlüsselGesetzt($data, "mail", "Pw")){
	$email = $data["mail"];
	$pw = $data["Pw"];
	
	$db = öffneBenutzerDB();
    
	if (userExestiertBereits($db, $email)) {
		$db->query("DELETE FROM `Benutzer` where email='$email'");
		prüfeSQLFehler($db, "Fehler:", True);
	} else {
		echo ("Fehler: USER NICHT VORHANDEN");
	}
}
?>
</body>
</html>