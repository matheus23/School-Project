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
		echo ("Fehler: <div align='center' font color='#FF0000'>ERROR ES GIBT DEN USER FÜR DIESE E-MAIL ADRESSE BEREITS </div>");
	}
	$db->query("INSERT INTO `Benutzer`(`Nutzername`, `Passwort`, `Email`) VALUES ('$user', '$pw', '$email')");
}
?>
<ul id="fehlerListe">
<?=$fehlerliste?>
</ul>

</body>
</html>
<script>
//Einstellungen für Passwort-/Benutzernamenlänge
var minPasswortLaenge = 5;
var maxPasswortLaenge = 10;
var minBenutzernamenLaenge = 3;

//Elemente der HTML-Seite werden als javascript-variabeln repräsentiert
var formular = document.getElementById("formular");
var benutzernameInput = document.getElementById("Bn");
var passwortInput = document.getElementById("Pw");
var passwortBestaetigtInput = document.getElementById("Pwb");
var emailInput = document.getElementById("email");
var fehlerListe = document.getElementById("fehlerListe");

//Funktion, die beim Abschicken aufgerufen wird
formular.onsubmit=function(){
	//fehlerliste wird geleert
	fehlerListe.innerHTML = "";
	//Formulardaten werden übernommen
	var benutzername = benutzernameInput.value;
	var passwort = passwortInput.value;
	var passwortBestaetigt = passwortBestaetigtInput.value;
	var email = emailInput.value;
	//Überprüfung, ob alle felder ausgefüllt
	if((benutzername.length==0)||(passwort.length==0)||(passwortBestaetigt.length==0)||(email.length==0)){
		fehlerListe.innerHTML += '<li>Alle Felder müssen ausgefüllt sein.</li>';
		return false;//Wenn nicht abbruch
	}
	//Überprüfung, ob Passwörter gleich
	if(passwortInput.value!=passwortBestaetigtInput.value){
		fehlerListe.innerHTML += '<li>Das Passwort stimmt nicht mit der Wiederholung überein</li>';
		return false;//Wenn nicht abbruch
	}
	//Überprüfung, ob Passwort nicht zu lang/kurz ist
	if((passwortInput.value.length<minPasswortLaenge)||(passwortInput.value.length>maxPasswortLaenge)){
		fehlerListe.innerHTML += '<li>Das Passwort muss mindestens '+minPasswortLaenge+' und höchstens '+maxPasswortLaenge+' Zeichen lang sein.</li>';
		return false;//Wenn nicht abbruch
	}
	//Überprüfung, ob Benutzername nicht zu kurz ist
	if(benutzernameInput.value.length<minBenutzernamenLaenge){
		fehlerListe.innerHTML += '<li>Der Benutzername muss mindestens '+minBenutzernamenLaenge+' Zeichen lang sein.</li>';
		return false;//Wenn nicht abbruch
	}
	return true;//Wenn alles in ordnung wird Formular abgesendet
}
</script>