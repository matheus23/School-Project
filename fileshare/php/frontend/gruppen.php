<!DOCTYPE HTML>
<?php
include_once "../utilities.php";
debugModus();
include_once "../generate.php";
include_once "frontendUtilities.php";
include_once "Menu.php";

session_start();
leiteUmWennNichtAngemeldet();

$nrt = new Nachrichten("#fehlerListeGruppe","../../");
$db = oeffneBenutzerDB($nrt);
$menu = new Menu($frontendMenu, "gruppen", "../../");
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
<?=generateHeaderBannerLogout();?>
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
			</div>
			<div id="editor">
				<div class="label">Gruppeneditor:</div>
				<div class="bottommargin">Gruppenname:<input type='text' id="gruppenname" name="gruppenname"/></div>
				<div class="liste" id="mitgliederliste">
					<div class="label">Mitglieder:</div>
					<p> Neues Mitglied (Benutzername oder Email): <input type='text' id="nameemail"/> <span id="neuesmitglied" class="hinzufuegen"></span>
					</p>
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
<?=(new CSRFSchutz())->neu()->genJS()?>
<script src="../../js/gruppen.js"></script>
<?php $nrt->genJsCode(); ?>
</html>