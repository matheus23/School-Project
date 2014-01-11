<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<link href="../css/style.css"  rel="stylesheet" type="text/css"/>
	<script src="../js/jsUtilities.js" type="text/javascript"></script>
	<title>Email bestätigen</title>
</head>
<body>
<div id='header'>
	<i><h1 id='banner'>Fileshare</h1></i>
</div>
<table width="100%" height="100%">
<tr>
	<td colspan="2">
		<table align="center">
		<tr><td align="center"><h1>E-mail bestätigen</h1></td></tr>
		<tr>
			<td>
				<!-- Falls fehler auftreten, werden hier <div>'s erzeugt, die fehlernachrichten beinhalten: -->
				<div id="fehlerListe">
					<!-- Dieses <div> wird nur angezeigt, sofern das <script> danach es nicht sofort wieder löscht,
						das bedeutet soviel wie: Nur wenn javascript nicht aktiviert ist. -->
					<div id="jsenable" class='infobox warnung'>
						<table border="0"><tr><td class="verticalMid">
							<img src='../img/warning.png' width='16' height='16' />
						</td><td class="verticalMid">
							Javascript muss aktiviert sein!
						</td></tr></table>
					</div>
					<script type="text/javascript">
						document.getElementById("jsenable").style.display = 'none';
					</script>
				</div>
			</td>
		</tr>
		<tr>
		<td>
		</td>
	</tr>
	<tr>
	<td class='bottom_fix_right'>
		Zurück zur <a href="Anmeldung.php">Startseite
	</td>
</tr>
</table>
<?php
include "../php/utilities.php";

debugModus();
$data = $_GET;
$nrt = new Nachrichten("fehlerListe");
if (alleSchluesselGesetzt($data, "nutzerID")){
	$db = oeffneBenutzerDB($nrt);
	tabelleNeueSpalte($db, "Benutzer", "Bestaetigt","BOOL");//MUSS SPÄTER ENTFERNT WERDEN ### NUR ZUR TABELLEN-MIGRATION
	$nutzerID=$db->real_escape_string($data["nutzerID"]);
	require_once("registrierungsEmail.php");
	pruefeRegistrierungsEmail($nutzerID,$db,$nrt);
}
else{
	$nrt->fehler("Keine nutzerID in der URL. Sicher, dass du auf der richtigen Seite bist?");
}
?>
<?php $nrt->genJsCode(); ?>
</body>
</html>