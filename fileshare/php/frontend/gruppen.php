<!DOCTYPE HTML>
<?php
include_once "../utilities.php";
include_once "../generate.php";
include_once "frontendUtilities.php";

$nrt = new Nachrichten("fehlerListeGruppe","../../");
$db = oeffneBenutzerDB($nrt);

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
$menu->add(new Menupunkt("upload","Upload","upload.php"));
$menu->add(new Menupunkt("gruppen","Gruppen","gruppen.php",true));
$menu->add(new Menupunkt("konto","Benutzerkonto","benutzerkonto.php"));
?>
<html>
<head>
	<meta charset="utf-8">
	<title>Gruppen</title>
	<link type="text/css" rel="stylesheet" href="../../css/style.css">
	<link type="text/css" rel="stylesheet" href="../../css/frontendStyle.css">
	<script src="../../js/jquery-1.10.2.min.js"></script>
	<script src="../../js/frontend.js"></script>
	<script src="../../js/jsUtilities.js" type="text/javascript"></script>
</head>
<body>
<?=generateHeader(generateBanner()."<a href='../abmelden.php' id='abmelden'>abmelden</a>");?>
<div id="contentWrapper">
	<div id="menu">
		<?=$menu->toHTML()?>
	</div>
	<div id="panel">
		<div id="uiWrapper">
			<h1>Gruppen</h1>
			<div class="liste" id="gruppenliste">
				<div class="label">Gruppenliste:<div id="neuegruppe" class="hinzufuegen rightfloat"></div></div>
				<div id="fehlerListeGruppe"></div>
				<?=generateHTMLGruppen($db)?>
			</div>
			<div id="gruppeneditor">
				<div class="label">Gruppeneditor:</div>
				<div class="bottommargin">Gruppenname:<input type='text' id="gruppenname" name="gruppenname"/></div>
				<div class="liste" id="mitgliederliste">
					<div class="label">Mitglieder:</div>
					<div>Neues Mitglied (Benutzername oder Email):<input type='text' id="nameemail"/><div id="neuesmitglied" class="hinzufuegen"></div></div>
					<div id="fehlerListe"></div>
					<div id="hinzufuegenAuswahl">
						<p>Welchen Nutzer meinst du?</p>
						<div class="liste nofloat" id="auswahlliste"></div>
					</div>
					<p>Mitglieder:</p>
				</div>
				<input id="editFertig" type="button" value="Fertig"/>
			</div>
		</div>
	</div>
</div>
</body>
<script src="../../js/gruppen.js"></script>
<?php $nrt->genJsCode(); ?>
</html>