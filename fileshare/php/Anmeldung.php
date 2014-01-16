<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<link href="../css/style.css" rel="stylesheet" type="text/css" />
	<script src="../js/jsUtilities.js" type="text/javascript"></script>
	<title>Anmeldeseite</title>
</head>
<body>
<?php
	session_start();
?>
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
						<?php 
						if (isset($_SESSION["merken"])) {
							$merken = $_SESSION["merken"];
							echo "<tr><td class=\"rightAlign\">E-Mail:</td><td><input type=\"text\" name=\"eanmeld\" id=\"bnanmeld\" value='$merken' required></td></tr>";
							}
						else {
							echo "<tr><td class=\"rightAlign\">E-Mail:</td><td><input type=\"text\" name=\"eanmeld\" id=\"bnanmeld\" required></td></tr>";
							}
							
						?>
						<tr><td class="rightAlign">Passwort:</td><td><input type="password" name="pwanmeld" id="pwanmeld" required></td></tr>
						<tr><td colspan="2" class="leftAlign">E-Mail merken <input type="checkbox" name="merken" id="merken"></td></tr>
						<tr><td></td><td><input type="submit" value="login"></td></tr>
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
include "../php/utilities.php";
include_once "../securimage/securimage.php";

debugModus();

$data = $_POST;
$nrt = new Nachrichten("fehlerListe");
$securimage = new Securimage();
if (alleSchluesselGesetzt($data, "eanmeld", "pwanmeld")) {
	$emaila = strtolower($_POST["eanmeld"]);
	$pwa = ($_POST["pwanmeld"]);
	$db = oeffneBenutzerDB($nrt);
	$pwTest = benutzerPwTest($db, $emaila, $pwa);
	if ($pwTest == WRONG_EMAIL) {
		$nrt->fehler("Falsche Email");
	}
	if ($pwTest == WRONG_COMBINATION) {
		$nrt->fehler("Falsches Passwort");
	}
	if ($pwTest == PASSWORD_PASS) {
		$nrt->okay("Anmeldung erfolgreich");
		if (isset($_POST["eanmeld"]) && ($_POST["pwanmeld"])) {
			$_SESSION["semail"] = ($_POST["eanmeld"]);
			$_SESSION["spw"]	= ($_POST["pwanmeld"]);
		}
	}
}
if (isset($_POST["merken"])) {
	$_SESSION["merken"] = ($_POST["eanmeld"]);
	}
else {
	unset($_SESSION['merken']);
	}
// Debug 
echo $_SESSION["semail"];
echo "<br>" . $_SESSION["spw"];
?>
<script src="../js/pruefeRegistrierung.js"></script>
<?php $nrt->genJsCode(); ?>
</body>
</html>