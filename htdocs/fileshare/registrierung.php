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
					<form method="post">
						<tr><td><input type="text" name ="Bn"> Benutzername</td></tr>
						<tr><td><input type="password" name ="Pw"> Passwort</td></tr>
						<tr><td><input type="password" name ="Pwb"> Passwort bestätigen</td></tr>
						<tr><td><input type="email" name ="email"> Email adresse</td></tr>
						<tr><td><input type="submit" value="Registrieren"><input type="reset" name="Löschen"></td></tr>
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

$data = $_POST;

if (alleSchluesselGesetzt($data, "Bn", "Pw", "Pwb", "email")) {

	$user = $data["Bn"];
	$pw = $data["Pw"];
	$pwb = $data["Pwb"];
	$email = $data["email"];
	
	if ($pw != $pwb){
		echo "Das Passwort stimmt nicht mit der Wiederholung überein";
	}
	
	$db = oeffneBenutzerDB();
	
	if (userExestiertBereits($db, $email)) {
		echo ("Fehler: <div align='center' font color='#FF0000'>ERROR ES GIBT DEN USER FÜR DIESE E-MAIL ADRESSE BEREITS </div>");
	}
	$db->query("INSERT INTO `Benutzer`(`Nutzername`, `Passwort`, `Email`) VALUES ('$user', '$pw', '$email')");
}
?>
</body>
</html>