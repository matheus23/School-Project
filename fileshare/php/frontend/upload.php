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
$menu->add(new Menupunkt("download","Download","download.php"));
$menu->add(new Menupunkt("upload","Upload","upload.php",true));
$menu->add(new Menupunkt("gruppen","Gruppen","gruppen.php"));
$menu->add(new Menupunkt("konto","Benutzerkonto","benutzerkonto.php"));
$menu->add(new Menupunkt("schluesselverwaltung","Schlüsselverwaltung","schluesselverwaltung.php"));
?>
<html>
<head>
	<meta charset="utf-8">
	<title>Upload</title>
	<link type="text/css" rel="stylesheet" href="../../css/style.css">
	<link type="text/css" rel="stylesheet" href="../../css/frontendStyle.css">
	<script type="text/javascript" src="../../js/jquery-1.10.2.min.js"></script>
	<script src="../../js/frontend.js"></script>
    <script src="../../js/jsUtilities.js"></script>
    <script src="../../js/forge/forge.bundle.js"></script>
</head>
<body>
<?=generateHeader(generateBanner()."<a class='confirm' href='../abmelden.php' id='abmelden'>abmelden</a>");?>
<div id="contentWrapper">
	<div id="menu">
		<?=$menu->toHTML()?>
	</div>
		<div id="panel">
			<h1>Upload</h1>
			<div id="uiWrapper">
				<table width="100%">
					<tr><td>
						<table align="center">
							<form method="post" enctype="multipart/form-data">
								<tr><td align="center"><h4>Datei für Upload auswählen<h4></td></tr>
                                <tr><td align="center"><div id="fehlerListe"></div></td></tr>
                                <tr><td align="center">Signieren mit: <span id="aktuellerSignaturschluessel"></span></td></tr>
                                <tr><td align="center">Passwort für den Signaturschlüssel:<input type="password" id="signaturschluesselPasswort"></td></tr>
								<tr><td align="center"><input type="file" id="file"></td></tr>
                                <tr><td align="center">Email des Empfängers:<input type="text" id="emailEmpfaenger"></td></tr>
								<tr><td align="center"><br><input type="button" value="Hochladen" id="hochladen"></td></tr>
							</form>
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
<script src="../../js/upload.js"></script>
</body>
</html>