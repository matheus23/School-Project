<!DOCTYPE HTML>
<?php
include_once "../utilities.php";
debugModus();
require_once(rootdir."fileshare/php/generate.php");
require_once(rootdir."fileshare/php/frontend/frontendUtilities.php"); // Definiert auch $frontendMenu
require_once(rootdir."fileshare/php/frontend/Menu.php");

session_start();
leiteUmWennNichtAngemeldet();
$menu = new Menu($frontendMenu, "upload", "../../");
?>
<html>
<head>
	<meta charset="utf-8">
	<title>Upload</title>
	<link type="text/css" rel="stylesheet" href="../../css/style.css">
	<link type="text/css" rel="stylesheet" href="../../css/frontendStyle.css">
	<script type="text/javascript" src="../../js/jquery-1.10.2.min.js"></script>
	<script src="../../js/ajaxFehler.js" type="text/javascript"></script>
    <script src="../../js/forge/forge.bundle.js"></script>
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
		<h1>Upload</h1>
		<div id="uiWrapper">
			<table width="100%">
				<tr><td>
					<table align="center" id="uploadForm">
						<form method="post" enctype="multipart/form-data">
							<tr><td align="center"><h4>Datei für Upload auswählen<h4></td></tr>
                            <tr><td align="center"><div id="fehlerListe"></div></td></tr>
                            <tr><td align="center">Signieren mit: <span id="aktuellerSignaturschluessel"></span></td></tr>
                            <tr><td align="center">Passwort für den Signaturschlüssel:<input type="password" id="signaturschluesselPasswort"></td></tr>
							<tr><td align="center"><input type="file" id="file"></td></tr>
							<tr><td align="center">Die Dateigröße ist auf 20 Megabyte begrenzt</td></tr>
                            <tr><td align="center">Email des Empfängers:<input type="text" id="emailEmpfaenger"></td></tr>
							<tr><td align="center"><br><input type="button" value="Hochladen" id="hochladen"></td></tr>
						</form>
						<div id="verschluesselnAnzeige" class="anzeigeUpload"></div>
						<div id="signierenAnzeige" class="anzeigeUpload"></div>
						<div id="uploadAnzeige" class="anzeigeUpload"></div>
					</table>
				</td></tr>
			</table>
		</div>
	</div>
</div>
<script>
	$(".confirm").click(function() {
		var abmelden = confirm("Willst du dich wirklich abmelden?!?");
		if (!abmelden){
			return false;
		}
	});
</script>
<?=(new CSRFSchutz())->neu()->genJS()?>
<?=aktuelleNutzerIDJavaScript()?>
<script src="../../js/upload.js"></script>
</body>
</html>