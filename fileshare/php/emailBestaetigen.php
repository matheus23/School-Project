<!DOCTYPE html>
<?php
include_once "utilities.php";
debugModus();
require_once(rootdir."fileshare/php/benutzerEmail.php");
require_once(rootdir."fileshare/php/generate.php");

$data = $_GET;
$nrt = new Nachrichten("#fehlerListe");
if (alleSchluesselGesetzt($data, "regID")){
	$db = oeffneBenutzerDB($nrt);
	$regID = $db->real_escape_string($data["regID"]);
	pruefeRegistrierungsEmail($regID, $db, $nrt);
} else {
	$nrt->fehler("Keine regID in der URL. Sicher, dass du auf der richtigen Seite bist?");
}
?>
<html>
<head>
	<meta charset="UTF-8" />
	<link href="../css/style.css"  rel="stylesheet" type="text/css"/>
	<script src="../js/jquery-1.10.2.min.js"></script>
	<script src="../js/jsUtilities.js" type="text/javascript"></script>
	<title>Email bestätigt</title>
</head>
<body>
<?=generateHeaderBanner();?>
<table width="100%" height="100%">
<tr>
	<td colspan="2">
		<table align="center">
		<tr><td align="center"><h1>E-mail bestätigt</h1></td></tr>
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
	<td style="font-size: 2em">
		Zurück zur <a href="Anmeldung.php">Anmeldung</a>
	</td>
</tr>
</table>
<?php $nrt->genJsCode(); ?>
</body>
</html>