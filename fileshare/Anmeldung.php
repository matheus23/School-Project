<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="style.css" />
	<script src="jsUtilities.js" type="text/javascript"></script>
	<title>Anmeldeseite</title>
</head>
<body>
<table width="100%" height="95%">
<tr>
	<td colspan="2">
		<table align="center">
		<tr><td><h1>Anmeldung</h1></td></tr>
		<tr>
			<td>
				<table align="center" valign="middle">
					<form method="post" id="formular" action="passworttest.php">
						<tr><td>Benutzername:	</td><td><input type="text" name="bnanmeld" id="bnanmeld" requierd></td></tr>
						<tr><td>Passwort: 		</td><td><input type="text" name="pwanmeld" id="pwanmeld" requierd></td></tr>
						<tr><td colspan="2"><input type="submit" value="login"></td></tr>
					</form>
				</table>
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
						<img src='img/warning.png' width='16' height='16' />
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








<script src="pruefeRegistrierung.js"></script>
<script type="text/javascript"><?=$fehlerjs?></script>
</body>
</html>