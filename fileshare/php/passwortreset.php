<!DOCTYPE html>
<?php
	include "generate.php";
	include_once "utilities.php";
?>
<html>
<head>
	<meta charset="UTF-8" />
	<link href="../css/style.css"  rel="stylesheet" type="text/css"/>
	<script src="../js/jquery-1.10.2.min.js"></script>
	<script src="../js/jsUtilities.js" type="text/javascript"></script>
	<title>Passwort zurücksetzen</title>
</head>
<body>
<?=generateHeaderBanner()?>
<table width="100%" height="95%">
<tr>
	<td colspan="2">
		<table align="center">
		<tr><td align="center"><h1>Passwort zurücksetzen</h1></td></tr>
		<tr>
			<td>
				<form method="post" id="formular">
					<table align="center" valign="middle">					
						<tr><td class="rightAlign">Neues Passwort:</td><td><input type="password" name="NPw" id="NPw" required></td></tr>
						<tr><td class="rightAlign">Neues Passwort bestätigen:</td><td><input type="password" name="NPwb" id="NPwb" required></td></tr>
						<tr><td colspan="2" align="right"><input type="submit" value="Passwort ändern" id="button"></td></tr>
					</table>
					<input type="hidden" name="resetID" value="<?=orDefault($_GET, "resetID", "")?>">
				</form>
			</td>
		</tr>
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
		</table>
		<br>
	</td>
</tr>
</table>
<?php
debugModus();
$data = $_POST;
$nrt = new Nachrichten("#fehlerListe");
if (alleSchluesselGesetzt($data, "resetID", "NPw", "NPwb")){
	$db = oeffneBenutzerDB($nrt);
	$resetID=$db->real_escape_string($data["resetID"]);
	$NPw=$data["NPw"];
	$NPwb=$data["NPwb"];
	if ($NPw != $NPwb) {
		$nrt->fehler("Das Passwort stimmt nicht mit der Wiederholung überein.");
	}
	else{
		require_once("benutzerEmail.php");
		resetPasswortEmail($resetID,passwordHash($NPw),$db,$nrt);
	}
}
?>
<script src="../js/pruefeAenderung.js"></script>
<?php $nrt->genJsCode(); ?>
</body>
</html>