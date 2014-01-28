<!DOCTYPE HTML>
<?php
include "../utilities.php";
include "../generate.php";

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
$menu->add(new Menupunkt("upload","Upload","upload.php",true));
$menu->add(new Menupunkt("gruppen","Gruppen","gruppen.php"));
$menu->add(new Menupunkt("konto","Benutzerkonto","benutzerkonto.php"));
?>
<html>
<head>
	<meta charset="utf-8">
	<title>Benutzerkonto</title>
	<link type="text/css" rel="stylesheet" href="../../css/style.css">
	<link type="text/css" rel="stylesheet" href="../../css/frontendStyle.css">
	<script src="../../js/frontend.js"></script>
</head>
<body>
<?=generateHeader(generateBanner()."<a href='../abmelden.php' id='abmelden'>abmelden</a>");?>
<div id="contentWrapper">
	<div id="menu">
		<?=$menu->toHTML()?>
	</div>
		<div id="panel">
			<h1>Upload</h1>
			<div id="uiWrapper">
				<table width="100%">
					<tr><td>
						<table align="left">
							
							
						</table>
						<table align="center">
							<form enctype="multipart/form-data">
								<tr><td><h4>Datei für Upload auswählen<h4></td></tr>
								<tr><td><input type="file" name="Datei"></td></tr>
								<tr><td align="center"><input type="submit" value="Hochladen"></td></tr>
							</form>
							
						</table>
					</td></tr>
				</table>
			</div>
		</div>
	</div>
<?php
$kb = 1024;
$mb = 1024*$kb;

if ($_FILES['Datei']['size'] > 2*$mb) {
	
}


?>
</body>
</html>