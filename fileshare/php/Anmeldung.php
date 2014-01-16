<!DOCTYPE html>
<?php
	include "../php/utilities.php";
	include_once "../securimage/securimage.php";
	
	session_start();
?>
<html>
<head>
	<meta charset="UTF-8" />
	<link href="../css/style.css" rel="stylesheet" type="text/css" />
	<script src="../js/jsUtilities.js" type="text/javascript"></script>
	<title>Anmeldeseite</title>
</head>
<body>
<div id='header'>
	<i><h1 id='banner'>Secureshare</h1></i>
</div>
<table width="100%" height="95%">
<tr>
	<td colspan="2">
		<table align="center">
		<tr><td><h1>Anmeldung</h1></td></tr>
		<tr>
			<td>
				<form method="post" id="formular" action="">
					<table align="center" valign="middle">
						<tr>
							<td class="rightAlign">E-Mail:</td>
							<td><input type="text" name="eanmeld" id="bnanmeld" value="<?php echo orDefault($_SESSION, "gemerkteEmail", ""); ?>" required /></td>
						</tr><tr>
							<td class="rightAlign">Passwort:</td>
							<td><input type="password" name="pwanmeld" id="pwanmeld" required></td>
						</tr><tr>
							<td class="rightAlign">E-Mail merken:</td>
							<td><input type="checkbox" name="merken" id="merken" <?php echo isset($_SESSION["gemerkteEmail"]) ? "checked" : ""; ?>></td>
						</tr><tr>
							<td></td>
							<td><input type="submit" value="Anmelden"></td>
						</tr>
					</table>
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
<div class="bottom_fix_right">Passwort vergessen? <a href="pwvergessen.php">Hier</a> klicken</div>
<div class="bottom_fix_left">Noch nicht registriert? <a href="registrierung.php">Hier</a> Registrieren</div>

<?php
debugModus();

$data = $_POST;
$nrt = new Nachrichten("fehlerListe");
$securimage = new Securimage();

if (alleSchluesselGesetzt($data, "eanmeld", "pwanmeld", "merken")) {
	$emaila = strtolower($data["eanmeld"]);
	$pwa = ($data["pwanmeld"]);
	$db = oeffneBenutzerDB($nrt);
	$pwTest = benutzerPwTest($db, $emaila, $pwa);
	if ($pwTest == WRONG_EMAIL) {
		$nrt->fehler("Falsche Email");
	} elseif ($pwTest == WRONG_COMBINATION) {
		$nrt->fehler("Falsches Passwort");
	} elseif ($pwTest == PASSWORD_PASS) {
		$nrt->okay("Anmeldung erfolgreich");
		if (alleSchluesselGesetzt($data, "eanmeld", "pwanmeld")) {
			$_SESSION["semail"] = ($data["eanmeld"]);
			$_SESSION["spw"]	= ($data["pwanmeld"]);
			if (isset($data["merken"])) {
				$_SESSION["gemerkteEmail"] = $data["eanmeld"];
			} else {
				unset($_SESSION["gemerkteEmail"]);
			}
		}
	}
}
?>
<script src="../js/pruefeRegistrierung.js"></script>
<?php $nrt->genJsCode(); ?>
</body>
</html>