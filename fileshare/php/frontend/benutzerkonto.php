<!DOCTYPE HTML>
<?php
include_once "../utilities.php";
debugModus();
require_once(rootdir."fileshare/php/generate.php");
require_once(rootdir."fileshare/php/frontend/frontendUtilities.php"); // Definiert auch $frontendMenu
require_once(rootdir."fileshare/php/frontend/Menu.php");
require_once(rootdir."fileshare/php/websiteFunktionen/loeschung.php");
require_once(rootdir."fileshare/php/websiteFunktionen/emailaenderung.php");

session_start();
leiteUmWennNichtAngemeldet();
$data = $_POST;
$menu = new Menu($frontendMenu, "konto", "../../");
$nrt = new Nachrichten("#fehlerListe", "../../");

if (isset($data["accloeschen"])) {
	if (alleSchluesselGesetzt($data, "mail", "pw", "check")) {
		verarbeiteLoeschung($nrt, $data["mail"], $data["pw"]);
		session_destroy();
		umleitenZuAnmeldung();
	}
	else { echo "Geht Nicht Penner"; }
}

if (isset($data["emaila"])) {
	if (alleSchluesselGesetzt($data, "mailae", "pwae", "nmailea")) {
		verarbeiteEmailaenderung($nrt, $data["maila"], $data["pw"], $data["nmailea"]);
		session_destroy();
		umleitenZuAnmeldung(); 
	}
	else { echo "Geht Nicht Penner"; }
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
</head>
<body>
<?=generateHeaderBannerLogout()?>
<div id="contentWrapper">
	<div id="menu">
		<?=$menu->toHTML()?>
	</div>
	<div id="panel">
		<h1>Benutzerkontenverwaltung</h1>
		<div class="liste" id="verwaltungsliste">
			<div class="label">Aktion wählen:</div>
			<div class="listenelement" data-aufruf="#nameAendern">Nutzername ändern</div>
			<div class="listenelement" data-aufruf="#emailAendern">Email ändern</div>
			<div class="listenelement" data-aufruf="#passwortAendern">Passwort ändern</div>
			<div class="listenelement" data-aufruf="#accountLoeschen">Account Löschen</div>
		</div>
		<div id="editor">
			<div class="label">Einstellungen</div>
			<div id="fehlerListe"></div>
			<form method="POST" action="" id="nameAendern">
				<table>
					<tr><td>Email:</td><td><input type="text" name="mail" id="mail"></td></tr>
					<tr><td>Aktuelles Passwort:</td><td><input type="password" name="pwa" id="pwa"></td></tr>
					<tr><td>Neuer Nutzername:</td><td><input type="password" name="pwn" id="pwn"><td></tr>
					<tr><td colspan="2"><input type="submit" value="Nutzername ändern" name="namea"></td></tr>
				</table>
			</form>
			<form method="POST" action="" id="emailAendern">
				<table>
				    <tr><td>Email:</td><td><input type="text" name="mailae" id="mail"></td></tr>
				    <tr><td>Neue Email:</td><td><input type="password" name="pwae" id="pwa"></td></tr>
				    <tr><td colspan="2"><input type="submit" value="Email ändern" name="emaila"></td></tr>
				</table>
			</form>
			<form method="POST" action="" id="passwortAendern">
				<table>
					<tr><td>Email:</td><td><input type="text" name="mail" id="mail"></td></tr>
					<tr><td>Aktuelles Passowort:</td><td><input type="password" name="pwa" id="pwa"></tr></td>
					<tr><td>Neues Passwort:</td><td><input type="password" name="pwn" id="pwn"></td></tr>
					<tr><td>Passwort wiederholen:</td><td><input type="password" name="pwn2" id="pwn2"></td></tr> 
					<tr><td colspan="2"><input type="submit" value="Passwort ändern" name="passworta"></td></tr>
				</table>
			</form>
			<form method="POST" action="" id="accountLoeschen">
				<table>
					<tr><td>Email:</td><td><input type="text" name="mail" id="mail"></td></tr>
					<tr><td>Passwort:</td><td><input type="password" name="pw" id="pw"></td></tr>
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
</body>
</html>