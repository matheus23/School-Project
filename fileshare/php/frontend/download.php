<!DOCTYPE HTML>
<?php
include_once "../utilities.php";
debugModus();
include_once "../generate.php";
include_once "frontendUtilities.php"; // Definiert auch $frontendMenu
include_once "Menu.php";

session_start();
leiteUmWennNichtAngemeldet();
$menu = new Menu($frontendMenu, "download", "../../");
?>
<html>
<head>
	<meta charset="utf-8">
	<title>Download</title>
	<link type="text/css" rel="stylesheet" href="../../css/style.css">
	<link type="text/css" rel="stylesheet" href="../../css/frontendStyle.css">
	<script type="text/javascript" src="../../js/jquery-1.10.2.min.js"></script>
	<script src="../../js/frontend.js"></script>
    <script src="../../js/jsUtilities.js"></script>
    <script src="../../js/forge/forge.bundle.js"></script>
    <script src="../../js/fileSaver/FileSaver.js"></script>
</head>
<body>
<?=generateHeaderBannerLogout();?>
<div id="contentWrapper">
	<div id="menu">
		<?=$menu->toHTML()?>
	</div>
	<div id="panel">
		<h1>Download</h1>
        <div id="uiWrapper">
        	<div id="fehlerListe"></div>
            <div class="liste" id="dateiListe"></div>
            <div id="editor">
				<div class="label">Download</div>
                <p>Schlüssel: <span id="schluessel"></span></p>
                Passwort für den Dateischlüssel:<input type="password" id="dateischluesselPasswort">
                <input type="button" value="Herunterladen" id="runterladen">
            </div>
        </div>
	</div>
<?=(new CSRFSchutz())->neu()->genJS()?>
<?=aktuelleNutzerIDJavaScript()?>
<script src="../../js/download.js"></script>
</body>
</html>