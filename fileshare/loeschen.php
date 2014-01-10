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
<tr>
	<td class='bottom_fix'>
		Zurück zur <a href="Anmeldung.php">Startseite
	</td>
</tr>
</table>
<?php
include "utilities.php";

$data = $_POST;
$nrt = new Nachrichten("fehlerListe");

if (alleSchluesselGesetzt($data, "mail", "Pw")){
	$email = $data["mail"];
	$pw = $data["Pw"];
	
	$db = oeffneBenutzerDB($nrt);
    
	if (userExestiertBereits($db, $email)) {
		$db->query("DELETE FROM `Benutzer` where email='$email'");
		echo ("LÖSCHEN DES ACCOUNTS ERFOLGREICH");
	} else {
		echo ("Fehler: USER NICHT VORHANDEN");
	}
}
$fehlerjs = $nrt->toJsCode();
?>
<script type="text/javascript"><?=$fehlerjs?></script>
</body>
</html>