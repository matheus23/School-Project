<!DOCTYPE HTML>
<?php
include_once "../utilities.php";
debugModus();
require_once(rootdir."fileshare/php/generate.php");
require_once(rootdir."fileshare/php/frontend/frontendUtilities.php"); // Definiert auch $frontendMenu
require_once(rootdir."fileshare/php/frontend/Menu.php");
require_once(rootdir."fileshare/php/websiteFunktionen/loeschung.php");
require_once(rootdir."fileshare/php/websiteFunktionen/emailaenderung.php");
require_once(rootdir."fileshare/php/websiteFunktionen/passwortaenderung.php");
require_once(rootdir."fileshare/php/websiteFunktionen/namensaenderung.php");

session_start();
leiteUmWennNichtAngemeldet();
$data = $_POST;
$menu = new Menu($frontendMenu, "konto", "../../");
$nrt = new Nachrichten("#fehlerListe", "../../");

if (isset($data["accloeschen"])) {
	if (!alleSchluesselGesetzt($data, "email", "passwort", "check")) {
		$nrt->fehler("Es wurden keine Angaben gemacht");
	} else {
		$erfolgreich = verarbeiteLoeschung($nrt, $data["email"], $data["passwort"]);
		if ($erfolgreich) {
			session_destroy();
			umleitenZuAnmeldung();
		}
	}
}

if (isset($data["emailaendern"])) {
	if (!alleSchluesselGesetzt($data, "email", "passwort", "neueemail")) {
		$nrt->fehler("Es wurden keine Angaben gemacht");
	} else {
		$erfolgreich = verarbeiteEmailaenderung($nrt, $data["email"], $data["passwort"], $data["neueemail"]);
		if ($erfolgreich) {
	        session_destroy();
			umleitenZuAnmeldung();
		}
	}
}
		

if(isset($data["passwortaendern"])) {
	if (!alleSchluesselGesetzt($data, "email", "aktuellespasswort", "neuespasswort", "neuespasswort2")) {
		$nrt->fehler("Es wurden keine Angaben gemacht");
	} else {
		$erfolgreich = verarbeitePasswortaenderung($nrt, $data["email"], $data["aktuellespasswort"], $data["neuespasswort"], $data["neuespasswort2"]);
		if ($erfolgreich) {
			session_destroy();
			umleitenZuAnmeldung();
		}
	}
}

if(isset($data["nameaendern"])) {
	if (!alleSchluesselGesetzt($data, "email", "passwort", "neuername")) {
		$nrt->fehler("Es wurden keine Angaben gemacht");
	} else {
		verarbeiteNamensaenderung($nrt, $data["email"], $data["passwort"], $data["neuername"]);
		// verarbeiteNamensaenderung() gibt bereits fehler/okay Nachrichten aus.
	}
}

?>
<html>
<head>
	<meta charset="utf-8">
	<title>Benutzerkonto</title>
	<link type="text/css" rel="stylesheet" href="../../css/style.css">
	<link type="text/css" rel="stylesheet" href="../../css/frontendStyle.css">
	<script src="../../js/jquery-1.10.2.min.js"></script>
	<script src="../../js/frontend.js"></script>
	<script src="../../js/jsUtilities.js"></script>
</head>
<body>
<?=generateHeaderBannerLogout()?>
<div id="contentWrapper">
	<div id="menu">
		<?=$menu->toHTML()?>
	</div>
	<div id="panel">
		<h1>Benutzerkontenverwaltung</h1>
		<div id="fehlerListe"></div>
		<div class="liste" id="verwaltungsliste">
			<div class="label">Aktion wählen:</div>
			<div class="listenelement" data-aufruf="#nameAendern">Nutzername ändern</div>
			<div class="listenelement" data-aufruf="#emailAendern">Email ändern</div>
			<div class="listenelement" data-aufruf="#passwortAendern">Passwort ändern</div>
			<div class="listenelement" data-aufruf="#accountLoeschen">Account Löschen</div>
		</div>
		<div id="editor">
			<div class="label">Einstellungen</div>
			<form method="POST" action="" id="nameAendern">
				<table>
					<tr><td>Email:</td><td><input type="text" name="email" id="mail"></td></tr>
					<tr><td>Aktuelles Passwort:</td><td><input type="password" name="passwort" id="pwa"></td></tr>
					<tr><td>Neuer Nutzername:</td><td><input type="text" name="neuername" id="nname"><td></tr>
					<tr><td colspan="2"><input type="submit" value="Nutzername ändern" name="nameaendern"></td></tr>
				</table>
			</form>
			<form method="POST" action="" id="emailAendern">
				<table>
				    <tr><td>Email:</td><td><input type="email" name="email" id="mail"></td></tr>
				    <tr><td>Passwort:</td><td><input type="password" name="passwort" id="pwae"></td></tr>
				    <tr><td>Neue Email:</td><td><input type="email" name="neueemail" id="nmail"></td></tr>
				    <tr><td colspan="2"><input type="submit" value="Email ändern" name="emailaendern"></td></tr>
				</table>
			</form>
			<form method="POST" action="" id="passwortAendern">
				<table>
					<tr><td>Email:</td><td><input type="text" name="email" id="mail"></td></tr>
					<tr><td>Aktuelles Passwort:</td><td><input type="password" name="aktuellespasswort" id="pwa"></tr></td>
					<tr><td>Neues Passwort:</td><td><input type="password" name="neuespasswort" id="neuespasswort"></td></tr>
					<tr><td>Passwort wiederholen:</td><td><input type="password" name="neuespasswort2" id="pwn"></td></tr> 
					<tr><td colspan="2"><input type="submit" value="Passwort ändern" name="passwortaendern"></td></tr>
				</table>
			</form>
			<form method="POST" action="" id="accountLoeschen">
				<table>
					<tr><td>Email:</td><td><input type="text" name="email" id="mail"></td></tr>
					<tr><td>Passwort:</td><td><input type="password" name="passwort" id="pw"></td></tr>
					<tr><td colspan="2">Account wirklich löschen: <input type="checkbox" name="check"></td></tr>
					<tr><td colspan="2"><input type="submit" value="Account Löschen" name="accloeschen"></tr>
				</table>
			</form>
		</div>
<!--		<br>
		Wenn du deinen Account löschen möchtest <a href="../loeschen.php">Hier</a> klicken <br>
		Du wirst weitergeleitet und musst deine Daten betsätigen.-->
	</div>
<script>
	$("#editor > form").hide(0);
	$(".listenelement").click(function(){
		$("#editor > .label").text($(this).text());
		$("#editor > form").hide(0);
		$("#editor").show(0);
		var element = $(this).data("aufruf");
		$(element).show(0);
	});
</script>
<?=$nrt->genJsCode();?>
</body>
</html>