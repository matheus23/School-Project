<!DOCTYPE HTML>
<?php
include_once "../utilities.php";
include_once "../generate.php";
include_once "frontendUtilities.php";

session_start();
debugModus();
if((!isset($_SESSION["semail"]))||($_SESSION["semail"]=="")){//Nicht angemeldet
	session_destroy();
	header("Location: http://".host.dirname(dirname($_SERVER["REQUEST_URI"]))."/Anmeldung.php");//Umleitung zur Anmeldung
}

include "Menu.php";
$menu = new Menu();
$menu->add(new Menupunkt("dashboard","Dashboard","dashboard.php"));
$menu->add(new Menupunkt("download","Download","download.php",true));
$menu->add(new Menupunkt("upload","Upload","upload.php"));
$menu->add(new Menupunkt("gruppen","Gruppen","gruppen.php"));
$menu->add(new Menupunkt("konto","Benutzerkonto","benutzerkonto.php"));
$menu->add(new Menupunkt("schluesselverwaltung","Schlüsselverwaltung","schluesselverwaltung.php"));
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
<?=generateHeader(generateBanner()."<a href='../abmelden.php' id='abmelden'>abmelden</a>");?>
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
<script src="../../js/download.js"></script>
</body>
</html>