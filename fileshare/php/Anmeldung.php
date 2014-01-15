<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<link href="../css/style.css" rel="stylesheet" type="text/css" />
	<script src="../js/jsUtilities.js" type="text/javascript"></script>
	<title>Anmeldeseite</title>
</head>
<body>
<div id='header'>
	<i><h1 id='banner'>Fileshare</h1></i>
</div>
<table width="100%" height="95%">
<tr>
	<td colspan="2">
		<table align="center">
		<tr><td><h1>Anmeldung</h1></td></tr>
		<tr>
			<td>
				<form method="post" id="formular" action="passworttest.php">
					<table align="center" valign="middle">
						<tr><td class="rightAlign">E-Mail:</td><td><input type="text" name="eanmeld" id="eanmeld" required></td></tr>
						<tr><td class="rightAlign">Passwort:</td><td><input type="password" name="pwanmeld" id="pwanmeld" required></td></tr>
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







<script src="../js/pruefeRegistrierung.js"></script>
<script type="text/javascript"><?=$fehlerjs?></script>
</body>
</html>