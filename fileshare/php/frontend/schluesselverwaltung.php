<!DOCTYPE HTML>
<?php
include_once "../utilities.php";
debugModus();
require_once(rootdir."fileshare/php/generate.php");
require_once(rootdir."fileshare/php/frontend/frontendUtilities.php");
require_once(rootdir."fileshare/php/frontend/Menu.php");

session_start();
leiteUmWennNichtAngemeldet();
$menu = new Menu($frontendMenu, "schluesselverwaltung", "../../");
?>
<html>
<head>
	<meta charset="utf-8">
	<title>Schluesselverwaltung</title>
	<link type="text/css" rel="stylesheet" href="../../css/style.css">
	<link type="text/css" rel="stylesheet" href="../../css/frontendStyle.css">
	<script src="../../js/jquery-1.10.2.min.js"></script>
	<script src="../../js/ajaxFehler.js" type="text/javascript"></script>
	<script src="../../js/forge/forge.bundle.js"></script>
	<script src="../../js/frontend.js"></script>
    <script src="../../js/jsUtilities.js" type="text/javascript"></script>
</head>
<body>
<?=generateHeaderBannerLogout();?>
<div id="contentWrapper">
	<div id="menu">
		<?=$menu->toHTML()?>
	</div>
	<div id="panel">
		<h1>Schlüsselverwaltung</h1>
		<div class="liste" id="verwaltungsliste">
			<div class="label">Aktion wählen:</div>
			<div class="listenelement" id="dateischluesselButton">Dateischlüssel</div>
			<div class="listenelement" id="signaturschluesselButton">Signaturschlüssel</div>
		</div>
		<div id="editor">
			<div class="label">Einstellungen</div>
            <div id="fehlerListe"></div>
			<div id="dateischluessel" class="fenster">
            	<p>Dateischlüssel bestehen aus zwei "Hälften". Eine Schlüsselhälfte wird zum Server geschickt. Erstelle einen neuen Schlüssel nur gelegentlich. Du kannst immer nur einen aktiven Schlüssel haben. Die anderen Schlüssel bleiben erhalten bis du sie manuell löschst.</p>
                <p>Bei jedem Entschlüsseln musst du das Passwort des Dateischlüssels angeben.</p>
                <table class="passwortEingabe">
                	<tr><td class="rightAlign">Passwort:</td><td><input type="password" id="dateischluesselPasswort"/></td></tr>
	                <tr><td class="rightAlign">Wiederholung:</td><td><input type="password" id="dateischluesselPasswortWdh"/></td></tr>
	                <tr><td></td><td><input type="button" value="Schlüssel generieren" id="dateischluesselGenerieren"/></td></tr>
                </table>
                <div class="schluesselFortschritt"></div>
                <div class="infobox warnung">
            		<table border="0">
            			<tr><td><img src="../../img/warning.png" width="16" height="16">
            			</td><td>
            				Dein Passwort muss 8-64 Zeichen lang sein, Groß- und Kleinschreibung beinhalten sowie zwei Sonderzeichen oder Ziffern beinhalten
            			</td></tr>
            		</table>
            	</div>
                <p>Aktueller Schlüssel: <span id="aktuellerDateischluessel"></span></p>
                <p>Alte Schlüssel - Du erhälst keine weiteren Dateien, die mit diesen Schlüsseln entschlüsselt werden können. Noch vorhandene, kannst du allerdings weiterhin entschlüsseln:</p>
            	<div class="liste nofloat" id="dateischluesselListe"></div>
            </div>
            <div id="signaturschluessel" class="fenster">
            	<p>Signaturschlüssel verifizieren dich als den Absender einer Datei. Folglich werden sie beim Senden benötigt.</p>
                <p>Bei jedem Verschlüsseln musst du das Passwort des Dateischlüssels angeben.</p>
                <table class="passwortEingabe">
	                <tr><td class="rightAlign">Passwort:</td><td><input type="password" id="signaturschluesselPasswort"/></td></tr>
	                <tr><td class="rightAlign">Wiederholung:</td><td><input type="password" id="signaturschluesselPasswortWdh"/></td></tr>
	                <tr><td></td><td><input type="button" value="Schlüssel generieren" id="signaturschluesselGenerieren"/></td></tr>
                </table>
                <div class="schluesselFortschritt"></div>
                <div class="infobox warnung">
            		<table border="0">
            			<tr><td><img src="../../img/warning.png" width="16" height="16">
            			</td><td>
            				Dein Passwort muss 8-64 Zeichen lang sein, Groß- und Kleinschreibung beinhalten sowie zwei Sonderzeichen oder Ziffern beinhalten
            			</td></tr>
            		</table>
            	</div>
                <p>Aktueller Schlüssel: <span id="aktuellerSignaturschluessel"></span></p>
                <p>Achtung: Du kannst immer nur einen Signaturschlüssel benutzen, wenn du diesen neu generierst, kann deine Identität bei bereits Verschlüsselten Dateien nicht mehr verifiziert werden.</p>
            </div>
		</div>
<!--		<br>
		Wenn du deinen Account löschen möchtest <a href="../loeschen.php">Hier</a> klicken <br>
		Du wirst weitergeleitet und musst deine Daten betsätigen.-->
	</div>
<?=(new CSRFSchutz())->neu()->genJS()?>
<?=aktuelleNutzerIDJavaScript()?>
<script src="../../js/schluessel.js"></script>
</body>
</html>