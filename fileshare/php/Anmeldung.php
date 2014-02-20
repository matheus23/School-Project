<?php
	session_start();
	include_once dirname(__FILE__) . "/utilities.php";
	debugModus();
	require_once(rootdir."fileshare/php/frontend/frontendUtilities.php");
	require_once(rootdir."fileshare/php/generate.php");
	require_once(rootdir."fileshare/php/websiteFunktionen/anmeldung.php");
	
	function zumDashboard() {
		// Umleitung auf Dashboard:
		header("Location: http://".host.dirname($_SERVER["REQUEST_URI"])."/frontend/dashboard.php");
	}
	
	if (istAngemeldet()){
		zumDashboard();
	}
	
	$data = $_POST;
	$nrt = new Nachrichten("#fehlerListe");

	if (alleSchluesselGesetzt($data, "eanmeld", "pwanmeld")) {
		$anmeldungErfolgreich = verarbeiteAnmeldung($nrt, $data["eanmeld"], isset($data["merken"]), $data["pwanmeld"]);
		if ($anmeldungErfolgreich) {
			zumDashboard();
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<link href="../css/style.css" rel="stylesheet" type="text/css" />
	<script src="../js/jsUtilities.js" type="text/javascript"></script>
	<script src="../js/jquery-1.10.2.min.js"></script>
	<title>Anmeldeseite</title>
</head>
<body>
<?=generateHeaderBanner()?>
<table width="100%" height="95%">
	<tr><td colspan="2">
		<table align="center">
			<tr><td align="center"><h1>Anmeldung</h1></td></tr>
			<tr>
				<td>
					<form method="post" id="formular" action="">
						<table align="center" valign="middle">
							<tr>
								<td class="rightAlign">E-Mail:</td>
								<td><input type="text" name="eanmeld" id="bnanmeld" value="<?=orDefault($_COOKIE,"email","")?>" required /></td>
							</tr><tr>
								<td class="rightAlign">Passwort:</td>
								<td><input type="password" name="pwanmeld" id="pwanmeld" required></td>
							</tr><tr>
								<td class="rightAlign">Email merken</td>
								<td><input type="checkbox" name="merken" id="merken"></td>
							</tr><tr>
								<td></td>
								<td><input type="submit" value="Anmelden"></td>
							</tr>
						</table>
					</form>
				</td>
			</tr>
			<tr><td>
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
			</td></tr>
		</table>
	</tr></td>
</table>
<div class="bottom_fix_right">Passwort vergessen? <a href="pwvergessen.php">Hier</a> klicken</div>
<div class="bottom_fix_left">Noch nicht registriert? <a href="registrierung.php">Hier</a> Registrieren</div>
<?php $nrt->genJsCode(); ?>
</body>
</html>